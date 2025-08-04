// arcade-effects.js - G Tech Arcade Interactive Effects

// Inizializzazione quando il DOM è pronto
document.addEventListener('DOMContentLoaded', function() {
    
    // Effetto suono arcade all'avvio (opzionale)
    initArcadeSound();
    
    // Animazione lettere titolo con delay randomico
    animateTitle();
    
    // Effetto glitch randomico sul titolo
    glitchEffect();
    
    // Preview animati per i giochi
    animateGamePreviews();
    
    // High Score Ticker
    initHighScoreTicker();
    
    // Effetti particelle di sfondo
    createParticles();
    
    // Effetto "Insert Coin" con suono
    initCoinSlots();
    
    // Effetto CRT distortion
    initCRTEffect();
    
    // Easter egg Konami Code
    initKonamiCode();
});

// Animazione lettere titolo
function animateTitle() {
    const letters = document.querySelectorAll('.arcade-title .letter');
    
    letters.forEach((letter, index) => {
        letter.style.animationDelay = `${index * 0.1}s`;
        
        // Effetto bounce on hover
        letter.addEventListener('mouseover', function() {
            this.style.animation = 'bounce 0.5s ease';
            setTimeout(() => {
                this.style.animation = `neon-flicker 1.5s infinite alternate`;
                this.style.animationDelay = `${index * 0.1}s`;
            }, 500);
        });
    });
}

// Effetto Glitch randomico
function glitchEffect() {
    const title = document.querySelector('.arcade-title');
    
    setInterval(() => {
        if (Math.random() > 0.95) {
            title.style.animation = 'glitch 0.3s';
            setTimeout(() => {
                title.style.animation = 'none';
            }, 300);
        }
    }, 2000);
}

// Animazione preview giochi
function animateGamePreviews() {
    const previews = document.querySelectorAll('.game-preview');
    const gamePatterns = {
        'firewall-defender': createFirewallPattern,
        'eraldin': createRPGPattern,
        'hedgehog-rush': createSpeedPattern,
        'cyberscape': createCyberPattern,
        'packet-runner': createNetworkPattern,
        'ghost-proxy': createStealthPattern,
        'datastream-dash': createDataPattern,
        'code-jumper': createCodePattern,
        'nano-blaster': createBlasterPattern
    };
    
    previews.forEach((preview) => {
        const cabinet = preview.closest('.arcade-cabinet');
        const gameType = cabinet.dataset.game;
        
        // Canvas per ogni preview
        const canvas = document.createElement('canvas');
        canvas.width = preview.offsetWidth;
        canvas.height = preview.offsetHeight;
        preview.appendChild(canvas);
        
        const ctx = canvas.getContext('2d');
        
        // Applica pattern specifico per ogni gioco
        if (gamePatterns[gameType]) {
            gamePatterns[gameType](ctx, canvas);
        }
    });
}

// Pattern animati per ogni gioco
function createFirewallPattern(ctx, canvas) {
    let offset = 0;
    
    function animate() {
        ctx.fillStyle = '#001a00';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Griglia matrix style
        ctx.strokeStyle = '#00ff00';
        ctx.lineWidth = 1;
        
        for (let x = 0; x < canvas.width; x += 20) {
            for (let y = 0; y < canvas.height; y += 20) {
                if (Math.random() > 0.98) {
                    ctx.fillStyle = `rgba(0, 255, 0, ${Math.random()})`;
                    ctx.fillRect(x, y + offset % 20, 15, 15);
                }
            }
        }
        
        offset += 2;
        requestAnimationFrame(animate);
    }
    animate();
}

function createRPGPattern(ctx, canvas) {
    let frame = 0;
    
    function animate() {
        ctx.fillStyle = '#1a0033';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Sparkles magici
        for (let i = 0; i < 5; i++) {
            const x = Math.random() * canvas.width;
            const y = Math.random() * canvas.height;
            const size = Math.random() * 3;
            
            ctx.fillStyle = `hsla(${frame % 360}, 100%, 70%, ${Math.random()})`;
            ctx.beginPath();
            ctx.arc(x, y, size, 0, Math.PI * 2);
            ctx.fill();
        }
        
        frame += 2;
        requestAnimationFrame(animate);
    }
    animate();
}

function createSpeedPattern(ctx, canvas) {
    let speed = 0;
    
    function animate() {
        ctx.fillStyle = 'rgba(0, 10, 30, 0.1)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Linee di velocità
        ctx.strokeStyle = '#00ffff';
        ctx.lineWidth = 2;
        
        for (let i = 0; i < 5; i++) {
            ctx.beginPath();
            ctx.moveTo(0, Math.random() * canvas.height);
            ctx.lineTo(canvas.width, Math.random() * canvas.height);
            ctx.stroke();
        }
        
        speed += 5;
        requestAnimationFrame(animate);
    }
    animate();
}

