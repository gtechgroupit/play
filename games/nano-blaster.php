<?php
/**
 * Nano Blaster - G Tech Arcade
 * Shooter survival game in microelectronic environment
 * (c) G Tech Group - All rights reserved
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="Nano Blaster - Defend the chip from nano-virus invasion! A thrilling HTML5 arcade shooter game.">
    <meta name="author" content="G Tech Group">
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><text y='12' font-size='12'>🎮</text></svg>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #080812;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
            position: relative;
            width: 100vw;
            height: 100vh;
            touch-action: none; /* Prevent scrolling on mobile */
        }
        
        #gameCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: block;
            cursor: crosshair;
        }
        
        #startScreen, #gameOverScreen, #achievementsScreen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: rgba(8, 8, 18, 0.9);
            color: #00ffcc;
            text-align: center;
            z-index: 10;
        }
        
        h1 {
            font-size: 3em;
            margin-bottom: 20px;
            text-shadow: 0 0 20px #00ffcc;
        }
        
        .button {
            background: #00ffcc;
            color: #080812;
            border: none;
            padding: 15px 40px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s;
            margin-top: 20px;
        }
        
        .button:hover {
            background: #00ffff;
            transform: scale(1.1);
            box-shadow: 0 0 20px #00ffcc;
        }
        
        #score {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #00ffcc;
            font-size: 1.5em;
            font-weight: bold;
            text-shadow: 0 0 10px #00ffcc;
            z-index: 5;
        }
        
        #lives {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #00ffcc;
            font-size: 1.5em;
            font-weight: bold;
            text-shadow: 0 0 10px #00ffcc;
            z-index: 5;
        }
        
        #wave {
            position: absolute;
            top: 60px;
            left: 20px;
            color: #ffcc00;
            font-size: 1.2em;
            font-weight: bold;
            text-shadow: 0 0 10px #ffcc00;
            z-index: 5;
        }
        
        /* Mobile controls */
        #mobileControls {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 200px;
            display: none;
            z-index: 6;
            pointer-events: none;
        }
        
        #joystick {
            position: absolute;
            bottom: 40px;
            left: 40px;
            width: 120px;
            height: 120px;
            border: 3px solid rgba(0, 255, 204, 0.5);
            border-radius: 50%;
            pointer-events: auto;
        }
        
        #joystickKnob {
            position: absolute;
            width: 50px;
            height: 50px;
            background: rgba(0, 255, 204, 0.8);
            border-radius: 50%;
            top: 35px;
            left: 35px;
            pointer-events: none;
            box-shadow: 0 0 10px rgba(0, 255, 204, 0.8);
        }
        
        #fireButton {
            position: absolute;
            bottom: 40px;
            right: 40px;
            width: 80px;
            height: 80px;
            background: rgba(255, 51, 68, 0.8);
            border: 3px solid #ff3344;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            color: white;
            font-size: 1.2em;
            pointer-events: auto;
        }
        
        #pauseButton {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            background: rgba(0, 255, 204, 0.2);
            border: 2px solid #00ffcc;
            border-radius: 5px;
            color: #00ffcc;
            font-size: 20px;
            display: none;
            z-index: 10;
            cursor: pointer;
            text-align: center;
            line-height: 36px;
            transition: all 0.3s;
        }
        
        #pauseButton:hover {
            background: rgba(0, 255, 204, 0.4);
            transform: scale(1.1);
        }
        
        #pauseButton:active {
            transform: scale(0.95);
        }
        
        @media (max-width: 768px) {
            h1 { font-size: 2em; }
            .button { padding: 12px 30px; font-size: 1em; }
            #score { font-size: 1.2em; left: 70px; }
            #lives { font-size: 1.2em; }
            #wave { font-size: 1em; left: 70px; }
            #highScore { font-size: 1em; top: 60px; }
        }
        
        /* Landscape mobile optimization */
        @media (max-height: 500px) and (orientation: landscape) {
            #mobileControls {
                height: 150px;
            }
            
            #joystick {
                width: 100px;
                height: 100px;
                bottom: 25px;
                left: 25px;
            }
            
            #joystickKnob {
                width: 40px;
                height: 40px;
                top: 30px;
                left: 30px;
            }
            
            #fireButton {
                width: 70px;
                height: 70px;
                bottom: 25px;
                right: 25px;
            }
        }
        
        /* Samsung Galaxy Fold specific */
        @media (min-width: 280px) and (max-width: 320px) {
            h1 { font-size: 1.5em; }
            #score, #lives, #wave { font-size: 0.9em; }
        }
    </style>
