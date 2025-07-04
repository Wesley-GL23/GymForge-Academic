<?php
/**
 * Script de Atualização Automática do Sistema de Têmpera
 * Este script deve ser executado periodicamente (a cada hora) via cron
 * 
 * Responsável por:
 * - Aplicar resfriamento aos personagens ativos
 * - Atualizar níveis de têmpera
 * - Distribuir recompensas
 * - Manter logs detalhados
 */

require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/../config/forge_tempering.php';
require_once __DIR__ . '/../config/forge_events.php';

// Configurações
define('LOG_FILE', __DIR__ . '/../logs/tempering_update.log');
define('LOCK_FILE', __DIR__ . '/update_tempering.lock');

// Classe para gerenciamento de logs
class Logger {
    private $logFile;
    
    public function __construct($logFile) {
        $this->logFile = $logFile;
        if (!file_exists(dirname($logFile))) {
            mkdir(dirname($logFile), 0755, true);
        }
    }
    
    public function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp][$level] $message\n";
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
        
        if ($level === 'ERROR') {
            error_log($message);
        }
    }
}

// Classe para gerenciamento de lock
class LockManager {
    private $lockFile;
    private $logger;
    
    public function __construct($lockFile, Logger $logger) {
        $this->lockFile = $lockFile;
        $this->logger = $logger;
    }
    
    public function acquireLock() {
        if (file_exists($this->lockFile)) {
            $lockTime = filectime($this->lockFile);
            $currentTime = time();
            
            // Se o lock existir por mais de 1 hora, consideramos que é um lock morto
            if ($currentTime - $lockTime > 3600) {
                $this->logger->log("Lock file exists but is stale. Removing...", "WARN");
                unlink($this->lockFile);
            } else {
                throw new Exception("Another instance is already running");
            }
        }
        
        file_put_contents($this->lockFile, getmypid());
        return true;
    }
    
    public function releaseLock() {
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
    }
}

try {
    // Inicializar logger e lock manager
    $logger = new Logger(LOG_FILE);
    $lockManager = new LockManager(LOCK_FILE, $logger);
    
    $logger->log("Iniciando atualização do sistema de têmpera...");
    
    // Tentar adquirir lock
    if (!$lockManager->acquireLock()) {
        $logger->log("Não foi possível adquirir lock. Abortando...", "ERROR");
        exit(1);
    }
    
    // Registrar início no banco
    $stmt = $conn->prepare("
        INSERT INTO system_jobs (job_type, start_time, status)
        VALUES ('tempering_update', NOW(), 'running')
    ");
    $stmt->execute();
    $jobId = $conn->lastInsertId();
    
    try {
        $conn->beginTransaction();
        
        // Pegar todos os personagens ativos com têmpera
        $stmt = $conn->prepare("
            SELECT DISTINCT 
                fc.id, 
                fc.user_id,
                mt.heat_points,
                mt.tempering_level,
                mt.last_exercise_date,
                mt.consecutive_days
            FROM forge_characters fc
            JOIN muscle_tempering mt ON fc.id = mt.character_id
            WHERE mt.last_exercise_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY mt.heat_points DESC
        ");
        $stmt->execute();
        $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats = [
            'total_processed' => 0,
            'total_updated' => 0,
            'errors' => 0,
            'rewards_given' => 0
        ];
        
        foreach ($characters as $character) {
            try {
                $logger->log("Processando personagem ID: {$character['id']}");
                
                // Aplicar resfriamento
                $result = $temperingManager->applyCooldown($character['id']);
                $stats['total_processed']++;
                
                if (is_array($result) && isset($result['updated']) && $result['updated']) {
                    $stats['total_updated']++;
                    
                    // Registrar atualização
                    $stmt = $conn->prepare("
                        INSERT INTO tempering_history 
                        (character_id, action_type, old_value, new_value, timestamp)
                        VALUES (?, 'cooldown', ?, ?, NOW())
                    ");
                    $stmt->execute([
                        $character['id'],
                        $result['old_heat'],
                        $result['new_heat']
                    ]);
                    
                    // Verificar e distribuir recompensas
                    if (isset($result['rewards']) && is_array($result['rewards'])) {
                        foreach ($result['rewards'] as $reward) {
                            $stmt = $conn->prepare("
                                INSERT INTO character_rewards 
                                (character_id, reward_type, amount, reason)
                                VALUES (?, ?, ?, ?)
                            ");
                            $stmt->execute([
                                $character['id'],
                                $reward['type'],
                                $reward['amount'],
                                $reward['reason']
                            ]);
                            $stats['rewards_given']++;
                        }
                    }
                }
                
            } catch (Exception $e) {
                $stats['errors']++;
                $logger->log(
                    "Erro ao processar personagem {$character['id']}: " . $e->getMessage(),
                    "ERROR"
                );
                
                // Registrar erro
                $stmt = $conn->prepare("
                    INSERT INTO system_errors 
                    (job_id, error_type, error_message, character_id)
                    VALUES (?, 'tempering_update', ?, ?)
                ");
                $stmt->execute([$jobId, $e->getMessage(), $character['id']]);
                
                continue;
            }
        }
        
        // Atualizar eventos ativos
        $eventManager->updateActiveEvents();
        
        $conn->commit();
        
        // Atualizar status do job
        $stmt = $conn->prepare("
            UPDATE system_jobs 
            SET 
                end_time = NOW(),
                status = 'completed',
                stats = ?
            WHERE id = ?
        ");
        $stmt->execute([json_encode($stats), $jobId]);
        
        $logger->log("Atualização concluída com sucesso!");
        $logger->log("Estatísticas: " . json_encode($stats, JSON_PRETTY_PRINT));
        
    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    $logger->log("ERRO CRÍTICO: " . $e->getMessage(), "ERROR");
    
    // Atualizar status do job se existir
    if (isset($jobId)) {
        $stmt = $conn->prepare("
            UPDATE system_jobs 
            SET 
                end_time = NOW(),
                status = 'failed',
                error_message = ?
            WHERE id = ?
        ");
        $stmt->execute([$e->getMessage(), $jobId]);
    }
    
    exit(1);
    
} finally {
    // Sempre liberar o lock
    if (isset($lockManager)) {
        $lockManager->releaseLock();
    }
}