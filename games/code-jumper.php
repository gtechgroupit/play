<?php
// Code Jumper - G Tech Arcade
// © G Tech Group - https://www.gtechgroup.it
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Code Jumper - G Tech Arcade</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #1e1e1e;
            overflow: hidden;
            font-family: 'Consolas', 'Monaco', monospace;
            touch-action: none;
        }
        
        #gameCanvas {
            display: block;
            width: 100vw;
            height: 100vh;
            cursor: crosshair;
        }
        
        #mobileControls {
            position: fixed;
            bottom: 20px;
            width: 100%;
            display: none;
            pointer-events: none;
            z-index: 10;
        }
        
        .control-button {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            pointer-events: all;
            touch-action: none;
        }
        
        #joystick {
            left: 20px;
            width: 120px;
            height: 120px;
        }
        
        #joystickKnob {
            position: absolute;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: none;
        }
        
        #jumpButton {
            right: 20px;
            width: 80px;
            height: 80px;
            background: rgba(0, 255, 128, 0.2);
            border-color: rgba(0, 255, 128, 0.5);
        }
        
        #jumpButton:active {
            background: rgba(0, 255, 128, 0.4);
        }
        
        /* Responsive per dispositivi mobile */
        @media (max-width: 768px), (pointer: coarse) {
            #mobileControls {
                display: block;
            }
        }
    </style>