function createCyberPattern(ctx, canvas) {
    let offset = 0;
    
    function animate() {
        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Griglia prospettica
        ctx.strokeStyle = '#ff00ff';
        ctx.lineWidth = 1;
        
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        
        for (let i = 0; i < 10; i++) {
            const size = (offset + i * 20) % 200;
            ctx.strokeRect(centerX - size/2, centerY - size/2, size, size);
        }
        
        offset += 2;
        requestAnimationFrame(animate);
    }
    animate();
}

function createNetworkPattern(ctx, canvas) {
    const nodes = [];
    for (let i = 0; i < 10; i++) {
        nodes.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            vx: (Math.random() - 0.5) * 2,
            vy: (Math.random() - 0.5) * 2
        });
    }
    
    function animate() {
        ctx.fillStyle = 'rgba(0, 0, 20, 0.1)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Muovi e disegna nodi
        nodes.forEach(node => {
            node.x += node.vx;
            node.y += node.vy;
            
            if (node.x < 0 || node.x > canvas.width) node.vx *= -1;
            if (node.y < 0 || node.y > canvas.height) node.vy *= -1;
            
            ctx.fillStyle = '#00ff00';
            ctx.beginPath();
            ctx.arc(node.x, node.y, 3, 0, Math.PI * 2);
            ctx.fill();
        });
        
        // Collegamenti
        ctx.strokeStyle = 'rgba(0, 255, 0, 0.3)';
        ctx.lineWidth = 1;
        
        for (let i = 0; i < nodes.length; i++) {
            for (let j = i + 1; j < nodes.length; j++) {
                const dist = Math.hypot(nodes[i].x - nodes[j].x, nodes[i].y - nodes[j].y);
                if (dist < 100) {
                    ctx.beginPath();
                    ctx.moveTo(nodes[i].x, nodes[i].y);
                    ctx.lineTo(nodes[j].x, nodes[j].y);
                    ctx.stroke();
                }
            }
        }
        
        requestAnimationFrame(animate);
    }
    animate();
}

function createStealthPattern(ctx, canvas) {
    let alpha = 0;
    let direction = 0.01;
    
    function animate() {
        ctx.fillStyle = '#000';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Effetto invisibilità
        ctx.fillStyle = `rgba(100, 0, 255, ${alpha})`;
        ctx.fillRect(canvas.width/4, canvas.height/4, canvas.width/2, canvas.height/2);
        
        alpha += direction;
        if (alpha > 0.5 || alpha < 0) direction *= -1;
        
        requestAnimationFrame(animate);
    }
    animate();
}

function createDataPattern(ctx, canvas) {
    const particles = [];
    
    for (let i = 0; i < 20; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            speed: 1 + Math.random() * 3,
            color: ['#ff0000', '#00ff00', '#0000ff'][Math.floor(Math.random() * 3)]
        });
    }
    
    function animate() {
        ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        particles.forEach(p => {
            p.x += p.speed;
            if (p.x > canvas.width) {
                p.x = 0;
                p.y = Math.random() * canvas.height;
            }
            
            ctx.fillStyle = p.color;
            ctx.fillRect(p.x, p.y, 10, 5);
        });
        
        requestAnimationFrame(animate);
    }
    animate();
}

function createCodePattern(ctx, canvas) {
    const code = ['if', 'for', 'while', 'function', 'return', 'const', 'let', 'var'];
    let offset = 0;
    
    function animate() {
        ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        ctx.fillStyle = '#00ff00';
        ctx.font = '10px monospace';
        
        for (let i = 0; i < 5; i++) {
            const text = code[Math.floor(Math.random() * code.length)];
            const x = Math.random() * canvas.width;
            const y = (offset + i * 30) % canvas.height;
            
            ctx.fillText(text, x, y);
        }
        
        offset += 1;
        requestAnimationFrame(animate);
    }
    animate();
}

function createBlasterPattern(ctx, canvas) {
    let lasers = [];
    
    function animate() {
        ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Aggiungi nuovi laser
        if (Math.random() > 0.9) {
            lasers.push({
                x: Math.random() * canvas.width,
                y: canvas.height,
                speed: 5 + Math.random() * 5
            });
        }
        
        // Anima laser
        ctx.strokeStyle = '#ff0000';
        ctx.lineWidth = 2;
        
        lasers = lasers.filter(laser => {
            laser.y -= laser.speed;
            
            ctx.beginPath();
            ctx.moveTo(laser.x, laser.y);
            ctx.lineTo(laser.x, laser.y + 20);
            ctx.stroke();
            
            return laser.y > -20;
        });
        
        requestAnimationFrame(animate);
    }
    animate();
}

// High Score Ticker
function initHighScoreTicker() {
    const ticker = document.getElementById('ticker-content');
    const scores = [
        'ACE - 999999 pts',
        'MAX - 875420 pts',
        'NEO - 750000 pts',
        'ZAP - 625300 pts',
        'JET - 500000 pts',
        'REX - 425100 pts',
        'SKY - 350000 pts',
        'FOX - 275000 pts'
    ];
    
    ticker.textContent = scores.join(' ★ ') + ' ★ ';
}

