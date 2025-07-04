<?php
// Configurações do Sistema
define('SYSTEM_NAME', 'GymForge');
define('SYSTEM_VERSION', '1.0.0');
define('SYSTEM_DESCRIPTION', 'Sistema gamificado de academia');

// Configurações de Gamificação
define('XP_POR_TREINO', 100);
define('XP_POR_EXERCICIO', 10);
define('XP_POR_SERIE', 5);
define('BONUS_STREAK_DIARIA', 50);

// Níveis e Ranks
$RANKS = [
    'novice_forger' => ['nome' => 'Forjador Novato', 'min_level' => 1],
    'apprentice_forger' => ['nome' => 'Aprendiz de Forjador', 'min_level' => 5],
    'journeyman_forger' => ['nome' => 'Forjador Jornaleiro', 'min_level' => 10],
    'expert_forger' => ['nome' => 'Forjador Experiente', 'min_level' => 20],
    'master_forger' => ['nome' => 'Mestre Forjador', 'min_level' => 30],
    'grandmaster_forger' => ['nome' => 'Grão-Mestre Forjador', 'min_level' => 40],
    'legendary_forger' => ['nome' => 'Forjador Lendário', 'min_level' => 50]
];

// Estágios de Têmpera
$TEMPERING_STAGES = [
    'bronze_cold' => ['nome' => 'Bronze Frio', 'cor' => '#CD7F32'],
    'bronze_hot' => ['nome' => 'Bronze Quente', 'cor' => '#FF8C00'],
    'iron_cold' => ['nome' => 'Ferro Frio', 'cor' => '#708090'],
    'iron_hot' => ['nome' => 'Ferro Quente', 'cor' => '#FF4500'],
    'steel_cold' => ['nome' => 'Aço Frio', 'cor' => '#4682B4'],
    'steel_hot' => ['nome' => 'Aço Quente', 'cor' => '#1E90FF'],
    'mithril_cold' => ['nome' => 'Mithril Frio', 'cor' => '#B0C4DE'],
    'mithril_hot' => ['nome' => 'Mithril Quente', 'cor' => '#87CEEB']
];

// Grupos Musculares
$MUSCLE_GROUPS = [
    'peito' => 'Peito',
    'costas' => 'Costas',
    'ombros' => 'Ombros',
    'biceps' => 'Bíceps',
    'triceps' => 'Tríceps',
    'pernas' => 'Pernas',
    'abdomen' => 'Abdômen',
    'gluteos' => 'Glúteos'
];

// Níveis de Dificuldade
$DIFFICULTY_LEVELS = [
    'iniciante' => ['nome' => 'Iniciante', 'cor' => '#28a745'],
    'intermediario' => ['nome' => 'Intermediário', 'cor' => '#ffc107'],
    'avancado' => ['nome' => 'Avançado', 'cor' => '#dc3545']
];

// Cargos de Guilda
$GUILD_ROLES = [
    'leader' => ['nome' => 'Líder', 'poder' => 3],
    'officer' => ['nome' => 'Oficial', 'poder' => 2],
    'member' => ['nome' => 'Membro', 'poder' => 1]
];

// Configurações de Recompensas
$REWARDS = [
    'daily_login' => ['xp' => 50, 'bonus_streak' => 10],
    'complete_workout' => ['xp' => 100, 'bonus_perfect' => 50],
    'guild_contribution' => ['xp' => 25, 'points' => 10]
];

// Configurações do Sistema de Guildas
return [
    // Configurações de Banco de Dados
    'database' => [
        'fetch_mode' => PDO::FETCH_ASSOC
    ],
    
    // Configurações de Guilda
    'guild' => [
        'min_level' => 10,
        'max_members' => 50,
        'creation_cost' => 1000,
        'roles' => [
            'leader' => [
                'name' => 'Líder',
                'icon' => 'crown',
                'permissions' => ['manage', 'promote', 'demote', 'kick', 'accept', 'reject']
            ],
            'officer' => [
                'name' => 'Oficial',
                'icon' => 'shield-alt',
                'permissions' => ['accept', 'reject', 'kick']
            ],
            'member' => [
                'name' => 'Membro',
                'icon' => 'user',
                'permissions' => []
            ]
        ]
    ],
    
    // Configurações de Atividades
    'activities' => [
        'workout' => [
            'points' => 10,
            'xp' => 100
        ],
        'achievement' => [
            'points' => 50,
            'xp' => 500
        ],
        'challenge' => [
            'points' => 25,
            'xp' => 250
        ],
        'event' => [
            'points' => 15,
            'xp' => 150
        ],
        'contribution' => [
            'points' => 5,
            'xp' => 50
        ]
    ],
    
    // Configurações de Interface
    'ui' => [
        'default_emblem' => '/assets/img/default_guild_emblem.png',
        'default_colors' => [
            'primary' => '#4A90E2',
            'secondary' => '#2C3E50'
        ]
    ]
]; 