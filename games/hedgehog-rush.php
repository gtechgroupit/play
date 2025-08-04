<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Hedgehog Rush - G Tech Arcade</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        body {
            background: #090921;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            position: fixed;
            width: 100%;
            height: 100%;
            touch-action: none;
        }

        #gameCanvas {
            display: block;
            width: 100%;
            height: 100%;
            cursor: pointer;
            image-rendering: crisp-edges;
            image-rendering: -moz-crisp-edges;
            image-rendering: -webkit-crisp-edges;
            image-rendering: pixelated;
        }

        #touchJumpButton {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 80px;
            height: 80px;
            background: rgba(255, 90, 242, 0.3);
            border: 3px solid #ff5af2;
            border-radius: 50%;
            display: none;
            justify-content: center;
            align-items: center;
            font-size: 30px;
            color: #ff5af2;
            font-weight: bold;
            z-index: 1000;
            touch-action: none;
            -webkit-tap-highlight-color: transparent;
        }

        #touchJumpButton:active {
            background: rgba(255, 90, 242, 0.6);
            transform: scale(0.95);
        }

        @media (hover: none) and (pointer: coarse) {
            #touchJumpButton {
                display: flex;
            }
        }

        /* Samsung Fold optimization */
        @media screen and (min-width: 600px) and (max-width: 850px) and (orientation: portrait) {
            #touchJumpButton {
                width: 100px;
                height: 100px;
                font-size: 36px;
            }
        }

        /* Landscape mobile */
        @media screen and (max-height: 500px) and (orientation: landscape) {
            #touchJumpButton {
                width: 60px;
                height: 60px;
                font-size: 24px;
                bottom: 10px;
                right: 10px;
            }
        }

        /* Loading screen */
        #loadingScreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #090921;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #00e8ff;
            font-size: 24px;
            z-index: 2000;
        }

        .hide {
            display: none !important;
        }

        /* Glow effect */
        .glow {
            text-shadow: 0 0 20px currentColor;
        }
    </style>
