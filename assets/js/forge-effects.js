// Classe para gerenciar partículas
class ParticleSystem {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            maxParticles: options.maxParticles || 50,
            particleLife: options.particleLife || 2000,
            particleSize: options.particleSize || 4,
            particleColor: options.particleColor || '#FF4D00',
            particleSpeed: options.particleSpeed || 2,
            spawnRate: options.spawnRate || 100
        };
        
        this.particles = [];
        this.lastSpawn = 0;
        this.active = false;
    }

    start() {
        this.active = true;
        this.animate();
    }

    stop() {
        this.active = false;
    }

    createParticle() {
        const particle = document.createElement('div');
        particle.className = 'particle-effect';
        particle.style.width = `${this.options.particleSize}px`;
        particle.style.height = `${this.options.particleSize}px`;
        particle.style.background = this.options.particleColor;
        
        // Posição inicial aleatória
        const x = Math.random() * this.container.offsetWidth;
        const y = Math.random() * this.container.offsetHeight;
        
        particle.style.left = `${x}px`;
        particle.style.top = `${y}px`;
        
        // Velocidade e direção aleatória
        const angle = Math.random() * Math.PI * 2;
        const speed = Math.random() * this.options.particleSpeed;
        
        const particleObj = {
            element: particle,
            x,
            y,
            vx: Math.cos(angle) * speed,
            vy: Math.sin(angle) * speed,
            life: this.options.particleLife,
            born: Date.now()
        };

        this.particles.push(particleObj);
        this.container.appendChild(particle);
    }

    animate() {
        if (!this.active) return;

        const now = Date.now();

        // Criar novas partículas
        if (now - this.lastSpawn > this.options.spawnRate && 
            this.particles.length < this.options.maxParticles) {
            this.createParticle();
            this.lastSpawn = now;
        }

        // Atualizar partículas existentes
        for (let i = this.particles.length - 1; i >= 0; i--) {
            const particle = this.particles[i];
            const age = now - particle.born;

            if (age >= particle.life) {
                // Remover partículas mortas
                this.container.removeChild(particle.element);
                this.particles.splice(i, 1);
                continue;
            }

            // Atualizar posição
            particle.x += particle.vx;
            particle.y += particle.vy;

            // Verificar colisões com as bordas
            if (particle.x < 0 || particle.x > this.container.offsetWidth) {
                particle.vx *= -1;
            }
            if (particle.y < 0 || particle.y > this.container.offsetHeight) {
                particle.vy *= -1;
            }

            // Aplicar fade out baseado na idade
            const opacity = 1 - (age / particle.life);
            
            // Atualizar elemento visual
            particle.element.style.transform = `translate(${particle.x}px, ${particle.y}px)`;
            particle.element.style.opacity = opacity;
        }

        requestAnimationFrame(() => this.animate());
    }
}

// Classe para gerenciar efeitos de têmpera
class TemperingEffect {
    constructor(element, options = {}) {
        this.element = element;
        this.options = {
            intensity: options.intensity || 0.5,
            color: options.color || '#FF4D00',
            followMouse: options.followMouse || true
        };

        this.effect = document.createElement('div');
        this.effect.className = 'tempering-effect';
        this.element.appendChild(this.effect);

        if (this.options.followMouse) {
            this.setupMouseTracking();
        }

        this.updateEffect();
    }

    setupMouseTracking() {
        this.element.addEventListener('mousemove', (e) => {
            const rect = this.element.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;

            this.effect.style.setProperty('--mouse-x', `${x}%`);
            this.effect.style.setProperty('--mouse-y', `${y}%`);
        });

        this.element.addEventListener('mouseleave', () => {
            this.effect.style.setProperty('--mouse-x', '50%');
            this.effect.style.setProperty('--mouse-y', '50%');
        });
    }

    updateEffect() {
        this.effect.style.setProperty('--tempering-color', this.options.color);
        this.effect.style.setProperty('--tempering-intensity', this.options.intensity);
    }

