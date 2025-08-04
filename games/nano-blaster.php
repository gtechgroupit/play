/* Responsive adjustments */
        @media (max-width: <?php
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
    <title>Nano Blaster - G Tech Arcade</title>
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
        
        #startScreen, #gameOverScreen {
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
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            h1 { font-size: 2em; }
            .button { padding: 12px 30px; font-size: 1em; }
            #score, #lives { font-size: 1.2em; }
            #wave { font-size: 1em; }
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
    <div id="score">Score: 0</div>
    <div id="lives">Lives: 3</div>
    <div id="wave">Wave: 1</div>
    <div id="highScore" style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); color: #ffcc00; font-size: 1.2em; text-shadow: 0 0 10px #ffcc00; z-index: 5;">High Score: 0</div>
    
    <!-- Start Screen -->
    <div id="startScreen">
        <h1>NANO BLASTER</h1>
        <p style="color: #ffcc00; margin-bottom: 20px;">Defend the chip from nano-virus invasion!</p>
        <button class="button" onclick="startGame()">START GAME</button>
        <p style="color: #aaa; margin-top: 40px; font-size: 0.9em;">
            Desktop: WASD/Arrows + Mouse<br>
            Mobile: Touch Controls
        </p>
    </div>
    
    <!-- Game Over Screen -->
    <div id="gameOverScreen" style="display: none;">
        <h1>GAME OVER</h1>
        <p style="font-size: 1.5em; color: #ffcc00; margin: 20px 0;">Final Score: <span id="finalScore">0</span></p>
        <button class="button" onclick="restartGame()">PLAY AGAIN</button>
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
                // Boss AI - predicts player movement
                const dx = player.x + player.dx * 20 - this.x;
                const dy = player.y + player.dy * 20 - this.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance > 0) {
                    this.x += (dx / distance) * this.speed;
                    this.y += (dy / distance) * this.speed;
                }
                
                // Dodge bullets occasionally
                bullets.forEach(bullet => {
                    const bDist = Math.sqrt((bullet.x - this.x) ** 2 + (bullet.y - this.y) ** 2);
                    if (bDist < 100) {
                        const dodgeX = this.x - bullet.x;
                        const dodgeY = this.y - bullet.y;
                        const dodgeDist = Math.sqrt(dodgeX * dodgeX + dodgeY * dodgeY);
                        if (dodgeDist > 0) {
                            this.x += (dodgeX / dodgeDist) * 2;
                            this.y += (dodgeY / dodgeDist) * 2;
                        }
                    }
                });
            }
            
            takeDamage(damage) {
                this.health -= damage;
                this.hitFlash = 10;
                
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
        
        // Check if mobile device
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
                         (window.innerWidth <= 768 && 'ontouchstart' in window);
        
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
            if (gameState !== 'playing') return;
            
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
                ctx.fillStyle = '#ffcc00';
                ctx.font = 'bold 24px Arial';
                ctx.textAlign = 'center';
                ctx.shadowBlur = 10;
                ctx.shadowColor = '#ffcc00';
                ctx.fillText(`${combo}x COMBO!`, canvas.width / 2, 100);
                ctx.restore();
            }
        }
        
        // Draw microelectronic background with animated elements
        function drawBackground() {
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
            
            // Animated circuit nodes
            const time = Date.now() * 0.001;
            ctx.fillStyle = 'rgba(0, 255, 204, 0.2)';
            for (let x = gridSize; x < canvas.width; x += gridSize) {
                for (let y = gridSize; y < canvas.height; y += gridSize) {
                    const pulse = Math.sin(time + x * 0.01 + y * 0.01) * 0.5 + 0.5;
                    ctx.beginPath();
                    ctx.arc(x, y, 2 + pulse * 2, 0, Math.PI * 2);
                    ctx.fill();
                }
            }
            
            // Draw memory cells (hexagons) in corners
            drawMemoryCell(50, 50, 30);
            drawMemoryCell(canvas.width - 50, 50, 30);
            drawMemoryCell(50, canvas.height - 50, 30);
            drawMemoryCell(canvas.width - 50, canvas.height - 50, 30);
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
            if (player.rapidFire > 0) player.rapidFire--;
            if (player.tripleShot > 0) player.tripleShot--;
            if (player.shield > 0) player.shield--;
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
            
            // Draw glow effect
            ctx.shadowBlur = 20 + player.glowIntensity * 10;
            ctx.shadowColor = player.color;
            
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
                    }
                } else {
                    // Single shot
                    bullets.push(new Bullet(player.x, player.y, player.angle));
                }
                
                lastShootTime = currentTime;
                
                // Add muzzle flash effect
                createMuzzleFlash();
            }
        }
        
        function createMuzzleFlash() {
            const flashX = player.x + Math.cos(player.angle) * player.radius;
            const flashY = player.y + Math.sin(player.angle) * player.radius;
            
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
                particle.dx *= 0.98;
                particle.dy *= 0.98;
                return particle.life > 0;
            });
        }
        
        function drawParticles() {
            particles.forEach(particle => {
                ctx.save();
                ctx.globalAlpha = particle.life / 10;
                ctx.fillStyle = particle.color;
                ctx.shadowBlur = 5;
                ctx.shadowColor = particle.color;
                ctx.beginPath();
                ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
                ctx.fill();
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
    </script>
</body>
</html>