</head>
<body>
    <div id="loadingScreen" class="glow">Caricamento...</div>
    <canvas id="gameCanvas"></canvas>
    <button id="touchJumpButton">↑</button>

    <script>
        // ===== CONFIGURAZIONE GLOBALE =====
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        const touchButton = document.getElementById('touchJumpButton');
        const loadingScreen = document.getElementById('loadingScreen');

        // Impostazioni di gioco
        const GAME_CONFIG = {
            gravity: 0.8,
            jumpPower: -15,
            maxFallSpeed: 20,
            baseSpeed: 8,
            maxSpeed: 16,
            speedIncrement: 0.001,
            ringValue: 10,
            boostDuration: 300, // frames (5 secondi a 60fps)
            invincibilityFlash: 10
        };

        // Colori tema cyber
        const COLORS = {
            background: '#090921',
            primary: '#00e8ff',
            secondary: '#ff5af2',
            accent: '#ffef00',
            danger: '#ff3366',
            white: '#ffffff',
            dark: '#1a1a2e'
        };

        // Stato del gioco
        let gameState = {
            running: false,
            paused: false,
            gameOver: false,
            score: 0,
            lives: 3,
            rings: 0,
            consecutiveRings: 0,
            speed: GAME_CONFIG.baseSpeed,
            distance: 0,
            boosting: false,
            boostTimer: 0,
            invincible: false,
            invincibilityTimer: 0
        };

        // Dimensioni responsive
        let screenWidth = window.innerWidth;
        let screenHeight = window.innerHeight;
        let scale = 1;

        // ===== CLASSE PLAYER (RICCIO) =====
        class Hedgehog {
            constructor() {
                this.width = 50;
                this.height = 50;
                this.x = screenWidth * 0.2;
                this.y = screenHeight * 0.5;
                this.velocityY = 0;
                this.jumping = false;
                this.doubleJump = false;
                this.rotation = 0;
                this.trail = [];
                this.maxTrailLength = 10;
                this.animationFrame = 0;
            }

            update() {
                // Applica gravità
                this.velocityY += GAME_CONFIG.gravity;
                this.velocityY = Math.min(this.velocityY, GAME_CONFIG.maxFallSpeed);
                this.y += this.velocityY;

                // Limiti schermo
                if (this.y < this.height / 2) {
                    this.y = this.height / 2;
                    this.velocityY = 0;
                }

                // Controllo terreno
                const groundY = screenHeight - 100;
                if (this.y > groundY - this.height / 2) {
                    this.y = groundY - this.height / 2;
                    this.velocityY = 0;
                    this.jumping = false;
                    this.doubleJump = false;
                }

                // Rotazione durante il salto
                if (this.jumping) {
                    this.rotation += 0.2;
                } else {
                    this.rotation = 0;
                }

                // Trail effect quando boost attivo
                if (gameState.boosting) {
                    this.trail.push({
                        x: this.x,
                        y: this.y,
                        opacity: 1
                    });

                    if (this.trail.length > this.maxTrailLength) {
                        this.trail.shift();
                    }
                }

                // Aggiorna trail
                this.trail.forEach((point, index) => {
                    point.opacity -= 0.1;
                    if (point.opacity <= 0) {
                        this.trail.splice(index, 1);
                    }
                });

                // Animazione
                this.animationFrame = (this.animationFrame + 0.5) % 360;
            }

            jump() {
                if (!this.jumping) {
                    this.velocityY = GAME_CONFIG.jumpPower;
                    this.jumping = true;
                    this.playJumpSound();
                } else if (!this.doubleJump && this.velocityY > -10) {
                    this.velocityY = GAME_CONFIG.jumpPower * 0.8;
                    this.doubleJump = true;
                    this.playJumpSound();
                }
            }

            draw() {
                // Disegna trail
                this.trail.forEach(point => {
                    ctx.save();
                    ctx.globalAlpha = point.opacity * 0.5;
                    ctx.fillStyle = COLORS.secondary;
                    ctx.shadowBlur = 20;
                    ctx.shadowColor = COLORS.secondary;
                    ctx.beginPath();
                    ctx.arc(point.x, point.y, this.width / 2 * 0.8, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.restore();
                });

                ctx.save();
                ctx.translate(this.x, this.y);
                ctx.rotate(this.rotation);

                // Effetto invincibilità
                if (gameState.invincible && Math.floor(gameState.invincibilityTimer / GAME_CONFIG.invincibilityFlash) % 2) {
                    ctx.globalAlpha = 0.5;
                }

                // Corpo del riccio (forma geometrica stilizzata)
                // Glow effect
                ctx.shadowBlur = gameState.boosting ? 30 : 15;
                ctx.shadowColor = gameState.boosting ? COLORS.accent : COLORS.primary;

                // Corpo principale
                const gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, this.width / 2);
                gradient.addColorStop(0, gameState.boosting ? COLORS.accent : COLORS.primary);
                gradient.addColorStop(0.7, gameState.boosting ? COLORS.secondary : '#0099cc');
                gradient.addColorStop(1, COLORS.dark);

                ctx.fillStyle = gradient;
                ctx.beginPath();
                ctx.arc(0, 0, this.width / 2, 0, Math.PI * 2);
                ctx.fill();

                // Spikes (aculei)
                ctx.strokeStyle = COLORS.white;
                ctx.lineWidth = 2;
                for (let i = 0; i < 8; i++) {
                    const angle = (i / 8) * Math.PI * 2 + this.animationFrame * 0.01;
                    const spikeLength = this.width / 2 + 5;
                    ctx.beginPath();
                    ctx.moveTo(
                        Math.cos(angle) * this.width / 3,
                        Math.sin(angle) * this.width / 3
                    );
                    ctx.lineTo(
                        Math.cos(angle) * spikeLength,
                        Math.sin(angle) * spikeLength
                    );
                    ctx.stroke();
                }

                // Occhio
                ctx.fillStyle = COLORS.white;
                ctx.beginPath();
                ctx.arc(10, -5, 8, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = COLORS.dark;
                ctx.beginPath();
                ctx.arc(12, -5, 4, 0, Math.PI * 2);
                ctx.fill();

                ctx.restore();
            }

            playJumpSound() {
                // Effetto sonoro sintetizzato
                if (window.AudioContext || window.webkitAudioContext) {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);

                    oscillator.frequency.setValueAtTime(400, audioCtx.currentTime);
                    oscillator.frequency.exponentialRampToValueAtTime(800, audioCtx.currentTime + 0.1);
                    
                    gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);

                    oscillator.start(audioCtx.currentTime);
                    oscillator.stop(audioCtx.currentTime + 0.1);
                }
            }

            reset() {
                this.y = screenHeight * 0.5;
                this.velocityY = 0;
                this.jumping = false;
                this.doubleJump = false;
                this.rotation = 0;
                this.trail = [];
            }
        }

        // ===== CLASSE ANELLO =====
        class Ring {
            constructor(x, y) {
                this.x = x;
                this.y = y;
                this.radius = 20;
                this.collected = false;
                this.animationFrame = Math.random() * 360;
                this.floatOffset = Math.random() * Math.PI * 2;
            }

            update() {
                this.x -= gameState.speed;
                this.animationFrame += 5;

                // Rimuovi se fuori schermo
                if (this.x < -50) {
                    return false;
                }
                return true;
            }

            draw() {
                if (this.collected) return;

                const floatY = this.y + Math.sin(this.animationFrame * 0.05 + this.floatOffset) * 5;

                ctx.save();
                ctx.translate(this.x, floatY);
                ctx.rotate(this.animationFrame * 0.02);

                // Glow effect
                ctx.shadowBlur = 20;
                ctx.shadowColor = COLORS.accent;

                // Anello esterno
                ctx.strokeStyle = COLORS.accent;
                ctx.lineWidth = 4;
                ctx.beginPath();
                ctx.arc(0, 0, this.radius, 0, Math.PI * 2);
                ctx.stroke();

                // Anello interno
                ctx.strokeStyle = COLORS.white;
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.arc(0, 0, this.radius * 0.7, 0, Math.PI * 2);
                ctx.stroke();

                // Centro luminoso
                const centerGradient = ctx.createRadialGradient(0, 0, 0, 0, 0, this.radius * 0.5);
                centerGradient.addColorStop(0, 'rgba(255, 239, 0, 0.8)');
                centerGradient.addColorStop(1, 'rgba(255, 239, 0, 0)');
                ctx.fillStyle = centerGradient;
                ctx.beginPath();
                ctx.arc(0, 0, this.radius * 0.5, 0, Math.PI * 2);
                ctx.fill();

                ctx.restore();
            }

            checkCollision(player) {
                if (this.collected) return false;

                const dx = this.x - player.x;
                const dy = this.y - player.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < this.radius + player.width / 2) {
                    this.collected = true;
                    return true;
                }
                return false;
            }
        }

        // ===== CLASSE OSTACOLO =====
        class Obstacle {
            constructor(x, y, type) {
                this.x = x;
                this.y = y;
                this.type = type;
                this.width = 40;
                this.height = 60;
                this.animationFrame = 0;

                // Personalizza dimensioni per tipo
                switch (type) {
                    case 'spike':
                        this.width = 40;
                        this.height = 40;
                        break;
                    case 'barrier':
                        this.width = 60;
                        this.height = 80;
                        break;
                    case 'hole':
                        this.width = 100;
                        this.height = 40;
                        break;
                    case 'firewall':
                        this.width = 50;
                        this.height = 100;
                        break;
                }
            }

            update() {
                this.x -= gameState.speed;
                this.animationFrame += 0.1;

                if (this.x < -100) {
                    return false;
                }
                return true;
            }

            draw() {
                ctx.save();
                ctx.translate(this.x, this.y);

                switch (this.type) {
                    case 'spike':
                        this.drawSpike();
                        break;
                    case 'barrier':
                        this.drawBarrier();
                        break;
                    case 'hole':
                        this.drawHole();
                        break;
                    case 'firewall':
                        this.drawFirewall();
                        break;
                }

                ctx.restore();
            }

            drawSpike() {
                ctx.fillStyle = COLORS.danger;
                ctx.shadowBlur = 15;
                ctx.shadowColor = COLORS.danger;

                // Triangolo appuntito
                ctx.beginPath();
                ctx.moveTo(0, -this.height / 2);
                ctx.lineTo(-this.width / 2, this.height / 2);
                ctx.lineTo(this.width / 2, this.height / 2);
                ctx.closePath();
                ctx.fill();

                // Dettaglio metallico
                ctx.strokeStyle = COLORS.white;
                ctx.lineWidth = 2;
                ctx.stroke();
            }

            drawBarrier() {
                // Barriera magnetica animata
                const pulse = Math.sin(this.animationFrame) * 0.1 + 1;

                ctx.save();
                ctx.scale(pulse, 1);

                // Gradiente elettrico
                const gradient = ctx.createLinearGradient(-this.width/2, 0, this.width/2, 0);
                gradient.addColorStop(0, COLORS.secondary);
                gradient.addColorStop(0.5, COLORS.white);
                gradient.addColorStop(1, COLORS.secondary);

                ctx.fillStyle = gradient;
                ctx.shadowBlur = 20;
                ctx.shadowColor = COLORS.secondary;
                ctx.fillRect(-this.width/2, -this.height/2, this.width, this.height);

                // Pattern elettrico
                ctx.strokeStyle = COLORS.white;
                ctx.lineWidth = 2;
                ctx.setLineDash([5, 5]);
                ctx.strokeRect(-this.width/2, -this.height/2, this.width, this.height);

                ctx.restore();
            }

            drawHole() {
                // Buco di rete
                ctx.fillStyle = COLORS.background;
                ctx.strokeStyle = COLORS.danger;
                ctx.lineWidth = 3;

                ctx.beginPath();
                ctx.ellipse(0, 0, this.width/2, this.height/2, 0, 0, Math.PI * 2);
                ctx.fill();
                ctx.stroke();

                // Effetto vortice
                ctx.strokeStyle = `rgba(255, 51, 102, ${Math.sin(this.animationFrame * 2) * 0.5 + 0.5})`;
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.ellipse(0, 0, this.width/3, this.height/3, this.animationFrame, 0, Math.PI * 2);
                ctx.stroke();
            }

            drawFirewall() {
                // Firewall animato
                const flameHeight = Math.sin(this.animationFrame * 3) * 10;

                // Fiamme digitali
                for (let i = -2; i <= 2; i++) {
                    const offset = i * 10;
                    const height = this.height + flameHeight + Math.sin(this.animationFrame * 5 + i) * 5;

                    const gradient = ctx.createLinearGradient(0, this.height/2, 0, -height/2);
                    gradient.addColorStop(0, COLORS.danger);
                    gradient.addColorStop(0.5, COLORS.secondary);
                    gradient.addColorStop(1, 'rgba(255, 90, 242, 0)');

                    ctx.fillStyle = gradient;
                    ctx.fillRect(offset - 5, this.height/2, 10, -height);
                }

                // Base del firewall
                ctx.fillStyle = COLORS.dark;
                ctx.strokeStyle = COLORS.danger;
                ctx.lineWidth = 2;
                ctx.fillRect(-this.width/2, this.height/2 - 10, this.width, 10);
                ctx.strokeRect(-this.width/2, this.height/2 - 10, this.width, 10);
            }

            checkCollision(player) {
                if (gameState.invincible) return false;

                // Collision box più piccola per gameplay più giusto
                const margin = 10;
                const playerLeft = player.x - player.width/2 + margin;
                const playerRight = player.x + player.width/2 - margin;
                const playerTop = player.y - player.height/2 + margin;
                const playerBottom = player.y + player.height/2 - margin;

                const obstacleLeft = this.x - this.width/2;
                const obstacleRight = this.x + this.width/2;
                const obstacleTop = this.y - this.height/2;
                const obstacleBottom = this.y + this.height/2;

                // Gestione speciale per i buchi
                if (this.type === 'hole') {
                    if (playerLeft < obstacleRight && playerRight > obstacleLeft && 
                        playerBottom > obstacleTop) {
                        return true;
                    }
                } else {
                    if (playerLeft < obstacleRight && playerRight > obstacleLeft &&
                        playerTop < obstacleBottom && playerBottom > obstacleTop) {
                        return true;
                    }
                }

                return false;
            }
        }

        // ===== CLASSE PARTICELLA =====
        class Particle {
            constructor(x, y, type) {
                this.x = x;
                this.y = y;
                this.type = type;
                this.velocityX = (Math.random() - 0.5) * 10;
                this.velocityY = (Math.random() - 0.5) * 10;
                this.life = 1;
                this.decay = 0.02;
                this.size = Math.random() * 5 + 5;

                if (type === 'ring') {
                    this.color = COLORS.accent;
                } else if (type === 'boost') {
                    this.color = COLORS.secondary;
                    this.decay = 0.01;
                } else if (type === 'damage') {
                    this.color = COLORS.danger;
                }
            }

            update() {
                this.x += this.velocityX;
                this.y += this.velocityY;
                this.velocityX *= 0.98;
                this.velocityY *= 0.98;
                this.life -= this.decay;
                this.size *= 0.98;

                return this.life > 0;
            }

            draw() {
                ctx.save();
                ctx.globalAlpha = this.life;
                ctx.fillStyle = this.color;
                ctx.shadowBlur = 10;
                ctx.shadowColor = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            }
        }

        // ===== GESTIONE SFONDO E PARALLASSE =====
        class Background {
            constructor() {
                this.layers = [
                    { speed: 0.5, elements: [] },
                    { speed: 1, elements: [] },
                    { speed: 2, elements: [] }
                ];

                this.generateElements();
            }

            generateElements() {
                // Layer 0: Stelle di sfondo
                for (let i = 0; i < 50; i++) {
                    this.layers[0].elements.push({
                        x: Math.random() * screenWidth * 2,
                        y: Math.random() * screenHeight * 0.7,
                        size: Math.random() * 2 + 1,
                        brightness: Math.random() * 0.5 + 0.5
                    });
                }

                // Layer 1: Strutture digitali medie
                for (let i = 0; i < 20; i++) {
                    this.layers[1].elements.push({
                        x: Math.random() * screenWidth * 2,
                        y: screenHeight * 0.3 + Math.random() * screenHeight * 0.3,
                        width: Math.random() * 60 + 40,
                        height: Math.random() * 100 + 50,
                        type: 'building'
                    });
                }

                // Layer 2: Elementi in primo piano
                for (let i = 0; i < 10; i++) {
                    this.layers[2].elements.push({
                        x: Math.random() * screenWidth * 2,
                        y: screenHeight - 150,
                        width: Math.random() * 80 + 20,
                        height: Math.random() * 40 + 20,
                        type: 'circuit'
                    });
                }
            }

            update() {
                this.layers.forEach(layer => {
                    layer.elements.forEach(element => {
                        element.x -= gameState.speed * layer.speed;

                        // Riposiziona elementi che escono dallo schermo
                        if (element.x < -100) {
                            element.x = screenWidth + Math.random() * 500;
                            if (element.type === 'building') {
                                element.height = Math.random() * 100 + 50;
                            }
                        }
                    });
                });
            }

            draw() {
                // Gradiente di sfondo
                const gradient = ctx.createLinearGradient(0, 0, 0, screenHeight);
                gradient.addColorStop(0, '#0a0a2e');
                gradient.addColorStop(0.5, COLORS.background);
                gradient.addColorStop(1, '#16213e');
                ctx.fillStyle = gradient;
                ctx.fillRect(0, 0, screenWidth, screenHeight);

                // Disegna layer 0: Stelle
                ctx.fillStyle = COLORS.white;
                this.layers[0].elements.forEach(star => {
                    ctx.save();
                    ctx.globalAlpha = star.brightness * (0.5 + Math.sin(Date.now() * 0.001 + star.x) * 0.5);
                    ctx.beginPath();
                    ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.restore();
                });

                // Disegna layer 1: Strutture
                this.layers[1].elements.forEach(building => {
                    ctx.fillStyle = '#1a1a3e';
                    ctx.strokeStyle = COLORS.primary;
                    ctx.lineWidth = 1;
                    ctx.fillRect(building.x, building.y, building.width, building.height);
                    ctx.strokeRect(building.x, building.y, building.width, building.height);

                    // Finestre illuminate
                    ctx.fillStyle = COLORS.primary;
                    for (let row = 0; row < 4; row++) {
                        for (let col = 0; col < 2; col++) {
                            if (Math.random() > 0.3) {
                                ctx.fillRect(
                                    building.x + 10 + col * 20,
                                    building.y + 10 + row * 20,
                                    10, 10
                                );
                            }
                        }
                    }
                });

                // Disegna layer 2: Circuiti
                this.layers[2].elements.forEach(circuit => {
                    ctx.strokeStyle = COLORS.secondary;
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(circuit.x, circuit.y);
                    ctx.lineTo(circuit.x + circuit.width, circuit.y);
                    ctx.lineTo(circuit.x + circuit.width, circuit.y - circuit.height);
                    ctx.stroke();

                    // Nodi del circuito
                    ctx.fillStyle = COLORS.secondary;
                    ctx.beginPath();
                    ctx.arc(circuit.x, circuit.y, 4, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.beginPath();
                    ctx.arc(circuit.x + circuit.width, circuit.y - circuit.height, 4, 0, Math.PI * 2);
                    ctx.fill();
                });
            }
        }

        // ===== SISTEMA DI GENERAZIONE LIVELLI =====
        class LevelGenerator {
            constructor() {
                this.patterns = [
                    { // Pattern 1: Anelli in linea
                        rings: [{x: 0, y: -50}, {x: 50, y: -50}, {x: 100, y: -50}],
                        obstacles: []
                    },
                    { // Pattern 2: Salto singolo
                        rings: [{x: 0, y: -100}, {x: 50, y: -150}, {x: 100, y: -100}],
                        obstacles: [{x: 50, y: 0, type: 'spike'}]
                    },
                    { // Pattern 3: Barriera alta
                        rings: [{x: 0, y: -150}, {x: 100, y: -150}],
                        obstacles: [{x: 50, y: -50, type: 'barrier'}]
                    },
                    { // Pattern 4: Doppio salto
                        rings: [{x: 50, y: -200}],
                        obstacles: [{x: 0, y: 0, type: 'spike'}, {x: 100, y: 0, type: 'spike'}]
                    },
                    { // Pattern 5: Firewall
                        rings: [{x: -50, y: -100}, {x: 150, y: -100}],
                        obstacles: [{x: 50, y: -20, type: 'firewall'}]
                    },
                    { // Pattern 6: Buco
                        rings: [{x: 50, y: -150}],
                        obstacles: [{x: 50, y: 50, type: 'hole'}]
                    }
                ];
                
                this.lastPatternX = screenWidth;
                this.minDistance = 300;
                this.maxDistance = 500;
            }

            shouldGenerate() {
                return this.lastPatternX < screenWidth + 200;
            }

            generate(rings, obstacles) {
                const pattern = this.patterns[Math.floor(Math.random() * this.patterns.length)];
                const baseX = this.lastPatternX + this.minDistance + Math.random() * (this.maxDistance - this.minDistance);
                const groundY = screenHeight - 100;

                // Genera anelli
                pattern.rings.forEach(ring => {
                    rings.push(new Ring(baseX + ring.x, groundY + ring.y));
                });

                // Genera ostacoli
                pattern.obstacles.forEach(obstacle => {
                    obstacles.push(new Obstacle(baseX + obstacle.x, groundY + obstacle.y, obstacle.type));
                });

                this.lastPatternX = baseX + 200;

                // Aumenta difficoltà nel tempo
                this.minDistance = Math.max(200, this.minDistance - 0.5);
                this.maxDistance = Math.max(300, this.maxDistance - 0.5);
            }
        }

        // ===== GESTIONE GIOCO PRINCIPALE =====
        class Game {
            constructor() {
                this.player = new Hedgehog();
                this.background = new Background();
                this.levelGenerator = new LevelGenerator();
                this.rings = [];
                this.obstacles = [];
                this.particles = [];
                this.lastTime = 0;
                this.init();
            }

            init() {
                this.setupCanvas();
                this.setupEventListeners();
                this.reset();
                
                // Nascondi schermata di caricamento
                loadingScreen.classList.add('hide');
                
                // Mostra schermata iniziale
                this.showStartScreen();
            }

            setupCanvas() {
                this.resizeCanvas();
                window.addEventListener('resize', () => this.resizeCanvas());
                window.addEventListener('orientationchange', () => {
                    setTimeout(() => this.resizeCanvas(), 100);
                });
            }

            resizeCanvas() {
                screenWidth = window.innerWidth;
                screenHeight = window.innerHeight;
                
                canvas.width = screenWidth;
                canvas.height = screenHeight;
                
                // Calcola scala per mantenere proporzioni
                scale = Math.min(screenWidth / 1920, screenHeight / 1080);
                
                // Aggiorna posizioni
                if (this.player) {
                    this.player.x = screenWidth * 0.2;
                }
            }

            setupEventListeners() {
                // Desktop
                window.addEventListener('keydown', (e) => {
                    if (e.code === 'Space' || e.code === 'ArrowUp') {
                        e.preventDefault();
                        this.handleJump();
                    }
                });

                // Mobile - Canvas touch
                canvas.addEventListener('touchstart', (e) => {
                    e.preventDefault();
                    this.handleJump();
                });

                // Mobile - Pulsante dedicato
                touchButton.addEventListener('touchstart', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.handleJump();
                });

                // Previeni comportamenti default su mobile
                document.addEventListener('touchmove', (e) => {
                    e.preventDefault();
                }, { passive: false });
            }

            handleJump() {
                if (gameState.gameOver) {
                    this.reset();
                } else if (!gameState.running) {
                    this.start();
                } else {
                    this.player.jump();
                }
            }

            reset() {
                gameState = {
                    running: false,
                    paused: false,
                    gameOver: false,
                    score: 0,
                    lives: 3,
                    rings: 0,
                    consecutiveRings: 0,
                    speed: GAME_CONFIG.baseSpeed,
                    distance: 0,
                    boosting: false,
                    boostTimer: 0,
                    invincible: false,
                    invincibilityTimer: 0
                };

                this.player.reset();
                this.rings = [];
                this.obstacles = [];
                this.particles = [];
                this.levelGenerator = new LevelGenerator();
                this.background = new Background();
            }

            start() {
                gameState.running = true;
                this.lastTime = performance.now();
                this.gameLoop();
            }

            gameLoop(currentTime = 0) {
                const deltaTime = currentTime - this.lastTime;
                this.lastTime = currentTime;

                this.update();
                this.render();

                if (gameState.running) {
                    requestAnimationFrame((time) => this.gameLoop(time));
                }
            }

            update() {
                if (!gameState.running || gameState.paused) return;

                // Update velocità
                if (!gameState.boosting) {
                    gameState.speed = Math.min(gameState.speed + GAME_CONFIG.speedIncrement, GAME_CONFIG.maxSpeed);
                } else {
                    gameState.speed = GAME_CONFIG.maxSpeed * 1.5;
                }

                // Update distanza e punteggio
                gameState.distance += gameState.speed;
                gameState.score += Math.floor(gameState.speed / 10);

                // Update giocatore
                this.player.update();

                // Update sfondo
                this.background.update();

                // Genera nuovi elementi
                if (this.levelGenerator.shouldGenerate()) {
                    this.levelGenerator.generate(this.rings, this.obstacles);
                }

                // Update anelli
                this.rings = this.rings.filter(ring => {
                    if (!ring.update()) return false;
                    
                    if (ring.checkCollision(this.player)) {
                        this.collectRing(ring);
                        return false;
                    }
                    return true;
                });

                // Update ostacoli
                this.obstacles = this.obstacles.filter(obstacle => {
                    if (!obstacle.update()) return false;
                    
                    if (obstacle.checkCollision(this.player)) {
                        this.handleCollision();
                    }
                    return true;
                });

                // Update particelle
                this.particles = this.particles.filter(particle => particle.update());

                // Update boost
                if (gameState.boosting) {
                    gameState.boostTimer--;
                    if (gameState.boostTimer <= 0) {
                        this.endBoost();
                    }
                }

                // Update invincibilità
                if (gameState.invincible) {
                    gameState.invincibilityTimer--;
                    if (gameState.invincibilityTimer <= 0) {
                        gameState.invincible = false;
                    }
                }
            }

            collectRing(ring) {
                gameState.rings++;
                gameState.consecutiveRings++;
                gameState.score += GAME_CONFIG.ringValue;

                // Crea particelle
                for (let i = 0; i < 10; i++) {
                    this.particles.push(new Particle(ring.x, ring.y, 'ring'));
                }

                // Controlla boost
                if (gameState.consecutiveRings >= 10 && !gameState.boosting) {
                    this.activateBoost();
                }

                // Effetto sonoro
                this.playCollectSound();
            }

            activateBoost() {
                gameState.boosting = true;
                gameState.boostTimer = GAME_CONFIG.boostDuration;
                gameState.invincible = true;
                gameState.invincibilityTimer = GAME_CONFIG.boostDuration;
                gameState.consecutiveRings = 0;

                // Effetto visivo
                for (let i = 0; i < 20; i++) {
                    this.particles.push(new Particle(this.player.x, this.player.y, 'boost'));
                }
            }

            endBoost() {
                gameState.boosting = false;
                gameState.invincible = false;
                gameState.invincibilityTimer = 0;
            }

            handleCollision() {
                if (gameState.invincible) return;

                gameState.lives--;
                gameState.consecutiveRings = 0;
                gameState.invincible = true;
                gameState.invincibilityTimer = 120; // 2 secondi

                // Particelle di danno
                for (let i = 0; i < 15; i++) {
                    this.particles.push(new Particle(this.player.x, this.player.y, 'damage'));
                }

                // Game Over
                if (gameState.lives <= 0) {
                    this.gameOver();
                }

                // Effetto sonoro
                this.playHitSound();
            }

            gameOver() {
                gameState.running = false;
                gameState.gameOver = true;
            }

            render() {
                // Clear canvas
                ctx.clearRect(0, 0, screenWidth, screenHeight);

                // Disegna sfondo
                this.background.draw();

                // Disegna terreno
                this.drawGround();

                // Disegna elementi di gioco
                this.rings.forEach(ring => ring.draw());
                this.obstacles.forEach(obstacle => obstacle.draw());
                this.particles.forEach(particle => particle.draw());
                this.player.draw();

                // Disegna UI
                this.drawUI();

                // Schermate speciali
                if (!gameState.running && !gameState.gameOver) {
                    this.drawStartScreen();
                } else if (gameState.gameOver) {
                    this.drawGameOverScreen();
                }
            }

            drawGround() {
                const groundY = screenHeight - 100;
                
                // Linea principale
                ctx.strokeStyle = COLORS.primary;
                ctx.lineWidth = 3;
                ctx.shadowBlur = 20;
                ctx.shadowColor = COLORS.primary;
                ctx.beginPath();
                ctx.moveTo(0, groundY);
                ctx.lineTo(screenWidth, groundY);
                ctx.stroke();

                // Griglia digitale
                ctx.strokeStyle = `${COLORS.primary}33`;
                ctx.lineWidth = 1;
                ctx.shadowBlur = 0;
                
                // Linee verticali
                for (let x = (gameState.distance % 50) * -1; x < screenWidth; x += 50) {
                    ctx.beginPath();
                    ctx.moveTo(x, groundY);
                    ctx.lineTo(x, screenHeight);
                    ctx.stroke();
                }

                // Linee orizzontali
                for (let y = groundY; y < screenHeight; y += 20) {
                    ctx.beginPath();
                    ctx.moveTo(0, y);
                    ctx.lineTo(screenWidth, y);
                    ctx.stroke();
                }
            }

            drawUI() {
                ctx.fillStyle = COLORS.white;
                ctx.font = `bold ${20 * scale}px Arial`;
                ctx.textAlign = 'left';
                ctx.shadowBlur = 10;
                ctx.shadowColor = COLORS.primary;

                // Punteggio
                ctx.fillText(`SCORE: ${gameState.score}`, 20, 40);
                
                // Anelli
                ctx.fillStyle = COLORS.accent;
                ctx.fillText(`RINGS: ${gameState.rings}`, 20, 70);
                
                // Vite
                ctx.fillStyle = COLORS.danger;
                for (let i = 0; i < gameState.lives; i++) {
                    ctx.fillText('❤', 20 + i * 30, 100);
                }

                // Indicatore boost
                if (gameState.boosting) {
                    ctx.fillStyle = COLORS.secondary;
                    ctx.font = `bold ${30 * scale}px Arial`;
                    ctx.textAlign = 'center';
                    ctx.fillText('BOOST MODE!', screenWidth / 2, 80);
                    
                    // Barra boost
                    const barWidth = 200;
                    const barHeight = 10;
                    const barX = (screenWidth - barWidth) / 2;
                    const barY = 100;
                    
                    ctx.strokeStyle = COLORS.secondary;
                    ctx.strokeRect(barX, barY, barWidth, barHeight);
                    
                    ctx.fillStyle = COLORS.secondary;
                    ctx.fillRect(barX, barY, barWidth * (gameState.boostTimer / GAME_CONFIG.boostDuration), barHeight);
                }

                // Indicatore combo anelli
                if (gameState.consecutiveRings > 0 && !gameState.boosting) {
                    ctx.fillStyle = COLORS.accent;
                    ctx.font = `bold ${24 * scale}px Arial`;
                    ctx.textAlign = 'center';
                    ctx.fillText(`COMBO: ${gameState.consecutiveRings}/10`, screenWidth / 2, 140);
                }
            }

            drawStartScreen() {
                // Overlay scuro
                ctx.fillStyle = 'rgba(9, 9, 33, 0.8)';
                ctx.fillRect(0, 0, screenWidth, screenHeight);

                ctx.fillStyle = COLORS.white;
                ctx.textAlign = 'center';
                ctx.shadowBlur = 20;
                ctx.shadowColor = COLORS.primary;

                // Titolo
                ctx.font = `bold ${60 * scale}px Arial`;
                ctx.fillStyle = COLORS.primary;
                ctx.fillText('HEDGEHOG RUSH', screenWidth / 2, screenHeight * 0.3);

                // Sottotitolo
                ctx.font = `${20 * scale}px Arial`;
                ctx.fillStyle = COLORS.secondary;
                ctx.fillText('Corri attraverso il cyberspazio!', screenWidth / 2, screenHeight * 0.4);

                // Istruzioni
                ctx.font = `${16 * scale}px Arial`;
                ctx.fillStyle = COLORS.white;
                
                if (window.matchMedia("(hover: none)").matches) {
                    ctx.fillText('Tocca per saltare', screenWidth / 2, screenHeight * 0.55);
                    ctx.fillText('Doppio tocco per doppio salto', screenWidth / 2, screenHeight * 0.6);
                } else {
                    ctx.fillText('Premi SPAZIO o ↑ per saltare', screenWidth / 2, screenHeight * 0.55);
                    ctx.fillText('Premi di nuovo per doppio salto', screenWidth / 2, screenHeight * 0.6);
                }

                // Obiettivi
                ctx.fillStyle = COLORS.accent;
                ctx.fillText('Raccogli 10 anelli per attivare il BOOST!', screenWidth / 2, screenHeight * 0.7);

                // Call to action
                ctx.font = `bold ${24 * scale}px Arial`;
                ctx.fillStyle = COLORS.white;
                const pulse = Math.sin(Date.now() * 0.005) * 0.2 + 0.8;
                ctx.globalAlpha = pulse;
                ctx.fillText('[ INIZIA ]', screenWidth / 2, screenHeight * 0.85);
                ctx.globalAlpha = 1;
            }

            drawGameOverScreen() {
                // Overlay scuro
                ctx.fillStyle = 'rgba(9, 9, 33, 0.9)';
                ctx.fillRect(0, 0, screenWidth, screenHeight);

                ctx.textAlign = 'center';
                ctx.shadowBlur = 20;

                // Game Over
                ctx.font = `bold ${60 * scale}px Arial`;
                ctx.fillStyle = COLORS.danger;
                ctx.shadowColor = COLORS.danger;
                ctx.fillText('GAME OVER', screenWidth / 2, screenHeight * 0.3);

                // Statistiche
                ctx.font = `${24 * scale}px Arial`;
                ctx.fillStyle = COLORS.white;
                ctx.shadowColor = COLORS.primary;
                
                ctx.fillText(`Punteggio Finale: ${gameState.score}`, screenWidth / 2, screenHeight * 0.45);
                ctx.fillText(`Anelli Raccolti: ${gameState.rings}`, screenWidth / 2, screenHeight * 0.52);
                ctx.fillText(`Distanza: ${Math.floor(gameState.distance / 100)}m`, screenWidth / 2, screenHeight * 0.59);

                // Riprova
                ctx.font = `bold ${24 * scale}px Arial`;
                const pulse = Math.sin(Date.now() * 0.005) * 0.2 + 0.8;
                ctx.globalAlpha = pulse;
                ctx.fillText('[ RIPROVA ]', screenWidth / 2, screenHeight * 0.75);
                ctx.globalAlpha = 1;

                // Credits
                ctx.font = `${14 * scale}px Arial`;
                ctx.fillStyle = COLORS.secondary;
                ctx.fillText('G Tech Arcade', screenWidth / 2, screenHeight * 0.9);
            }

            showStartScreen() {
                this.render();
            }

            // Effetti sonori sintetizzati
            playCollectSound() {
                if (window.AudioContext || window.webkitAudioContext) {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);

                    oscillator.frequency.setValueAtTime(600, audioCtx.currentTime);
                    oscillator.frequency.exponentialRampToValueAtTime(1200, audioCtx.currentTime + 0.1);
                    
                    gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);

                    oscillator.start(audioCtx.currentTime);
                    oscillator.stop(audioCtx.currentTime + 0.1);
                }
            }

            playHitSound() {
                if (window.AudioContext || window.webkitAudioContext) {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioCtx.destination);

                    oscillator.type = 'sawtooth';
                    oscillator.frequency.setValueAtTime(200, audioCtx.currentTime);
                    oscillator.frequency.exponentialRampToValueAtTime(50, audioCtx.currentTime + 0.2);
                    
                    gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);

                    oscillator.start(audioCtx.currentTime);
                    oscillator.stop(audioCtx.currentTime + 0.2);
                }
            }
        }

        // ===== INIZIALIZZAZIONE =====
        window.addEventListener('load', () => {
            const game = new Game();
        });

        // Previeni zoom su doppio tap mobile
        document.addEventListener('gesturestart', (e) => e.preventDefault());
    </script>
</body>
</html>