    setIntensity(intensity) {
        this.options.intensity = Math.max(0, Math.min(1, intensity));
        this.updateEffect();
    }

    setColor(color) {
        this.options.color = color;
        this.updateEffect();
    }
}

// Classe para gerenciar auras
class AuraEffect {
    constructor(element, options = {}) {
        this.element = element;
        this.options = {
            color: options.color || '#FF4D00',
            size: options.size || 10,
            pulseSpeed: options.pulseSpeed || 2,
            blurAmount: options.blurAmount || 15
        };

        this.aura = document.createElement('div');
        this.aura.className = 'aura-effect';
        this.element.appendChild(this.aura);

        this.updateAura();
    }

    updateAura() {
        this.aura.style.setProperty('--aura-color', this.options.color);
        this.aura.style.setProperty('--aura-size', `${this.options.size}px`);
        this.aura.style.setProperty('--pulse-speed', `${this.options.pulseSpeed}s`);
        this.aura.style.setProperty('--blur-amount', `${this.options.blurAmount}px`);
    }

    setColor(color) {
        this.options.color = color;
        this.updateAura();
    }

    setSize(size) {
        this.options.size = size;
        this.updateAura();
    }

    setPulseSpeed(speed) {
        this.options.pulseSpeed = speed;
        this.updateAura();
    }

    setBlurAmount(blur) {
        this.options.blurAmount = blur;
        this.updateAura();
    }
}

// Classe para gerenciar conquistas
class AchievementEffect {
    constructor(element, options = {}) {
        this.element = element;
        this.options = {
            particleCount: options.particleCount || 20,
            particleColor: options.particleColor || '#FFD700',
            duration: options.duration || 2000
        };

        this.container = document.createElement('div');
        this.container.className = 'achievement-particles';
        this.element.appendChild(this.container);
    }

    trigger() {
        // Limpar partículas existentes
        this.container.innerHTML = '';

        // Criar novas partículas
        for (let i = 0; i < this.options.particleCount; i++) {
            const particle = document.createElement('div');
            particle.className = 'achievement-particle';
            particle.style.setProperty('--particle-color', this.options.particleColor);
            
            // Posição inicial aleatória
            particle.style.left = `${Math.random() * 100}%`;
            
            // Atraso aleatório
            particle.style.animationDelay = `${Math.random() * this.options.duration}ms`;
            
            this.container.appendChild(particle);
        }

        // Limpar após a animação
        setTimeout(() => {
            this.container.innerHTML = '';
        }, this.options.duration);
    }
}

// Inicialização dos efeitos
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar efeitos de têmpera nos músculos
    document.querySelectorAll('.muscle-highlight').forEach(muscle => {
        new TemperingEffect(muscle, {
            intensity: parseFloat(muscle.dataset.level) / 100,
            color: muscle.dataset.color
        });
    });

    // Inicializar auras nos ranks lendários
    document.querySelectorAll('.rank-theme-legendary').forEach(rank => {
        new AuraEffect(rank, {
            color: '#FF4D00',
            size: 15,
            pulseSpeed: 2,
            blurAmount: 20
        });
    });

    // Inicializar sistema de partículas nas conquistas
    document.querySelectorAll('.achievement-card.completed').forEach(achievement => {
        const particles = new ParticleSystem(achievement, {
            maxParticles: 20,
            particleColor: achievement.dataset.particleColor || '#FFD700'
        });

        achievement.addEventListener('mouseenter', () => particles.start());
        achievement.addEventListener('mouseleave', () => particles.stop());
    });

    // Efeito de conquista ao completar
    document.addEventListener('achievementUnlocked', (e) => {
        const achievement = document.querySelector(`.achievement-card[data-code="${e.detail.code}"]`);
        if (achievement) {
            const effect = new AchievementEffect(achievement, {
                particleColor: e.detail.color
            });
            effect.trigger();
        }
    });
}); 