// Particelle di sfondo
function createParticles() {
    const particlesContainer = document.createElement('div');
    particlesContainer.className = 'particles-container';
    particlesContainer.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    `;
    document.body.appendChild(particlesContainer);
    
    // Crea particelle
    for (let i = 0; i < 50; i++) {
        const particle = document.createElement('div');
        particle.style.cssText = `
            position: absolute;
            width: 2px;
            height: 2px;
            background: ${Math.random() > 0.5 ? '#ff00ff' : '#00ffff'};
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100}%;
            opacity: ${Math.random()};
            animation: float ${5 + Math.random() * 10}s linear infinite;
        `;
        particlesContainer.appendChild(particle);
    }
    
    // Aggiungi animazione CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes float {
            0% { transform: translateY(100vh) rotate(0deg); }
            100% { transform: translateY(-100vh) rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
}

// Effetti Coin Slot
function initCoinSlots() {
    const coinSlots = document.querySelectorAll('.coin-slot');
    
    coinSlots.forEach(slot => {
        slot.addEventListener('click', function() {
            this.textContent = 'CREDIT: 1';
            this.style.color = '#00ff00';
            
            // Effetto audio moneta
            playSound('coin');
            
            // Resetta dopo 3 secondi
            setTimeout(() => {
                this.textContent = 'INSERT COIN';
                this.style.color = '#ffff00';
            }, 3000);
        });
    });
}

// Effetto CRT Distortion
function initCRTEffect() {
    let distortionActive = false;
    
    document.addEventListener('keydown', function(e) {
        // Premi 'D' per attivare/disattivare distorsione
        if (e.key === 'd' || e.key === 'D') {
            distortionActive = !distortionActive;
            
            if (distortionActive) {
                document.body.style.filter = 'contrast(1.1) brightness(1.05)';
                document.body.style.animation = 'crt-flicker 0.1s infinite';
            } else {
                document.body.style.filter = 'none';
                document.body.style.animation = 'none';
            }
        }
    });
    
    // Aggiungi animazione CRT
    const style = document.createElement('style');
    style.textContent = `
        @keyframes crt-flicker {
            0% { opacity: 1; }
            50% { opacity: 0.98; }
            100% { opacity: 1; }
        }
    `;
    document.head.appendChild(style);
}

// Easter Egg - Konami Code
function initKonamiCode() {
    const konamiCode = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 
                       'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 
                       'b', 'a'];
    let konamiIndex = 0;
    
    document.addEventListener('keydown', function(e) {
        if (e.key === konamiCode[konamiIndex]) {
            konamiIndex++;
            
            if (konamiIndex === konamiCode.length) {
                activateEasterEgg();
                konamiIndex = 0;
            }
        } else {
            konamiIndex = 0;
        }
    });
}

function activateEasterEgg() {
    console.log('🎮 Konami Code Attivato!');
    
    // Effetto arcobaleno su tutto
    document.body.style.animation = 'rainbow 2s linear infinite';
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes rainbow {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
    
    // Messaggio segreto
    const message = document.createElement('div');
    message.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2rem;
        color: #fff;
        text-shadow: 0 0 20px #ff00ff;
        z-index: 10000;
        animation: bounce 1s ease infinite;
    `;
    message.textContent = '🎮 POWER UP! 🎮';
    document.body.appendChild(message);
    
    // Rimuovi dopo 3 secondi
    setTimeout(() => {
        document.body.style.animation = 'none';
        message.remove();
    }, 3000);
}

// Sistema audio (semplificato)
function initArcadeSound() {
    // Crea elementi audio se necessario
    const sounds = {
        startup: 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGD0fPTgjMGHm7A7+OZURE', // Placeholder
        coin: 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBTGD0fPTgjMGHm7A7+OZURE'  // Placeholder
    };
    
    window.arcadeSounds = {};
    
    for (const [name, src] of Object.entries(sounds)) {
        const audio = new Audio(src);
        audio.volume = 0.3;
        window.arcadeSounds[name] = audio;
    }
}

function playSound(soundName) {
    if (window.arcadeSounds && window.arcadeSounds[soundName]) {
        window.arcadeSounds[soundName].currentTime = 0;
        window.arcadeSounds[soundName].play().catch(e => {
            // Gestisci errori audio silenziosamente
            console.log('Audio non disponibile');
        });
    }
}

// Aggiungi effetto hover sui cabinati con suono
document.addEventListener('DOMContentLoaded', function() {
    const cabinets = document.querySelectorAll('.arcade-cabinet');
    
    cabinets.forEach(cabinet => {
        cabinet.addEventListener('mouseenter', function() {
            playSound('hover');
        });
    });
});