</head>
<body>
    <canvas id="gameCanvas"></canvas>
    
    <!-- Controlli mobile -->
    <div id="mobileControls">
        <div id="joystick" class="control-button">
            <div id="joystickKnob"></div>
        </div>
        <div id="jumpButton" class="control-button"></div>
    </div>
    
    <script>
        // ===== CONFIGURAZIONE GLOBALE =====
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        
        // Polyfill per roundRect se non supportato
        if (!ctx.roundRect) {
            CanvasRenderingContext2D.prototype.roundRect = function(x, y, width, height, radius) {
                this.beginPath();
                this.moveTo(x + radius, y);
                this.lineTo(x + width - radius, y);
                this.arc(x + width - radius, y + radius, radius, -Math.PI/2, 0);
                this.lineTo(x + width, y + height - radius);
                this.arc(x + width - radius, y + height - radius, radius, 0, Math.PI/2);
                this.lineTo(x + radius, y + height);
                this.arc(x + radius, y + height - radius, radius, Math.PI/2, Math.PI);
                this.lineTo(x, y + radius);
                this.arc(x + radius, y + radius, radius, Math.PI, Math.PI*1.5);
                this.closePath();
            };
        }
        
        // Variabili di gioco
        let gameState = 'playing'; // 'playing', 'gameOver', 'paused'
        let score = 0;
        let highScore = 0; // High score for this session
        let lives = 3;
        let currentLanguage = 'JavaScript';
        let level = 1;
        let languages = ['JavaScript', 'Python', 'C++', 'Java', 'TypeScript'];
        
        // Dimensioni canvas responsive
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        // ===== PLAYER =====
        const player = {
            x: 100,
            y: 300,
            width: 30,
            height: 40,
            velocityX: 0,
            velocityY: 0,
            speed: 5,
            jumpPower: -15,
            onGround: false,
            canDoubleJump: true,
            color: '#00ff88',
            // Power-up states
            hasShield: false,
            speedBoost: 1,
            magnetActive: false,
            timeSlowActive: false,
            powerUpTimers: {},
            // Animation
            direction: 1,
            squash: 1,
            stretch: 1,
            rotation: 0
        };
        
        // ===== FISICA =====
        const physics = {
            gravity: 0.8,
            friction: 0.8,
            maxVelocityY: 20
        };
        
        // ===== CAMERA =====
        const camera = {
            x: 0,
            y: 0
        };
        
        // ===== PLATFORMS =====
        const platforms = [];
        const codeTypes = [
            { text: 'function()', color: '#569cd6', width: 150 },
            { text: 'if(condition)', color: '#c586c0', width: 140 },
            { text: 'for(let i=0)', color: '#c586c0', width: 140 },
            { text: 'while(true)', color: '#c586c0', width: 130 },
            { text: 'const x = 0', color: '#4ec9b0', width: 120 },
            { text: 'return true', color: '#c586c0', width: 120 },
            { text: 'class App', color: '#4ec9b0', width: 110 },
            { text: 'try {', color: '#c586c0', width: 80 },
            { text: 'catch(e)', color: '#c586c0', width: 100 }
        ];
        
        // ===== ENEMIES (BUGS) =====
        const enemies = [];
        
        // ===== COLLECTIBLES =====
        const collectibles = [];
        
        // ===== PARTICLES =====
        const particles = [];
        
        // ===== POWER-UPS =====
        const powerUps = [];
        const powerUpTypes = {
            speed: { color: '#00ffff', duration: 5000, emoji: '⚡' },
            shield: { color: '#ff00ff', duration: 8000, emoji: '🛡️' },
            magnet: { color: '#ffff00', duration: 6000, emoji: '🧲' },
            slowTime: { color: '#ff8800', duration: 4000, emoji: '⏰' }
        };
        
        // ===== SOUND SYSTEM =====
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const sounds = {
            jump: () => playTone(200, 0.1, 'square'),
            collect: () => playTone(800, 0.1, 'sine'),
            powerUp: () => playTone(400, 0.3, 'triangle'),
            hurt: () => playTone(100, 0.2, 'sawtooth'),
            land: () => playTone(150, 0.05, 'square')
        };
        
        function playTone(frequency, duration, type = 'sine') {
            try {
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.value = frequency;
                oscillator.type = type;
                
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duration);
                
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + duration);
            } catch (e) {
                // Silence audio errors
            }
        }
        
        // ===== COMBO SYSTEM =====
        let combo = 0;
        let comboTimer = 0;
        let maxCombo = 0;
        
        // ===== BACKGROUND LAYERS =====
        const backgroundLayers = [];
        
        // ===== CHECKPOINT =====
        let checkpoint = { x: 100, y: 300 };
        
        // ===== ANIMATIONS =====
        const animations = {
            playerFrame: 0,
            playerAnimation: 'idle'
        };
        
        // Generate initial level
        function generateLevel() {
            platforms.length = 0;
            enemies.length = 0;
            collectibles.length = 0;
            powerUps.length = 0;
            backgroundLayers.length = 0;
            
            // Generate background layers
            for (let i = 0; i < 3; i++) {
                backgroundLayers.push({
                    elements: [],
                    speed: 0.2 + i * 0.3
                });
                
                // Add background code elements
                for (let j = 0; j < 20; j++) {
                    backgroundLayers[i].elements.push({
                        x: Math.random() * 2000,
                        y: Math.random() * canvas.height,
                        text: ['//TODO', 'console.log()', '/* comment */', 'import', 'export'][Math.floor(Math.random() * 5)],
                        size: 12 + i * 4,
                        opacity: 0.1 + i * 0.05
                    });
                }
            }
            
            // Starting platform
            platforms.push({
                x: 50,
                y: 400,
                width: 200,
                height: 20,
                type: codeTypes[0],
                isSolid: true,
                isCheckpoint: true
            });
            
            // Difficulty scaling
            const enemyChance = 0.3 + (level * 0.05);
            const errorZoneChance = 0.2 + (level * 0.03);
            
            // Generate platforms
            for (let i = 0; i < 50 + level * 10; i++) {
                const type = codeTypes[Math.floor(Math.random() * codeTypes.length)];
                const platform = {
                    x: 250 + i * 200 + Math.random() * 100,
                    y: 200 + Math.random() * 300,
                    width: type.width,
                    height: 20,
                    type: type,
                    isSolid: true,
                    isErrorZone: Math.random() < errorZoneChance,
                    errorTimer: 0
                };
                
                // Some platforms are try/catch blocks (safe zones)
                if (type.text.includes('try') || type.text.includes('catch')) {
                    platform.isSafeZone = true;
                    platform.type.color = '#00ff88';
                }
                
                // Add checkpoints every 10 platforms
                if (i % 10 === 0 && i > 0) {
                    platform.isCheckpoint = true;
                }
                
                platforms.push(platform);
                
                // Add collectibles on some platforms
                if (Math.random() < 0.4) {
                    collectibles.push({
                        x: platform.x + platform.width / 2 - 10,
                        y: platform.y - 30,
                        width: 20,
                        height: 20,
                        type: Math.random() < 0.7 ? 'code' : 'compiler',
                        collected: false,
                        float: 0
                    });
                }
                
                // Add power-ups rarely
                if (Math.random() < 0.1) {
                    const powerUpType = Object.keys(powerUpTypes)[Math.floor(Math.random() * Object.keys(powerUpTypes).length)];
                    powerUps.push({
                        x: platform.x + platform.width / 2 - 15,
                        y: platform.y - 50,
                        width: 30,
                        height: 30,
                        type: powerUpType,
                        collected: false,
                        float: 0,
                        rotation: 0
                    });
                }
                
                // Add enemies near some platforms
                if (Math.random() < enemyChance && !platform.isSafeZone) {
                    enemies.push({
                        x: platform.x,
                        y: platform.y - 40,
                        width: 30,
                        height: 30,
                        velocityX: (1 + Math.random() * 2) * (1 + level * 0.1),
                        platformIndex: i + 1,
                        type: ['🐛', '🪲', '🦟'][Math.floor(Math.random() * 3)],
                        amplitude: 20 + Math.random() * 30,
                        baseY: platform.y - 40
                    });
                }
            }
            
            // Add boss at the end of level
            if (level % 5 === 0) {
                const bossX = platforms[platforms.length - 5].x + 300;
                enemies.push({
                    x: bossX,
                    y: 200,
                    width: 80,
                    height: 80,
                    velocityX: 2,
                    velocityY: 0,
                    isBoss: true,
                    health: 5,
                    type: '🦠',
                    pattern: 'sine',
                    amplitude: 100,
                    baseY: 200
                });
            }
            
            // Add "undefined" zones (death pits)
            for (let i = 0; i < 10 + level * 2; i++) {
                platforms.push({
                    x: 500 + i * 400,
                    y: canvas.height - 50,
                    width: 150,
                    height: 50,
                    type: { text: 'undefined', color: '#ff0000' },
                    isDeathZone: true
                });
            }
        }
        
        // Collision detection
        function checkCollision(rect1, rect2) {
            return rect1.x < rect2.x + rect2.width &&
                   rect1.x + rect1.width > rect2.x &&
                   rect1.y < rect2.y + rect2.height &&
                   rect1.y + rect1.height > rect2.y;
        }
        
        // Create particle effect
        function createParticles(x, y, color, count = 10) {
            for (let i = 0; i < count; i++) {
                particles.push({
                    x: x,
                    y: y,
                    velocityX: (Math.random() - 0.5) * 8,
                    velocityY: Math.random() * -5 - 2,
                    size: Math.random() * 4 + 2,
                    color: color,
                    life: 1
                });
            }
        }
        
        // ===== INPUT HANDLING =====
        const keys = {};
        
        // Keyboard controls
        window.addEventListener('keydown', (e) => {
            keys[e.key] = true;
            
            // Prevent spacebar scroll
            if (e.key === ' ') {
                e.preventDefault();
            }
            
            // Pause game with Escape or P
            if ((e.key === 'Escape' || e.key === 'p' || e.key === 'P') && gameState === 'playing') {
                gameState = 'paused';
            } else if ((e.key === 'Escape' || e.key === 'p' || e.key === 'P') && gameState === 'paused') {
                gameState = 'playing';
            }
        });
        
        window.addEventListener('keyup', (e) => {
            keys[e.key] = false;
        });
        
        // Mobile controls
        let joystickActive = false;
        let joystickX = 0;
        
        const joystick = document.getElementById('joystick');
        const joystickKnob = document.getElementById('joystickKnob');
        const jumpButton = document.getElementById('jumpButton');
        
        // Joystick handling
        function handleJoystickStart(e) {
            joystickActive = true;
            handleJoystickMove(e);
        }
        
        function handleJoystickMove(e) {
            if (!joystickActive) return;
            
            const touch = e.touches ? e.touches[0] : e;
            const rect = joystick.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            let deltaX = touch.clientX - centerX;
            let deltaY = touch.clientY - centerY;
            
            // Limit to joystick radius
            const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
            const maxDistance = rect.width / 2 - 25;
            
            if (distance > maxDistance) {
                deltaX = (deltaX / distance) * maxDistance;
                deltaY = (deltaY / distance) * maxDistance;
            }
            
            joystickKnob.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
            joystickX = deltaX / maxDistance;
        }
        
        function handleJoystickEnd() {
            joystickActive = false;
            joystickX = 0;
            joystickKnob.style.transform = 'translate(-50%, -50%)';
        }
        
        joystick.addEventListener('touchstart', handleJoystickStart);
        joystick.addEventListener('touchmove', handleJoystickMove);
        joystick.addEventListener('touchend', handleJoystickEnd);
        joystick.addEventListener('mousedown', handleJoystickStart);
        window.addEventListener('mousemove', handleJoystickMove);
        window.addEventListener('mouseup', handleJoystickEnd);
        
        // Jump button
        jumpButton.addEventListener('touchstart', (e) => {
            e.preventDefault();
            jump();
        });
        jumpButton.addEventListener('mousedown', (e) => {
            e.preventDefault();
            jump();
        });
        
        // Restart game
        function restartGame() {
            gameState = 'playing';
            score = 0;
            lives = 3;
            level = 1;
            combo = 0;
            maxCombo = 0;
            comboTimer = 0;
            currentLanguage = 'JavaScript';
            player.x = 100;
            player.y = 300;
            player.velocityX = 0;
            player.velocityY = 0;
            player.hasShield = false;
            player.speedBoost = 1;
            player.magnetActive = false;
            player.timeSlowActive = false;
            player.powerUpTimers = {};
            player.squash = 1;
            player.stretch = 1;
            checkpoint = { x: 100, y: 300 };
            particles.length = 0;
            generateLevel();
        }
        
        // Handle restart input
        window.addEventListener('keydown', (e) => {
            if (gameState === 'gameOver' && e.key === ' ') {
                restartGame();
            }
        });
        
        canvas.addEventListener('click', () => {
            if (gameState === 'gameOver') {
                restartGame();
            }
        });
        
        // Initialize game
        generateLevel();
        
        // ===== GAME LOOP =====
        function gameLoop() {
            update();
            render();
            requestAnimationFrame(gameLoop);
        }
        
        function update() {
            if (gameState !== 'playing') return;
            
            // Time factor for slow-time power-up
            const timeFactor = player.timeSlowActive ? 0.5 : 1;
            
            // Player movement
            if (keys['ArrowLeft'] || keys['a'] || keys['A'] || joystickX < -0.3) {
                player.velocityX = -player.speed * player.speedBoost;
                player.direction = -1;
                animations.playerAnimation = 'run';
            } else if (keys['ArrowRight'] || keys['d'] || keys['D'] || joystickX > 0.3) {
                player.velocityX = player.speed * player.speedBoost;
                player.direction = 1;
                animations.playerAnimation = 'run';
            } else {
                player.velocityX *= physics.friction;
                animations.playerAnimation = 'idle';
            }
            
            // Jump
            if (keys['ArrowUp'] || keys[' '] || keys['w'] || keys['W']) {
                jump();
                keys['ArrowUp'] = false;
                keys[' '] = false;
                keys['w'] = false;
                keys['W'] = false;
            }
            
            // Apply physics
            player.velocityY += physics.gravity * timeFactor;
            player.velocityY = Math.min(player.velocityY, physics.maxVelocityY);
            
            // Update position
            player.x += player.velocityX * timeFactor;
            player.y += player.velocityY * timeFactor;
            
            // Animation squash and stretch
            if (player.velocityY > 5) {
                player.squash = 0.8;
                player.stretch = 1.2;
            } else if (player.velocityY < -5) {
                player.squash = 1.2;
                player.stretch = 0.8;
            } else {
                player.squash += (1 - player.squash) * 0.2;
                player.stretch += (1 - player.stretch) * 0.2;
            }
            
            // Platform collision
            player.onGround = false;
            for (let platform of platforms) {
                if (checkCollision(player, platform)) {
                    // Checkpoint
                    if (platform.isCheckpoint && platform.isSolid) {
                        checkpoint = { x: platform.x + 50, y: platform.y - 50 };
                        createParticles(platform.x + platform.width/2, platform.y, '#00ff88', 20);
                    }
                    
                    // Death zone check
                    if (platform.isDeathZone) {
                        if (!player.hasShield) {
                            loseLife();
                        } else {
                            player.hasShield = false;
                            player.powerUpTimers.shield = 0;
                            createParticles(player.x + player.width/2, player.y + player.height/2, '#ff00ff', 30);
                        }
                        continue;
                    }
                    
                    // Error zone mechanics
                    if (platform.isErrorZone && platform.isSolid) {
                        platform.errorTimer += 0.02 * timeFactor;
                        if (platform.errorTimer > 1) {
                            platform.isSolid = false;
                            createParticles(platform.x + platform.width/2, platform.y, '#ff0000', 20);
                        }
                    }
                    
                    // Normal collision
                    if (platform.isSolid) {
                        // Landing on top
                        if (player.velocityY > 0 && player.y < platform.y) {
                            player.y = platform.y - player.height;
                            player.velocityY = 0;
                            player.onGround = true;
                            player.canDoubleJump = true;
                            sounds.land();
                            
                            // Reset animations
                            player.squash = 0.7;
                            player.stretch = 1.3;
                        }
                    }
                }
            }
            
            // Enemy collision and updates
            for (let i = enemies.length - 1; i >= 0; i--) {
                const enemy = enemies[i];
                
                // Boss behavior
                if (enemy.isBoss) {
                    enemy.x += enemy.velocityX * timeFactor;
                    enemy.y = enemy.baseY + Math.sin(Date.now() * 0.002) * enemy.amplitude;
                    
                    // Boss bounds
                    if (enemy.x < camera.x - 100 || enemy.x > camera.x + canvas.width + 100) {
                        enemy.velocityX *= -1;
                    }
                }
                
                if (checkCollision(player, enemy)) {
                    if (!player.hasShield) {
                        if (enemy.isBoss) {
                            enemy.health--;
                            if (enemy.health <= 0) {
                                enemies.splice(i, 1);
                                score += 2000;
                                createParticles(enemy.x + enemy.width/2, enemy.y + enemy.height/2, '#ffff00', 50);
                                continue;
                            }
                        }
                        loseLife();
                        break;
                    } else {
                        // Shield protects and destroys normal enemies
                        if (!enemy.isBoss) {
                            enemies.splice(i, 1);
                            score += 50;
                            createParticles(enemy.x + enemy.width/2, enemy.y + enemy.height/2, '#ff00ff', 20);
                        }
                    }
                }
                
                // Update normal enemy movement
                if (!enemy.isBoss) {
                    enemy.x += enemy.velocityX * timeFactor;
                    
                    // Enemy platform bounds
                    const platform = platforms[enemy.platformIndex];
                    if (platform) {
                        if (enemy.x < platform.x || enemy.x + enemy.width > platform.x + platform.width) {
                            enemy.velocityX *= -1;
                        }
                        
                        // Floating motion
                        enemy.y = enemy.baseY + Math.sin(Date.now() * 0.003) * enemy.amplitude;
                    }
                }
            }
            
            // Collectible collision with magnet effect
            for (let item of collectibles) {
                if (!item.collected) {
                    // Magnet attraction
                    if (player.magnetActive) {
                        const dx = (player.x + player.width/2) - (item.x + 10);
                        const dy = (player.y + player.height/2) - (item.y + 10);
                        const distance = Math.sqrt(dx * dx + dy * dy);
                        
                        if (distance < 150) {
                            item.x += dx * 0.1;
                            item.y += dy * 0.1;
                        }
                    }
                    
                    if (checkCollision(player, item)) {
                        item.collected = true;
                        sounds.collect();
                        
                        if (item.type === 'code') {
                            score += 100 * (combo + 1);
                            combo++;
                            comboTimer = 120; // 2 seconds at 60fps
                            createParticles(item.x + 10, item.y + 10, '#00ff88', 15);
                        } else if (item.type === 'compiler') {
                            score += 500 * (combo + 1);
                            lives = Math.min(lives + 1, 5);
                            createParticles(item.x + 10, item.y + 10, '#ffff00', 20);
                        }
                        
                        maxCombo = Math.max(maxCombo, combo);
                    }
                    
                    // Floating animation
                    item.float = Math.sin(Date.now() * 0.005) * 5;
                }
            }
            
            // Power-up collision
            for (let i = powerUps.length - 1; i >= 0; i--) {
                const powerUp = powerUps[i];
                if (!powerUp.collected && checkCollision(player, powerUp)) {
                    powerUp.collected = true;
                    sounds.powerUp();
                    
                    // Apply power-up effect
                    const type = powerUpTypes[powerUp.type];
                    player.powerUpTimers[powerUp.type] = type.duration;
                    
                    switch(powerUp.type) {
                        case 'speed':
                            player.speedBoost = 1.5;
                            break;
                        case 'shield':
                            player.hasShield = true;
                            break;
                        case 'magnet':
                            player.magnetActive = true;
                            break;
                        case 'slowTime':
                            player.timeSlowActive = true;
                            break;
                    }
                    
                    createParticles(powerUp.x + 15, powerUp.y + 15, type.color, 25);
                    powerUps.splice(i, 1);
                }
                
                // Rotation animation
                if (!powerUp.collected) {
                    powerUp.rotation += 0.05;
                    powerUp.float = Math.sin(Date.now() * 0.003) * 8;
                }
            }
            
            // Update power-up timers
            for (let [key, timer] of Object.entries(player.powerUpTimers)) {
                if (timer > 0) {
                    player.powerUpTimers[key] -= 16; // ~16ms per frame
                    if (player.powerUpTimers[key] <= 0) {
                        // Remove power-up effect
                        switch(key) {
                            case 'speed':
                                player.speedBoost = 1;
                                break;
                            case 'shield':
                                player.hasShield = false;
                                break;
                            case 'magnet':
                                player.magnetActive = false;
                                break;
                            case 'slowTime':
                                player.timeSlowActive = false;
                                break;
                        }
                    }
                }
            }
            
            // Update combo timer
            if (comboTimer > 0) {
                comboTimer--;
            } else {
                combo = 0;
            }
            
            // Update particles
            for (let i = particles.length - 1; i >= 0; i--) {
                const p = particles[i];
                p.x += p.velocityX * timeFactor;
                p.y += p.velocityY * timeFactor;
                p.velocityY += 0.3 * timeFactor;
                p.life -= 0.02;
                
                if (p.life <= 0) {
                    particles.splice(i, 1);
                }
            }
            
            // Update background layers
            for (let layer of backgroundLayers) {
                for (let element of layer.elements) {
                    element.x -= layer.speed * player.velocityX * 0.1;
                    if (element.x < -200) element.x += 2200;
                    if (element.x > 2200) element.x -= 2200;
                }
            }
            
            // Camera follow player with smoothing
            camera.x += (player.x - canvas.width / 2 - camera.x) * 0.1;
            camera.y += (player.y - canvas.height / 2 - camera.y) * 0.1;
            
            // Limit camera
            camera.x = Math.max(0, camera.x);
            camera.y = Math.max(-200, Math.min(camera.y, 500));
            
            // Fall check
            if (player.y > canvas.height + 100) {
                loseLife();
            }
            
            // Boundary check
            player.x = Math.max(0, player.x);
            
            // Level progression check
            if (player.x > platforms[platforms.length - 5].x) {
                nextLevel();
            }
            
            // Update animation frame
            animations.playerFrame = (animations.playerFrame + 0.2) % 4;
        }
        
        function nextLevel() {
            level++;
            currentLanguage = languages[level % languages.length];
            score += 1000;
            
            // Bonus score for remaining lives
            score += lives * 200;
            
            // Victory sound
            playTone(600, 0.1, 'sine');
            setTimeout(() => playTone(800, 0.1, 'sine'), 100);
            setTimeout(() => playTone(1000, 0.2, 'sine'), 200);
            
            // Reset player position
            player.x = 100;
            player.y = 300;
            player.velocityX = 0;
            player.velocityY = 0;
            checkpoint = { x: 100, y: 300 };
            
            // Clear all power-ups but give bonus
            for (let key in player.powerUpTimers) {
                if (player.powerUpTimers[key] > 0) {
                    score += 100;
                }
            }
            player.powerUpTimers = {};
            player.hasShield = false;
            player.speedBoost = 1;
            player.magnetActive = false;
            player.timeSlowActive = false;
            
            generateLevel();
            createParticles(canvas.width/2, canvas.height/2, '#00ff88', 50);
            
            // Level announcement
            particles.push({
                x: canvas.width/2,
                y: canvas.height/2,
                velocityX: 0,
                velocityY: -1,
                size: 200,
                color: '#ffffff',
                life: 2,
                text: `Level ${level}: ${currentLanguage}`,
                isText: true
            });
        }
        
        function loseLife() {
            lives--;
            createParticles(player.x + player.width/2, player.y + player.height/2, '#ff0000', 30);
            
            if (lives <= 0) {
                gameState = 'gameOver';
            } else {
                // Respawn at last safe platform
                player.x = 100;
                player.y = 300;
                player.velocityX = 0;
                player.velocityY = 0;
            }
        }
        
        function jump() {
            if (player.onGround) {
                player.velocityY = player.jumpPower;
                player.onGround = false;
            } else if (player.canDoubleJump) {
                player.velocityY = player.jumpPower * 0.8;
                player.canDoubleJump = false;
            }
        }
        
        function render() {
            // Clear canvas
            ctx.fillStyle = '#1e1e1e';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Draw parallax background layers
            for (let layer of backgroundLayers) {
                ctx.save();
                ctx.globalAlpha = layer.elements[0]?.opacity || 0.1;
                ctx.fillStyle = '#ffffff';
                ctx.font = `${layer.elements[0]?.size || 14}px Consolas, monospace`;
                
                for (let element of layer.elements) {
                    ctx.fillText(element.text, element.x - camera.x * layer.speed, element.y - camera.y * layer.speed * 0.5);
                }
                ctx.restore();
            }
            
            // Draw code background pattern
            ctx.save();
            ctx.globalAlpha = 0.05;
            ctx.fillStyle = '#ffffff';
            ctx.font = '14px Consolas, monospace';
            for (let i = 0; i < 50; i++) {
                ctx.fillText(`${i + 1}`, 10 - camera.x * 0.1, i * 20 - camera.y * 0.1);
            }
            ctx.restore();
            
            // Save context for camera transform
            ctx.save();
            ctx.translate(-camera.x, -camera.y);
            
            // Draw platforms
            for (let platform of platforms) {
                if (platform.isDeathZone) {
                    // Death zone (undefined)
                    ctx.fillStyle = 'rgba(255, 0, 0, 0.3)';
                    ctx.fillRect(platform.x, platform.y, platform.width, platform.height);
                    
                    ctx.fillStyle = '#ff0000';
                    ctx.font = 'bold 16px Consolas, monospace';
                    ctx.fillText(platform.type.text, platform.x + 10, platform.y + 25);
                } else if (platform.isSolid) {
                    // Normal platform
                    ctx.fillStyle = platform.isErrorZone ? 
                        `rgba(255, 0, 0, ${0.3 + platform.errorTimer * 0.7})` : 
                        platform.type.color;
                    
                    // Platform with rounded corners
                    ctx.beginPath();
                    ctx.roundRect(platform.x, platform.y, platform.width, platform.height, 5);
                    ctx.fill();
                    
                    // Code text
                    ctx.fillStyle = '#ffffff';
                    ctx.font = '14px Consolas, monospace';
                    ctx.fillText(platform.type.text, platform.x + 10, platform.y + platform.height - 5);
                    
                    // Safe zone glow
                    if (platform.isSafeZone) {
                        ctx.strokeStyle = '#00ff88';
                        ctx.lineWidth = 2;
                        ctx.stroke();
                    }
                    
                    // Checkpoint beacon
                    if (platform.isCheckpoint) {
                        ctx.save();
                        ctx.fillStyle = '#00ff88';
                        ctx.globalAlpha = 0.5 + Math.sin(Date.now() * 0.005) * 0.3;
                        ctx.beginPath();
                        ctx.arc(platform.x + platform.width/2, platform.y - 20, 10, 0, Math.PI * 2);
                        ctx.fill();
                        ctx.restore();
                    }
                }
            }
            
            // Draw collectibles
            for (let item of collectibles) {
                if (!item.collected) {
                    ctx.save();
                    ctx.translate(item.x + 10, item.y + 10 + item.float);
                    
                    if (item.type === 'code') {
                        // Code fragment
                        ctx.fillStyle = '#00ff88';
                        ctx.beginPath();
                        ctx.moveTo(0, -8);
                        ctx.lineTo(8, 0);
                        ctx.lineTo(0, 8);
                        ctx.lineTo(-8, 0);
                        ctx.closePath();
                        ctx.fill();
                        
                        ctx.fillStyle = '#ffffff';
                        ctx.font = 'bold 10px Consolas';
                        ctx.fillText('{ }', -6, 3);
                    } else {
                        // Compiler
                        ctx.fillStyle = '#ffff00';
                        ctx.beginPath();
                        ctx.arc(0, 0, 10, 0, Math.PI * 2);
                        ctx.fill();
                        
                        ctx.fillStyle = '#000000';
                        ctx.font = 'bold 12px Consolas';
                        ctx.fillText('C', -5, 4);
                    }
                    
                    ctx.restore();
                }
            }
            
            // Draw power-ups
            for (let powerUp of powerUps) {
                if (!powerUp.collected) {
                    ctx.save();
                    ctx.translate(powerUp.x + 15, powerUp.y + 15 + powerUp.float);
                    ctx.rotate(powerUp.rotation);
                    
                    // Power-up glow
                    const type = powerUpTypes[powerUp.type];
                    ctx.shadowColor = type.color;
                    ctx.shadowBlur = 20;
                    
                    // Power-up body
                    ctx.fillStyle = type.color;
                    ctx.beginPath();
                    ctx.arc(0, 0, 15, 0, Math.PI * 2);
                    ctx.fill();
                    
                    // Power-up icon
                    ctx.font = '20px Arial';
                    ctx.fillText(type.emoji, -10, 7);
                    
                    ctx.restore();
                }
            }
            
            // Draw enemies
            for (let enemy of enemies) {
                ctx.save();
                ctx.translate(enemy.x + enemy.width/2, enemy.y + enemy.height/2);
                
                if (enemy.isBoss) {
                    // Boss rendering
                    ctx.scale(1 + Math.sin(Date.now() * 0.005) * 0.1, 1 + Math.cos(Date.now() * 0.005) * 0.1);
                    ctx.rotate(Date.now() * 0.001);
                    
                    // Boss health bar
                    ctx.fillStyle = '#ff0000';
                    ctx.fillRect(-40, -50, 80, 5);
                    ctx.fillStyle = '#00ff00';
                    ctx.fillRect(-40, -50, (enemy.health / 5) * 80, 5);
                    
                    // Boss body
                    ctx.font = '60px Arial';
                    ctx.fillText(enemy.type, -30, 20);
                } else {
                    // Normal enemy
                    ctx.rotate(Date.now() * 0.002);
                    
                    // Bug body
                    ctx.font = '24px Arial';
                    ctx.fillText(enemy.type, -12, 8);
                }
                
                // Glitch effect
                if (Math.random() < 0.1) {
                    ctx.fillStyle = 'rgba(255, 0, 0, 0.5)';
                    ctx.fillRect(-enemy.width/2, -enemy.height/2, enemy.width, enemy.height);
                }
                
                ctx.restore();
            }
            
            // Draw player
            ctx.save();
            ctx.translate(player.x + player.width/2, player.y + player.height/2);
            ctx.scale(player.squash * player.direction, player.stretch);
            
            // Shield effect
            if (player.hasShield) {
                ctx.strokeStyle = '#ff00ff';
                ctx.lineWidth = 3;
                ctx.globalAlpha = 0.5 + Math.sin(Date.now() * 0.01) * 0.3;
                ctx.beginPath();
                ctx.arc(0, 0, 25, 0, Math.PI * 2);
                ctx.stroke();
                ctx.globalAlpha = 1;
            }
            
            // Speed effect
            if (player.speedBoost > 1) {
                ctx.fillStyle = '#00ffff';
                ctx.globalAlpha = 0.3;
                for (let i = 1; i < 4; i++) {
                    ctx.fillRect(-player.width/2 - i * 5 * player.direction, -player.height/2, player.width * 0.8, player.height);
                }
                ctx.globalAlpha = 1;
            }
            
            // Player body with gradient
            const gradient = ctx.createLinearGradient(0, -player.height/2, 0, player.height/2);
            gradient.addColorStop(0, player.color);
            gradient.addColorStop(1, '#005533');
            ctx.fillStyle = gradient;
            
            ctx.beginPath();
            ctx.moveTo(0, -player.height/2);
            ctx.lineTo(player.width/2, player.height/2);
            ctx.lineTo(-player.width/2, player.height/2);
            ctx.closePath();
            ctx.fill();
            
            // Digital effect
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.stroke();
            
            // Time slow effect
            if (player.timeSlowActive) {
                ctx.strokeStyle = '#ff8800';
                ctx.lineWidth = 3;
                ctx.setLineDash([5, 5]);
                ctx.beginPath();
                ctx.arc(0, 0, 30, 0, Math.PI * 2);
                ctx.stroke();
                ctx.setLineDash([]);
            }
            
            ctx.restore();
            
            // Draw particles
            for (let p of particles) {
                ctx.globalAlpha = p.life;
                
                if (p.isText) {
                    // Text particles (level announcements)
                    ctx.fillStyle = p.color;
                    ctx.font = 'bold 32px Consolas, monospace';
                    ctx.textAlign = 'center';
                    ctx.fillText(p.text, p.x, p.y);
                    ctx.textAlign = 'left';
                } else {
                    // Normal particles
                    ctx.fillStyle = p.color;
                    ctx.fillRect(p.x - p.size/2, p.y - p.size/2, p.size, p.size);
                }
            }
            ctx.globalAlpha = 1;
            
            // Restore context
            ctx.restore();
            
            // Draw UI (not affected by camera)
            drawUI();
        }
        
        function drawUI() {
            // UI Background
            ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
            ctx.fillRect(10, 10, 250, 140);
            
            ctx.strokeStyle = '#00ff88';
            ctx.lineWidth = 2;
            ctx.strokeRect(10, 10, 250, 140);
            
            // UI Text
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 18px Consolas, monospace';
            ctx.fillText(`Score: ${score}`, 25, 40);
            
            // Lives as hearts
            ctx.fillText('Lives: ', 25, 70);
            for (let i = 0; i < lives; i++) {
                ctx.fillStyle = '#ff0066';
                ctx.fillText('♥', 90 + i * 25, 70);
            }
            
            // Current language with syntax color
            ctx.fillStyle = '#569cd6';
            ctx.fillText(`lang: ${currentLanguage}`, 25, 100);
            
            // Level indicator
            ctx.fillStyle = '#ce9178';
            ctx.font = 'bold 16px Consolas, monospace';
            ctx.fillText(`Level: ${level}`, 25, 130);
            
            // Combo display
            if (combo > 0) {
                ctx.save();
                ctx.translate(canvas.width / 2, 100);
                ctx.scale(1 + combo * 0.1, 1 + combo * 0.1);
                ctx.fillStyle = '#ffff00';
                ctx.font = 'bold 32px Consolas, monospace';
                ctx.textAlign = 'center';
                ctx.fillText(`${combo}x COMBO!`, 0, 0);
                
                // Combo bar
                ctx.fillStyle = 'rgba(255, 255, 0, 0.3)';
                ctx.fillRect(-100, 10, 200, 10);
                ctx.fillStyle = '#ffff00';
                ctx.fillRect(-100, 10, (comboTimer / 120) * 200, 10);
                ctx.textAlign = 'left';
                ctx.restore();
            }
            
            // Power-up indicators
            let powerUpY = 170;
            for (let [key, timer] of Object.entries(player.powerUpTimers)) {
                if (timer > 0) {
                    const type = powerUpTypes[key];
                    ctx.fillStyle = type.color;
                    ctx.font = '16px Arial';
                    ctx.fillText(type.emoji, 20, powerUpY);
                    
                    // Timer bar
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.2)';
                    ctx.fillRect(50, powerUpY - 12, 100, 10);
                    ctx.fillStyle = type.color;
                    ctx.fillRect(50, powerUpY - 12, (timer / type.duration) * 100, 10);
                    
                    powerUpY += 25;
                }
            }
            
            // Progress bar
            const progress = (player.x / (platforms[platforms.length - 5]?.x || 1000)) * 100;
            ctx.fillStyle = 'rgba(0, 255, 136, 0.3)';
            ctx.fillRect(canvas.width - 210, 20, 200, 10);
            ctx.fillStyle = '#00ff88';
            ctx.fillRect(canvas.width - 210, 20, Math.min(progress * 2, 200), 10);
            
            // Resolution display
            ctx.fillStyle = '#888888';
            ctx.font = '12px Consolas';
            ctx.textAlign = 'right';
            ctx.fillText(`${canvas.width}x${canvas.height}`, canvas.width - 10, 50);
            
            // Max combo
            if (maxCombo > 10) {
                ctx.fillStyle = '#ffaa00';
                ctx.fillText(`Best Combo: ${maxCombo}x`, canvas.width - 10, 70);
            }
            ctx.textAlign = 'left';
            
            // Tutorial on first level
            if (level === 1 && player.x < 500) {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.8)';
                ctx.fillRect(canvas.width/2 - 200, canvas.height - 120, 400, 100);
                
                ctx.fillStyle = '#ffffff';
                ctx.font = '16px Consolas, monospace';
                ctx.textAlign = 'center';
                ctx.fillText('Use ← → or A/D to move', canvas.width/2, canvas.height - 90);
                ctx.fillText('Press SPACE or ↑ to jump (double jump available!)', canvas.width/2, canvas.height - 65);
                ctx.fillText('Collect { } for points, C for extra lives', canvas.width/2, canvas.height - 40);
                ctx.textAlign = 'left';
            }
            
            // Game Over screen
            if (gameState === 'gameOver') {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.9)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                // Error message with glitch effect
                ctx.save();
                ctx.translate(canvas.width / 2, canvas.height / 2 - 50);
                
                ctx.fillStyle = '#ff0000';
                ctx.font = 'bold 48px Consolas, monospace';
                ctx.textAlign = 'center';
                
                // Glitch effect
                if (Math.random() < 0.5) {
                    ctx.fillText('SYNTAX ERROR!', Math.random() * 4 - 2, 0);
                }
                ctx.fillStyle = '#ff0000';
                ctx.fillText('SYNTAX ERROR!', 0, 0);
                
                ctx.restore();
                
                // Stats
                ctx.fillStyle = '#ffffff';
                ctx.font = '24px Consolas, monospace';
                ctx.textAlign = 'center';
                ctx.fillText(`Final Score: ${score}`, canvas.width / 2, canvas.height / 2 + 20);
                
                if (score === highScore && score > 0) {
                    ctx.fillStyle = '#ffff00';
                    ctx.font = 'bold 28px Consolas, monospace';
                    ctx.fillText('NEW HIGH SCORE!', canvas.width / 2, canvas.height / 2 + 50);
                    ctx.fillStyle = '#ffffff';
                    ctx.font = '24px Consolas, monospace';
                } else {
                    ctx.fillText(`High Score: ${highScore}`, canvas.width / 2, canvas.height / 2 + 50);
                }
                
                ctx.fillText(`Level Reached: ${level}`, canvas.width / 2, canvas.height / 2 + 80);
                ctx.fillText(`Best Combo: ${maxCombo}x`, canvas.width / 2, canvas.height / 2 + 110);
                
                ctx.font = '18px Consolas, monospace';
                ctx.fillStyle = '#00ff88';
                ctx.fillText('Press SPACE or tap to restart', canvas.width / 2, canvas.height / 2 + 120);
                
                // G Tech Group credit
                ctx.font = '14px Consolas, monospace';
                ctx.fillStyle = '#888888';
                ctx.fillText('© G Tech Group - www.gtechgroup.it', canvas.width / 2, canvas.height - 30);
                
                ctx.textAlign = 'left';
            }
            
            // Pause screen
            if (gameState === 'paused') {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                ctx.fillStyle = '#00ff88';
                ctx.font = 'bold 48px Consolas, monospace';
                ctx.textAlign = 'center';
                ctx.fillText('// PAUSED', canvas.width / 2, canvas.height / 2);
                
                ctx.fillStyle = '#ffffff';
                ctx.font = '20px Consolas, monospace';
                ctx.fillText('Press ESC or P to resume', canvas.width / 2, canvas.height / 2 + 40);
                ctx.textAlign = 'left';
            }
        }
        
        // Start game
        gameLoop();
    </script>
</body>
</html>