</head>
<body>
    <!-- Canvas del gioco -->
    <canvas id="gameCanvas"></canvas>
    
    <!-- UI Elements -->
    <div id="pauseButton" onclick="togglePause()">⏸</div>
    <div id="score">Score: 0</div>
    <div id="lives">Lives: 3</div>
    <div id="wave">Wave: 1</div>
    <div id="powerUpIndicators" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; z-index: 5;">
        <div id="rapidFireIndicator" style="display: none; padding: 5px 10px; background: rgba(255, 255, 0, 0.3); border: 2px solid #ffff00; border-radius: 5px; color: #ffff00; font-size: 0.9em;">⚡ RAPID</div>
        <div id="tripleShotIndicator" style="display: none; padding: 5px 10px; background: rgba(0, 255, 255, 0.3); border: 2px solid #00ffff; border-radius: 5px; color: #00ffff; font-size: 0.9em;">☆ TRIPLE</div>
        <div id="shieldIndicator" style="display: none; padding: 5px 10px; background: rgba(255, 0, 255, 0.3); border: 2px solid #ff00ff; border-radius: 5px; color: #ff00ff; font-size: 0.9em;">◯ SHIELD</div>
    </div>
    
    <!-- Start Screen -->
    <div id="startScreen">
        <h1>NANO BLASTER</h1>
        <p style="color: #ffcc00; margin-bottom: 20px;">Defend the chip from nano-virus invasion!</p>
        <button class="button" onclick="startGame()">START GAME</button>
        <button class="button" style="background: #aa00ff; margin-top: 10px;" onclick="showAchievements()">ACHIEVEMENTS</button>
        <div style="margin-top: 40px; max-width: 600px; text-align: left; color: #aaa; font-size: 0.9em;">
            <p style="text-align: center; margin-bottom: 20px;">
                <strong style="color: #00ffcc;">CONTROLS</strong><br>
                Desktop: WASD/Arrows + Mouse (P to pause)<br>
                Mobile: Touch Controls
            </p>
            <p style="text-align: center;">
                <strong style="color: #00ffcc;">POWER-UPS</strong><br>
                <span style="color: #00ff00;">❤️ Health</span> • 
                <span style="color: #ffff00;">⚡ Rapid Fire</span> • 
                <span style="color: #00ffff;">☆ Triple Shot</span><br>
                <span style="color: #ff00ff;">◯ Shield</span> • 
                <span style="color: #ff6600;">✦ Screen Bomb</span>
            </p>
            <p style="text-align: center; margin-top: 20px; color: #ff3344;">
                <strong>⚠️ BOSS every 5 waves!</strong>
            </p>
        </div>
        <div style="position: absolute; bottom: 20px; color: #666; font-size: 0.8em;">
            © 2024 G Tech Group - All rights reserved
        </div>
    </div>
    
    <!-- Achievements Screen -->
    <div id="achievementsScreen" style="display: none;">
        <h1 style="color: #ffcc00;">ACHIEVEMENTS</h1>
        <div id="achievementsList" style="max-width: 600px; margin: 20px auto;">
            <!-- Achievements will be populated here -->
        </div>
        <button class="button" onclick="hideAchievements()">BACK</button>
    </div>
    
    <!-- Game Over Screen -->
    <div id="gameOverScreen" style="display: none;">
        <h1>GAME OVER</h1>
        <p style="font-size: 1.5em; color: #ffcc00; margin: 20px 0;">Final Score: <span id="finalScore">0</span></p>
        <div style="background: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px; margin: 20px; max-width: 400px;">
            <h3 style="color: #00ffcc; margin-bottom: 15px;">STATISTICS</h3>
            <div style="text-align: left; color: #aaa;">
                <p>Waves Survived: <span id="statWaves" style="color: #ffcc00; float: right;">0</span></p>
                <p>Total Kills: <span id="statKills" style="color: #ffcc00; float: right;">0</span></p>
                <p>Max Combo: <span id="statCombo" style="color: #ffcc00; float: right;">0</span></p>
                <p>Power-ups Collected: <span id="statPowerups" style="color: #ffcc00; float: right;">0</span></p>
                <p>Accuracy: <span id="statAccuracy" style="color: #ffcc00; float: right;">0%</span></p>
            </div>
        </div>
        <button class="button" onclick="restartGame()">PLAY AGAIN</button>
        <button class="button" style="background: #666; margin-top: 10px;" onclick="backToMenu()">MAIN MENU</button>
    </div>
    
    <!-- Mobile Controls -->
    <div id="mobileControls">
        <div id="joystick">
            <div id="joystickKnob"></div>
        </div>
        <div id="fireButton">FIRE</div>
    </div>
    
    <script>
        // Canvas setup
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        
        // Enemy types configuration
        const enemyTypes = {
            virus: {
                radius: 15,
                speed: 2,
                health: 1,
                color: '#ff3344',
                score: 10,
                behavior: 'direct' // moves directly toward player
            },
            worm: {
                radius: 20,
                speed: 1.5,
                health: 2,
                color: '#aa00ff',
                score: 20,
                behavior: 'zigzag' // zigzag movement
            },
            trojan: {
                radius: 25,
                speed: 1,
                health: 3,
                color: '#ffcc00',
                score: 30,
                behavior: 'circle' // circles around player
            },
            boss: {
                radius: 40,
                speed: 2.5,
                health: 10,
                color: '#ff0066',
                score: 100,
                behavior: 'smart' // advanced AI
            }
        };
        
        // Enemy class
        class Enemy {
            constructor(type, x, y) {
                const config = enemyTypes[type];
                this.type = type;
                this.x = x;
                this.y = y;
                this.radius = config.radius;
                this.speed = config.speed;
                this.health = config.health;
                this.maxHealth = config.health;
                this.color = config.color;
                this.score = config.score;
                this.behavior = config.behavior;
                this.angle = 0;
                this.zigzagTimer = 0;
                this.circleAngle = Math.random() * Math.PI * 2;
                this.glowIntensity = 0;
                this.hitFlash = 0;
            }
            
            update() {
                // Flash effect when hit
                if (this.hitFlash > 0) {
                    this.hitFlash--;
                }
                
                // Glow animation
                this.glowIntensity = Math.sin(Date.now() * 0.01) * 0.5 + 0.5;
                
                // Movement based on behavior
                switch (this.behavior) {
                    case 'direct':
                        this.moveDirectly();
                        break;
                    case 'zigzag':
                        this.moveZigzag();
                        break;
                    case 'circle':
                        this.moveInCircle();
                        break;
                    case 'smart':
                        this.moveSmart();
                        break;
                }
                
                // Rotate based on movement
                this.angle += 0.05;
            }
            
            moveDirectly() {
                const dx = player.x - this.x;
                const dy = player.y - this.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance > 0) {
                    this.x += (dx / distance) * this.speed;
                    this.y += (dy / distance) * this.speed;
                }
            }
            
            moveZigzag() {
                this.zigzagTimer += 0.1;
                const zigzagOffset = Math.sin(this.zigzagTimer) * 50;
                
                const dx = player.x - this.x;
                const dy = player.y - this.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance > 0) {
                    // Normal direction
                    const nx = dx / distance;
                    const ny = dy / distance;
                    
                    // Perpendicular direction for zigzag
                    const px = -ny;
                    const py = nx;
                    
                    this.x += nx * this.speed + px * zigzagOffset * 0.02;
                    this.y += ny * this.speed + py * zigzagOffset * 0.02;
                }
            }
            
            moveInCircle() {
                this.circleAngle += 0.02;
                const targetRadius = 150;
                
                const targetX = player.x + Math.cos(this.circleAngle) * targetRadius;
                const targetY = player.y + Math.sin(this.circleAngle) * targetRadius;
                
                const dx = targetX - this.x;
                const dy = targetY - this.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance > 0) {
                    this.x += (dx / distance) * this.speed * 2;
                    this.y += (dy / distance) * this.speed * 2;
                }
            }
            
            moveSmart() {
                // Boss AI - advanced patterns
                const distToPlayer = Math.sqrt((player.x - this.x) ** 2 + (player.y - this.y) ** 2);
                
                // Pattern selection based on health
                if (this.health > 7) {
                    // Aggressive chase pattern
                    const dx = player.x + player.dx * 20 - this.x;
                    const dy = player.y + player.dy * 20 - this.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance > 0) {
                        this.x += (dx / distance) * this.speed;
                        this.y += (dy / distance) * this.speed;
                    }
                } else if (this.health > 4) {
                    // Circular strafing pattern
                    this.circleAngle += 0.03;
                    const targetRadius = 200;
                    const targetX = player.x + Math.cos(this.circleAngle) * targetRadius;
                    const targetY = player.y + Math.sin(this.circleAngle) * targetRadius;
                    
                    const dx = targetX - this.x;
                    const dy = targetY - this.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance > 0) {
                        this.x += (dx / distance) * this.speed * 1.5;
                        this.y += (dy / distance) * this.speed * 1.5;
                    }
                    
                    // Spawn mini viruses occasionally
                    if (Math.random() < 0.02 && enemies.length < 15) {
                        enemies.push(new Enemy('virus', this.x, this.y));
                    }
                } else {
                    // Desperate charge pattern
                    const dx = player.x - this.x;
                    const dy = player.y - this.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    // Speed increases as health decreases
                    const desperationSpeed = this.speed * (1.5 + (5 - this.health) * 0.3);
                    
                    if (distance > 0) {
                        this.x += (dx / distance) * desperationSpeed;
                        this.y += (dy / distance) * desperationSpeed;
                    }
                    
                    // Leave poison trail
                    if (Math.random() < 0.1) {
                        particles.push({
                            x: this.x,
                            y: this.y,
                            dx: (Math.random() - 0.5) * 2,
                            dy: (Math.random() - 0.5) * 2,
                            radius: 15,
                            life: 60,
                            color: '#ff0066',
                            type: 'poison'
                        });
                    }
                }
                
                // Dodge bullets
                bullets.forEach(bullet => {
                    const bDist = Math.sqrt((bullet.x - this.x) ** 2 + (bullet.y - this.y) ** 2);
                    if (bDist < 100) {
                        const dodgeX = this.x - bullet.x;
                        const dodgeY = this.y - bullet.y;
                        const dodgeDist = Math.sqrt(dodgeX * dodgeX + dodgeY * dodgeY);
                        if (dodgeDist > 0) {
                            this.x += (dodgeX / dodgeDist) * 3;
                            this.y += (dodgeY / dodgeDist) * 3;
                        }
                    }
                });
            }
            
            takeDamage(damage) {
                this.health -= damage;
                this.hitFlash = 10;
                
                // Show damage number
                showFloatingText(this.x, this.y - this.radius - 10, `-${damage}`, '#ffffff');
                
                // Create hit particles
                for (let i = 0; i < 5; i++) {
                    particles.push({
                        x: this.x,
                        y: this.y,
                        dx: (Math.random() - 0.5) * 10,
                        dy: (Math.random() - 0.5) * 10,
                        radius: Math.random() * 4 + 2,
                        life: 20,
                        color: this.color
                    });
                }
                
                // Boss splits into smaller enemies when health is half
                if (this.type === 'boss' && this.health === 5 && enemies.length < 20) {
                    showFloatingText(this.x, this.y, 'SPLITTING!', '#ff0066');
                    for (let i = 0; i < 3; i++) {
                        const angle = (Math.PI * 2 / 3) * i;
                        const newX = this.x + Math.cos(angle) * 50;
                        const newY = this.y + Math.sin(angle) * 50;
                        enemies.push(new Enemy('worm', newX, newY));
                    }
                }
                
                return this.health <= 0;
            }
            
            draw() {
                ctx.save();
                
                // Hit flash effect
                if (this.hitFlash > 0) {
                    ctx.globalAlpha = 0.5 + (this.hitFlash / 10) * 0.5;
                }
                
                // Glow effect
                ctx.shadowBlur = 15 + this.glowIntensity * 10;
                ctx.shadowColor = this.color;
                
                // Draw enemy based on type
                ctx.fillStyle = this.color;
                ctx.strokeStyle = this.color;
                ctx.lineWidth = 2;
                
                if (this.type === 'virus') {
                    // Spiky virus
                    ctx.beginPath();
                    for (let i = 0; i < 8; i++) {
                        const angle = (Math.PI * 2 / 8) * i + this.angle;
                        const spike = i % 2 === 0 ? this.radius : this.radius * 0.6;
                        const x = this.x + Math.cos(angle) * spike;
                        const y = this.y + Math.sin(angle) * spike;
                        if (i === 0) {
                            ctx.moveTo(x, y);
                        } else {
                            ctx.lineTo(x, y);
                        }
                    }
                    ctx.closePath();
                    ctx.fill();
                } else if (this.type === 'worm') {
                    // Segmented worm
                    for (let i = 0; i < 3; i++) {
                        const offset = i * 10;
                        const wobble = Math.sin(Date.now() * 0.01 + i) * 5;
                        ctx.beginPath();
                        ctx.arc(
                            this.x - offset * Math.cos(this.angle) + wobble,
                            this.y - offset * Math.sin(this.angle),
                            this.radius - i * 3,
                            0, Math.PI * 2
                        );
                        ctx.fill();
                    }
                } else if (this.type === 'trojan') {
                    // Square trojan
                    ctx.save();
                    ctx.translate(this.x, this.y);
                    ctx.rotate(this.angle);
                    ctx.fillRect(-this.radius, -this.radius, this.radius * 2, this.radius * 2);
                    ctx.strokeRect(-this.radius * 0.6, -this.radius * 0.6, this.radius * 1.2, this.radius * 1.2);
                    ctx.restore();
                } else if (this.type === 'boss') {
                    // Boss worm mutante
                    ctx.beginPath();
                    for (let i = 0; i < 12; i++) {
                        const angle = (Math.PI * 2 / 12) * i + this.angle;
                        const wobble = Math.sin(Date.now() * 0.02 + i) * 5;
                        const spike = this.radius + wobble;
                        const x = this.x + Math.cos(angle) * spike;
                        const y = this.y + Math.sin(angle) * spike;
                        if (i === 0) {
                            ctx.moveTo(x, y);
                        } else {
                            ctx.lineTo(x, y);
                        }
                    }
                    ctx.closePath();
                    ctx.fill();
                    
                    // Inner core
                    ctx.fillStyle = '#080812';
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.radius * 0.5, 0, Math.PI * 2);
                    ctx.fill();
                }
                
                // Health bar for enemies with more than 1 health
                if (this.maxHealth > 1) {
                    const barWidth = this.radius * 2;
                    const barHeight = 4;
                    const barY = this.y - this.radius - 10;
                    
                    ctx.fillStyle = 'rgba(255, 0, 0, 0.5)';
                    ctx.fillRect(this.x - barWidth / 2, barY, barWidth, barHeight);
                    
                    ctx.fillStyle = 'rgba(0, 255, 0, 0.8)';
                    ctx.fillRect(this.x - barWidth / 2, barY, barWidth * (this.health / this.maxHealth), barHeight);
                }
                
                ctx.restore();
            }
        }
        
        // Player object
        const player = {
            x: 0,
            y: 0,
            radius: 20,
            speed: 5,
            dx: 0,
            dy: 0,
            angle: 0,
            color: '#00ffcc',
            glowIntensity: 0,
            // Power-up states
            rapidFire: 0,
            tripleShot: 0,
            shield: 0
        };
        
        // Floating text system
        let floatingTexts = [];
        
        function showFloatingText(x, y, text, color) {
            floatingTexts.push({
                x: x,
                y: y,
                text: text,
                color: color,
                life: 60,
                dy: -2
            });
        }
        
        function updateFloatingTexts() {
            floatingTexts = floatingTexts.filter(text => {
                text.y += text.dy;
                text.life--;
                return text.life > 0;
            });
        }
        
        function drawFloatingTexts() {
            floatingTexts.forEach(text => {
                ctx.save();
                ctx.globalAlpha = text.life / 60;
                ctx.fillStyle = text.color;
                ctx.font = 'bold 20px Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.shadowBlur = 10;
                ctx.shadowColor = text.color;
                ctx.fillText(text.text, text.x, text.y);
                ctx.restore();
            });
        }
        
        // Input handling
        const keys = {};
        const mouse = { x: 0, y: 0, isDown: false };
        const touch = { 
            joystick: { active: false, x: 0, y: 0 },
            fire: false 
        };
        
        // Check if mobile device and performance settings
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
                         (window.innerWidth <= 768 && 'ontouchstart' in window);
        
        // Detect low-end device (rough estimation)
        const isLowEnd = isMobile && (window.innerWidth < 400 || window.devicePixelRatio < 2);
        
        // Canvas resize handler
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            // Reset player position to center
            player.x = canvas.width / 2;
            player.y = canvas.height / 2;
            
            // Show/hide mobile controls
            if (isMobile) {
                document.getElementById('mobileControls').style.display = 'block';
            }
        }
        
        // Initialize canvas
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        // Game initialization
        function startGame() {
            document.getElementById('startScreen').style.display = 'none';
            gameState = 'playing';
            if (isMobile) {
                document.getElementById('pauseButton').style.display = 'block';
                document.getElementById('pauseButton').textContent = '⏸';
            }
            resetGame();
            gameLoop();
        }
        
        function resetGame() {
            score = 0;
            lives = 3;
            wave = 1;
            enemies = [];
            bullets = [];
            particles = [];
            waveEnemyCount = 5;
            enemiesKilledInWave = 0;
            bossSpawned = false;
            player.x = canvas.width / 2;
            player.y = canvas.height / 2;
            player.dx = 0;
            player.dy = 0;
            screenShake = 0;
            waveAnnouncementAlpha = 0;
            
            // Reset mobile controls
            if (isMobile) {
                resetJoystick();
            }
            
            updateUI();
        }
        
        function restartGame() {
            document.getElementById('gameOverScreen').style.display = 'none';
            startGame();
        }
        
        // Update UI elements
        function updateUI() {
            document.getElementById('score').textContent = `Score: ${score}`;
            document.getElementById('lives').textContent = `Lives: ${lives}`;
            document.getElementById('wave').textContent = `Wave: ${wave}`;
        }
        
        // Main game loop
        function gameLoop() {
            if (gameState !== 'playing') {
                if (gameState === 'paused') {
                    // Draw pause screen
                    ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    
                    ctx.fillStyle = '#00ffcc';
                    ctx.font = 'bold 48px Arial';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.shadowBlur = 20;
                    ctx.shadowColor = '#00ffcc';
                    ctx.fillText('PAUSED', canvas.width / 2, canvas.height / 2);
                    
                    ctx.font = '20px Arial';
                    ctx.fillText(isMobile ? 'Tap pause button to continue' : 'Press P to continue', canvas.width / 2, canvas.height / 2 + 60);
                }
                return;
            }
            
            // Clear canvas
            ctx.fillStyle = '#080812';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Apply screen shake
            if (screenShake > 0) {
                ctx.save();
                ctx.translate(
                    (Math.random() - 0.5) * screenShake,
                    (Math.random() - 0.5) * screenShake
                );
                screenShake--;
            }
            
            // Draw grid background
            drawBackground();
            
            // Update and draw game objects
            updatePlayer();
            updateBullets();
            updateEnemies();
            updateParticles();
            updatePowerUps();
            updateFloatingTexts();
            updateCombo();
            
            // Draw game objects
            drawPowerUps();
            drawPlayer();
            drawBullets();
            drawEnemies();
            drawParticles();
            drawFloatingTexts();
            
            // Draw wave announcement
            drawWaveAnnouncement();
            
        // Draw minimap
        function drawMinimap() {
            // Don't draw minimap on very small screens
            if (canvas.width < 600 || canvas.height < 400) return;
            
            const minimapSize = isMobile ? 100 : 150;
            const minimapX = canvas.width - minimapSize - 20;
            const minimapY = isMobile ? 80 : canvas.height - minimapSize - 20;
            const scale = minimapSize / Math.max(canvas.width, canvas.height);
            
            // Minimap background
            ctx.save();
            ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
            ctx.strokeStyle = 'rgba(0, 255, 204, 0.5)';
            ctx.lineWidth = 2;
            ctx.fillRect(minimapX, minimapY, minimapSize, minimapSize);
            ctx.strokeRect(minimapX, minimapY, minimapSize, minimapSize);
            
            // Draw player on minimap
            ctx.fillStyle = '#00ffcc';
            ctx.beginPath();
            ctx.arc(
                minimapX + player.x * scale,
                minimapY + player.y * scale,
                3, 0, Math.PI * 2
            );
            ctx.fill();
            
            // Draw enemies on minimap
            enemies.forEach(enemy => {
                ctx.fillStyle = enemy.type === 'boss' ? '#ff0066' : '#ff3344';
                ctx.beginPath();
                ctx.arc(
                    minimapX + enemy.x * scale,
                    minimapY + enemy.y * scale,
                    enemy.type === 'boss' ? 4 : 2,
                    0, Math.PI * 2
                );
                ctx.fill();
            });
            
            // Draw power-ups on minimap
            powerUps.forEach(powerUp => {
                ctx.fillStyle = powerUp.config.color;
                ctx.beginPath();
                ctx.arc(
                    minimapX + powerUp.x * scale,
                    minimapY + powerUp.y * scale,
                    3, 0, Math.PI * 2
                );
                ctx.fill();
            });
            
            ctx.restore();
        }
            
            // Check collisions
            checkCollisions();
            
            // Spawn enemies
            spawnEnemies();
            
            // Restore canvas if shaking
            if (screenShake > 0) {
                ctx.restore();
            }
            
            // Continue loop
            requestAnimationFrame(gameLoop);
        }
        
        function updatePowerUps() {
            powerUps = powerUps.filter(powerUp => powerUp.update());
        }
        
        function drawPowerUps() {
            powerUps.forEach(powerUp => powerUp.draw());
        }
        
        function updateCombo() {
            if (comboTimer > 0) {
                comboTimer--;
            } else {
                combo = 0;
            }
        }
        
        function drawCombo() {
            if (combo > 1) {
                ctx.save();
                
                // Combo bar background
                const barWidth = 200;
                const barHeight = 10;
                const barX = (canvas.width - barWidth) / 2;
                const barY = 120;
                
                ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
                ctx.fillRect(barX, barY, barWidth, barHeight);
                
                // Combo bar fill
                const fillWidth = (comboTimer / 120) * barWidth;
                const hue = Math.min(combo * 30, 360);
                ctx.fillStyle = `hsl(${hue}, 100%, 50%)`;
                ctx.fillRect(barX, barY, fillWidth, barHeight);
                
                // Combo text
                ctx.fillStyle = '#ffcc00';
                ctx.font = `bold ${24 + Math.min(combo, 10) * 2}px Arial`;
                ctx.textAlign = 'center';
                ctx.shadowBlur = 10 + combo;
                ctx.shadowColor = '#ffcc00';
                
                // Shake effect for high combos
                const shakeX = combo > 5 ? (Math.random() - 0.5) * combo * 0.5 : 0;
                const shakeY = combo > 5 ? (Math.random() - 0.5) * combo * 0.5 : 0;
                
                ctx.fillText(`${combo}x COMBO!`, canvas.width / 2 + shakeX, 100 + shakeY);
                
                // Multiplier indicator
                if (combo > 5) {
                    ctx.font = 'bold 16px Arial';
                    ctx.fillStyle = '#ffffff';
                    ctx.fillText(`Score x${combo}`, canvas.width / 2, 140);
                }
                
                ctx.restore();
            }
        }
        
        // Draw microelectronic background with animated elements
        function drawBackground() {
            // Ambient particles
            if (Math.random() < 0.1 && particles.length < 100) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    dx: (Math.random() - 0.5) * 0.5,
                    dy: (Math.random() - 0.5) * 0.5,
                    radius: Math.random() * 2 + 1,
                    life: 100 + Math.random() * 100,
                    color: 'rgba(0, 255, 204, 0.3)',
                    type: 'ambient'
                });
            }
            
            // Skip some effects on low-end devices
            if (!isLowEnd) {
                // Animated scan line effect
                const scanY = (Date.now() * 0.1) % canvas.height;
                ctx.fillStyle = 'rgba(0, 255, 204, 0.05)';
                ctx.fillRect(0, scanY - 20, canvas.width, 40);
            }
            
            // Grid lines
            ctx.strokeStyle = 'rgba(0, 255, 204, 0.1)';
            ctx.lineWidth = 1;
            
            const gridSize = 50;
            for (let x = 0; x < canvas.width; x += gridSize) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, canvas.height);
                ctx.stroke();
            }
            
            for (let y = 0; y < canvas.height; y += gridSize) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(canvas.width, y);
                ctx.stroke();
            }
            
            // Skip animated nodes on very low-end devices
            if (!isLowEnd) {
                // Animated circuit nodes (less dense on mobile for performance)
                const time = Date.now() * 0.001;
                const nodeSpacing = isMobile ? gridSize * 2 : gridSize;
                ctx.fillStyle = 'rgba(0, 255, 204, 0.2)';
                for (let x = nodeSpacing; x < canvas.width; x += nodeSpacing) {
                    for (let y = nodeSpacing; y < canvas.height; y += nodeSpacing) {
                        const pulse = Math.sin(time + x * 0.01 + y * 0.01) * 0.5 + 0.5;
                        ctx.beginPath();
                        ctx.arc(x, y, 2 + pulse * 2, 0, Math.PI * 2);
                        ctx.fill();
                    }
                }
            }
            
            // Draw memory cells (hexagons) in corners
            drawMemoryCell(50, 50, 30);
            drawMemoryCell(canvas.width - 50, 50, 30);
            drawMemoryCell(50, canvas.height - 50, 30);
            drawMemoryCell(canvas.width - 50, canvas.height - 50, 30);
            
            // Skip center decoration on low-end
            if (!isLowEnd) {
                // Center decoration
                ctx.save();
                ctx.globalAlpha = 0.1;
                ctx.strokeStyle = '#00ffcc';
                ctx.lineWidth = 2;
                const centerRadius = Math.min(canvas.width, canvas.height) * 0.3;
                ctx.beginPath();
                ctx.arc(canvas.width / 2, canvas.height / 2, centerRadius, 0, Math.PI * 2);
                ctx.stroke();
                
                // Rotating center element
                ctx.save();
                ctx.translate(canvas.width / 2, canvas.height / 2);
                ctx.rotate(time * 0.2);
                for (let i = 0; i < 6; i++) {
                    const angle = (Math.PI / 3) * i;
                    ctx.beginPath();
                    ctx.moveTo(0, 0);
                    ctx.lineTo(Math.cos(angle) * centerRadius * 0.8, Math.sin(angle) * centerRadius * 0.8);
                    ctx.stroke();
                }
                ctx.restore();
                ctx.restore();
            }
        }
        
        function drawMemoryCell(x, y, size) {
            ctx.strokeStyle = 'rgba(255, 204, 0, 0.5)';
            ctx.lineWidth = 2;
            ctx.beginPath();
            for (let i = 0; i < 6; i++) {
                const angle = (Math.PI / 3) * i;
                const px = x + Math.cos(angle) * size;
                const py = y + Math.sin(angle) * size;
                if (i === 0) {
                    ctx.moveTo(px, py);
                } else {
                    ctx.lineTo(px, py);
                }
            }
            ctx.closePath();
            ctx.stroke();
        }
        
        // Player update logic
        function updatePlayer() {
            // Update position based on input
            player.x += player.dx * player.speed;
            player.y += player.dy * player.speed;
            
            // Keep player in bounds
            player.x = Math.max(player.radius, Math.min(canvas.width - player.radius, player.x));
            player.y = Math.max(player.radius, Math.min(canvas.height - player.radius, player.y));
            
            // Update glow effect
            player.glowIntensity = Math.sin(Date.now() * 0.005) * 0.5 + 0.5;
            
            // Update angle based on mouse/touch
            if (!isMobile) {
                const dx = mouse.x - player.x;
                const dy = mouse.y - player.y;
                player.angle = Math.atan2(dy, dx);
            }
            // For mobile, angle is updated in joystick handler
            
            // Update power-up timers
            if (player.rapidFire > 0) {
                player.rapidFire--;
                document.getElementById('rapidFireIndicator').style.display = 'block';
            } else {
                document.getElementById('rapidFireIndicator').style.display = 'none';
            }
            
            if (player.tripleShot > 0) {
                player.tripleShot--;
                document.getElementById('tripleShotIndicator').style.display = 'block';
            } else {
                document.getElementById('tripleShotIndicator').style.display = 'none';
            }
            
            if (player.shield > 0) {
                player.shield--;
                document.getElementById('shieldIndicator').style.display = 'block';
            } else {
                document.getElementById('shieldIndicator').style.display = 'none';
            }
        }
        
        // Draw player
        function drawPlayer() {
            ctx.save();
            
            // Draw shield if active
            if (player.shield > 0) {
                ctx.strokeStyle = `rgba(255, 0, 255, ${0.3 + Math.sin(Date.now() * 0.01) * 0.2})`;
                ctx.lineWidth = 3;
                ctx.beginPath();
                ctx.arc(player.x, player.y, player.radius + 15, 0, Math.PI * 2);
                ctx.stroke();
                
                // Shield hex pattern
                for (let i = 0; i < 6; i++) {
                    const angle = (Math.PI / 3) * i + Date.now() * 0.001;
                    ctx.beginPath();
                    ctx.arc(
                        player.x + Math.cos(angle) * (player.radius + 10),
                        player.y + Math.sin(angle) * (player.radius + 10),
                        5, 0, Math.PI * 2
                    );
                    ctx.stroke();
                }
            }
            
            // Draw glow effect (reduced on low-end devices)
            if (!isLowEnd) {
                ctx.shadowBlur = 20 + player.glowIntensity * 10;
                ctx.shadowColor = player.color;
            }
            
            // Draw player circle
            ctx.fillStyle = player.color;
            ctx.beginPath();
            ctx.arc(player.x, player.y, player.radius, 0, Math.PI * 2);
            ctx.fill();
            
            // Draw inner circle
            ctx.fillStyle = '#080812';
            ctx.beginPath();
            ctx.arc(player.x, player.y, player.radius * 0.6, 0, Math.PI * 2);
            ctx.fill();
            
            // Draw direction indicator
            ctx.strokeStyle = player.color;
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.moveTo(player.x, player.y);
            ctx.lineTo(
                player.x + Math.cos(player.angle) * player.radius * 1.5,
                player.y + Math.sin(player.angle) * player.radius * 1.5
            );
            ctx.stroke();
            
            // Draw power-up indicators
            if (player.rapidFire > 0) {
                ctx.fillStyle = '#ffff00';
                ctx.beginPath();
                ctx.arc(player.x - 10, player.y - player.radius - 10, 3, 0, Math.PI * 2);
                ctx.fill();
            }
            
            if (player.tripleShot > 0) {
                ctx.fillStyle = '#00ffff';
                ctx.beginPath();
                ctx.arc(player.x, player.y - player.radius - 10, 3, 0, Math.PI * 2);
                ctx.fill();
            }
            
            if (player.shield > 0) {
                ctx.fillStyle = '#ff00ff';
                ctx.beginPath();
                ctx.arc(player.x + 10, player.y - player.radius - 10, 3, 0, Math.PI * 2);
                ctx.fill();
            }
            
            ctx.restore();
        }
        
        // Bullet class
        class Bullet {
            constructor(x, y, angle) {
                this.x = x;
                this.y = y;
                this.radius = 4;
                this.speed = 15;
                this.dx = Math.cos(angle) * this.speed;
                this.dy = Math.sin(angle) * this.speed;
                this.life = 60; // frames
                this.trail = [];
            }
            
            update() {
                // Store trail positions
                this.trail.push({ x: this.x, y: this.y });
                if (this.trail.length > 5) {
                    this.trail.shift();
                }
                
                this.x += this.dx;
                this.y += this.dy;
                this.life--;
                
                // Check bounds
                if (this.x < 0 || this.x > canvas.width || 
                    this.y < 0 || this.y > canvas.height) {
                    return false;
                }
                
                return this.life > 0;
            }
            
            draw() {
                // Draw trail
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.3)';
                ctx.lineWidth = 2;
                ctx.beginPath();
                this.trail.forEach((pos, i) => {
                    if (i === 0) {
                        ctx.moveTo(pos.x, pos.y);
                    } else {
                        ctx.lineTo(pos.x, pos.y);
                    }
                });
                ctx.stroke();
                
                // Draw bullet with glow
                ctx.save();
                ctx.shadowBlur = 10;
                ctx.shadowColor = '#ffffff';
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            }
        }
        
        // Update bullets
        function updateBullets() {
            bullets = bullets.filter(bullet => {
                return bullet.update();
            });
        }
        
        function drawBullets() {
            bullets.forEach(bullet => bullet.draw());
        }
        
        // Shooting system
        let lastShootTime = 0;
        const shootCooldown = 150; // milliseconds
        
        function shoot() {
            const currentTime = Date.now();
            const cooldown = player.rapidFire > 0 ? 50 : shootCooldown;
            
            if (currentTime - lastShootTime > cooldown) {
                if (player.tripleShot > 0) {
                    // Triple shot
                    for (let i = -1; i <= 1; i++) {
                        const angle = player.angle + (i * 0.2);
                        bullets.push(new Bullet(player.x, player.y, angle));
                        totalShotsFired++;
                    }
                } else {
                    // Single shot
                    bullets.push(new Bullet(player.x, player.y, player.angle));
                    totalShotsFired++;
                }
                
                lastShootTime = currentTime;
                
                // Add muzzle flash effect
                createMuzzleFlash();
            }
        }
        
        function createMuzzleFlash() {
            const flashX = player.x + Math.cos(player.angle) * player.radius;
            const flashY = player.y + Math.sin(player.angle) * player.radius;
            
            // Visual sound wave effect
            particles.push({
                x: flashX,
                y: flashY,
                dx: 0,
                dy: 0,
                radius: 5,
                life: 10,
                color: '#ffffff',
                type: 'soundwave'
            });
            
            for (let i = 0; i < 5; i++) {
                particles.push({
                    x: flashX,
                    y: flashY,
                    dx: Math.cos(player.angle + (Math.random() - 0.5)) * Math.random() * 5,
                    dy: Math.sin(player.angle + (Math.random() - 0.5)) * Math.random() * 5,
                    radius: Math.random() * 3 + 1,
                    life: 10,
                    color: '#ffff00'
                });
            }
        }
        
        function updateEnemies() {
            // Enemy update logic
        }
        
        function drawEnemies() {
            // Enemy drawing logic
        }
        
        function updateParticles() {
            particles = particles.filter(particle => {
                particle.x += particle.dx;
                particle.y += particle.dy;
                particle.life--;
                
                if (particle.type === 'ambient') {
                    // Wrap ambient particles around screen
                    if (particle.x < 0) particle.x = canvas.width;
                    if (particle.x > canvas.width) particle.x = 0;
                    if (particle.y < 0) particle.y = canvas.height;
                    if (particle.y > canvas.height) particle.y = 0;
                } else {
                    particle.dx *= 0.98;
                    particle.dy *= 0.98;
                }
                
                // Check poison cloud collision with player
                if (particle.type === 'poison' && gameState === 'playing') {
                    const dx = player.x - particle.x;
                    const dy = player.y - particle.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < player.radius + particle.radius) {
                        if (player.shield > 0) {
                            player.shield = Math.max(0, player.shield - 30);
                        } else {
                            // Poison damage
                            lives--;
                            updateUI();
                            createExplosion(player.x, player.y, '#ff0000', false);
                            shakeScreen();
                            
                            if (lives <= 0) {
                                gameOver();
                            }
                        }
                        return false; // Remove poison cloud
                    }
                }
                
                return particle.life > 0;
            });
        }
        
        function drawParticles() {
            particles.forEach(particle => {
                ctx.save();
                
                if (particle.type === 'ambient') {
                    // Ambient background particles
                    ctx.globalAlpha = (particle.life / 200) * 0.3;
                    ctx.fillStyle = particle.color;
                    ctx.beginPath();
                    ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
                    ctx.fill();
                } else if (particle.type === 'poison') {
                    // Draw poison cloud
                    ctx.globalAlpha = (particle.life / 60) * 0.6;
                    const gradient = ctx.createRadialGradient(
                        particle.x, particle.y, 0,
                        particle.x, particle.y, particle.radius
                    );
                    gradient.addColorStop(0, '#ff0066');
                    gradient.addColorStop(1, 'rgba(255, 0, 102, 0)');
                    ctx.fillStyle = gradient;
                    ctx.beginPath();
                    ctx.arc(particle.x, particle.y, particle.radius + Math.sin(Date.now() * 0.01) * 5, 0, Math.PI * 2);
                    ctx.fill();
                    
                    // Warning symbol
                    ctx.globalAlpha = (particle.life / 60);
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 16px Arial';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('☠', particle.x, particle.y);
                } else if (particle.type === 'soundwave') {
                    // Sound wave effect
                    const maxRadius = 30;
                    const currentRadius = maxRadius * (1 - particle.life / 10);
                    ctx.globalAlpha = particle.life / 10 * 0.5;
                    ctx.strokeStyle = particle.color;
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.arc(particle.x, particle.y, currentRadius, 0, Math.PI * 2);
                    ctx.stroke();
                } else {
                    // Normal particles
                    ctx.globalAlpha = particle.life / 30;
                    ctx.fillStyle = particle.color;
                    ctx.shadowBlur = 5;
                    ctx.shadowColor = particle.color;
                    ctx.beginPath();
                    ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
                    ctx.fill();
                }
                
                ctx.restore();
            });
        }
        
        function checkCollisions() {
            // Collision detection logic
        }
        
        function spawnEnemies() {
            // Enemy spawning logic
        }
        
        // Input event listeners
        window.addEventListener('keydown', (e) => {
            keys[e.key.toLowerCase()] = true;
            updatePlayerMovement();
            
            // Pause with P key
            if (e.key.toLowerCase() === 'p' && (gameState === 'playing' || gameState === 'paused')) {
                togglePause();
            }
        });
        
        window.addEventListener('keyup', (e) => {
            keys[e.key.toLowerCase()] = false;
            updatePlayerMovement();
        });
        
        window.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });
        
        window.addEventListener('mousedown', (e) => {
            if (e.button === 0 && gameState === 'playing') {
                mouse.isDown = true;
                shoot();
            }
        });
        
        window.addEventListener('mouseup', (e) => {
            if (e.button === 0) {
                mouse.isDown = false;
            }
        });
        
        // Mobile touch controls
        let joystickTouch = null;
        let fireInterval = null;
        
        // Joystick controls
        const joystick = document.getElementById('joystick');
        const joystickKnob = document.getElementById('joystickKnob');
        const fireButton = document.getElementById('fireButton');
        
        joystick.addEventListener('touchstart', (e) => {
            e.preventDefault();
            const touch = e.touches[0];
            joystickTouch = touch.identifier;
            handleJoystickMove(touch);
        });
        
        window.addEventListener('touchmove', (e) => {
            e.preventDefault();
            for (let i = 0; i < e.touches.length; i++) {
                const touch = e.touches[i];
                if (touch.identifier === joystickTouch) {
                    handleJoystickMove(touch);
                }
            }
        });
        
        window.addEventListener('touchend', (e) => {
            e.preventDefault();
            for (let i = 0; i < e.changedTouches.length; i++) {
                const touch = e.changedTouches[i];
                if (touch.identifier === joystickTouch) {
                    joystickTouch = null;
                    resetJoystick();
                }
            }
        });
        
        function handleJoystickMove(touch) {
            const rect = joystick.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            let dx = touch.clientX - centerX;
            let dy = touch.clientY - centerY;
            
            const distance = Math.sqrt(dx * dx + dy * dy);
            const maxDistance = rect.width / 2 - 25;
            
            if (distance > maxDistance) {
                dx = (dx / distance) * maxDistance;
                dy = (dy / distance) * maxDistance;
            }
            
            joystickKnob.style.transform = `translate(${dx}px, ${dy}px)`;
            
            // Update player movement
            player.dx = dx / maxDistance;
            player.dy = dy / maxDistance;
            
            // Update player angle for mobile aiming
            if (Math.abs(dx) > 5 || Math.abs(dy) > 5) {
                player.angle = Math.atan2(dy, dx);
            }
        }
        
        function resetJoystick() {
            joystickKnob.style.transform = 'translate(0, 0)';
            player.dx = 0;
            player.dy = 0;
        }
        
        // Fire button controls
        fireButton.addEventListener('touchstart', (e) => {
            e.preventDefault();
            if (gameState === 'playing') {
                shoot();
                fireInterval = setInterval(shoot, 50); // Check more frequently
            }
        });
        
        fireButton.addEventListener('touchend', (e) => {
            e.preventDefault();
            clearInterval(fireInterval);
        });
        
        // Update player movement based on keyboard input
        function updatePlayerMovement() {
            if (isMobile) return; // Skip if using mobile controls
            
            player.dx = 0;
            player.dy = 0;
            
            if (keys['w'] || keys['arrowup']) player.dy = -1;
            if (keys['s'] || keys['arrowdown']) player.dy = 1;
            if (keys['a'] || keys['arrowleft']) player.dx = -1;
            if (keys['d'] || keys['arrowright']) player.dx = 1;
            
            // Normalize diagonal movement
            if (player.dx !== 0 && player.dy !== 0) {
                player.dx *= 0.707;
                player.dy *= 0.707;
            }
        }
        
        // Auto-fire for mouse hold
        setInterval(() => {
            if (mouse.isDown && gameState === 'playing') {
                shoot();
            }
        }, 50); // Check more frequently for rapid fire
        
        // Pause game when window loses focus
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && gameState === 'playing') {
                // Game is paused automatically when tab is hidden
            }
        });
        
        // Prevent right-click context menu on canvas
        canvas.addEventListener('contextmenu', (e) => {
            e.preventDefault();
        });
        
        // Initialize UI on load
        updateUI();
        loadAchievements();
        
        // Set welcome message based on time of day
        const hour = new Date().getHours();
        let welcomeMessage = '';
        if (hour >= 5 && hour < 12) {
            welcomeMessage = 'Good morning, Commander!';
        } else if (hour >= 12 && hour < 18) {
            welcomeMessage = 'Good afternoon, Commander!';
        } else {
            welcomeMessage = 'Good evening, Commander!';
        }
        
        // Add welcome message to start screen
        const startScreen = document.getElementById('startScreen');
        const welcomeEl = document.createElement('p');
        welcomeEl.style.cssText = 'color: #00ffcc; font-size: 0.9em; margin-top: -10px; margin-bottom: 20px;';
        welcomeEl.textContent = welcomeMessage;
        startScreen.insertBefore(welcomeEl, startScreen.children[2]);
        
        // Achievement screen functions
        function showAchievements() {
            document.getElementById('startScreen').style.display = 'none';
            document.getElementById('achievementsScreen').style.display = 'flex';
            populateAchievements();
        }
        
        function hideAchievements() {
            document.getElementById('achievementsScreen').style.display = 'none';
            document.getElementById('startScreen').style.display = 'flex';
        }
        
        function populateAchievements() {
            const list = document.getElementById('achievementsList');
            list.innerHTML = '';
            
            let unlockedCount = 0;
            const totalCount = Object.keys(achievements).length;
            
            for (let key in achievements) {
                const achievement = achievements[key];
                if (achievement.unlocked) unlockedCount++;
                
                const item = document.createElement('div');
                item.style.cssText = `
                    padding: 15px;
                    margin: 10px 0;
                    background: ${achievement.unlocked ? 'rgba(255, 204, 0, 0.1)' : 'rgba(100, 100, 100, 0.1)'};
                    border: 2px solid ${achievement.unlocked ? '#ffcc00' : '#666666'};
                    border-radius: 5px;
                    display: flex;
                    align-items: center;
                `;
                
                item.innerHTML = `
                    <div style="font-size: 30px; margin-right: 15px;">
                        ${achievement.unlocked ? '🏆' : '🔒'}
                    </div>
                    <div>
                        <div style="font-weight: bold; color: ${achievement.unlocked ? '#ffcc00' : '#666666'};">
                            ${achievement.name}
                        </div>
                        <div style="font-size: 0.9em; color: ${achievement.unlocked ? '#aaaaaa' : '#555555'};">
                            ${achievement.desc}
                        </div>
                    </div>
                `;
                
                list.appendChild(item);
            }
            
            // Progress indicator
            const progress = document.createElement('div');
            progress.style.cssText = 'text-align: center; margin-top: 20px; color: #00ffcc;';
            progress.innerHTML = `Progress: ${unlockedCount}/${totalCount} (${Math.floor(unlockedCount / totalCount * 100)}%)`;
            list.appendChild(progress);
        }
    </script>
</body>
</html>
