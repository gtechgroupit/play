<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Cyberscape - G Tech Arcade</title>
    <style>
        /* Reset e stili base */
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
            background-color: #0a0f1c;
            color: #00ffff;
            font-family: 'Courier New', monospace;
            overflow: hidden;
            position: fixed;
            width: 100%;
            height: 100%;
            touch-action: none;
        }

        /* Canvas principale */
        #gameCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: block;
            cursor: none;
        }

        /* UI overlay */
        #gameUI {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }

        /* Score e vite */
        #gameStats {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 20px;
            text-shadow: 0 0 10px #00ffff;
            font-weight: bold;
        }

        .life {
            display: inline-block;
            width: 20px;
            height: 20px;
            background: #ff66cc;
            margin-left: 5px;
            box-shadow: 0 0 10px #ff66cc;
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
        }
        
        /* Power-up indicators */
        .powerup-indicator {
            margin-top: 5px;
            padding: 5px 10px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 5px;
            display: inline-block;
            animation: glow-indicator 1s ease-in-out infinite;
        }
        
        @keyframes glow-indicator {
            0%, 100% { box-shadow: 0 0 5px currentColor; }
            50% { box-shadow: 0 0 15px currentColor; }
        }

        /* Menu principale */
        #startMenu, #gameOverMenu {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: rgba(10, 15, 28, 0.95);
            padding: 40px;
            border: 2px solid #00ffff;
            box-shadow: 0 0 30px #00ffff, inset 0 0 30px rgba(0, 255, 255, 0.2);
            border-radius: 10px;
            z-index: 20;
        }

        h1, h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
            text-shadow: 0 0 20px #00ffff;
            animation: glow 2s ease-in-out infinite;
        }

        @keyframes glow {
            0%, 100% { text-shadow: 0 0 20px #00ffff, 0 0 30px #00ffff; }
            50% { text-shadow: 0 0 30px #ff66cc, 0 0 40px #ff66cc; }
        }

        .button {
            background: linear-gradient(45deg, #00ffff, #ff66cc);
            border: none;
            color: #0a0f1c;
            padding: 15px 30px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            margin: 10px;
            border-radius: 5px;
            text-transform: uppercase;
            transition: all 0.3s;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
        }

        .button:hover {
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(255, 102, 204, 0.8);
        }

        /* Controlli mobile */
        #mobileControls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: none;
            gap: 20px;
            z-index: 15;
        }

        .control-btn {
            width: 80px;
            height: 80px;
            background: rgba(0, 255, 255, 0.2);
            border: 2px solid #00ffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: #00ffff;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
        }

        .control-btn:active {
            background: rgba(0, 255, 255, 0.5);
            transform: scale(0.9);
        }

        /* Checkpoint notification */
        #checkpoint {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3em;
            font-weight: bold;
            color: #ff66cc;
            text-shadow: 0 0 30px #ff66cc;
            opacity: 0;
            pointer-events: none;
            z-index: 25;
        }

        @keyframes checkpointPulse {
            0% { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
            50% { opacity: 1; transform: translate(-50%, -50%) scale(1.2); }
            100% { opacity: 0; transform: translate(-50%, -50%) scale(1.5); }
        }

        .checkpoint-active {
            animation: checkpointPulse 2s ease-out;
        }

        /* Media queries per responsive */
        @media (max-width: 768px) {
            #gameStats {
                font-size: 16px;
                top: 10px;
                left: 10px;
            }
            
            h1, h2 {
                font-size: 2em;
            }
            
            #startMenu, #gameOverMenu {
                padding: 20px;
                width: 90%;
                max-width: 350px;
            }
        }

        /* Mostra controlli mobile su dispositivi touch */
        @media (hover: none) and (pointer: coarse) {
            #mobileControls {
                display: flex;
            }
        }

        /* Ottimizzazioni per Samsung Fold */
        @media screen and (min-width: 768px) and (max-width: 1024px) and (orientation: portrait) {
            #mobileControls {
                bottom: 40px;
            }
            
            .control-btn {
                width: 100px;
                height: 100px;
            }
        }
    </style>
</head>
<body>
    <!-- Canvas principale del gioco -->
    <canvas id="gameCanvas"></canvas>
    
    <!-- UI del gioco -->
    <div id="gameUI">
        <div id="gameStats" style="display: none;">
            <div>SCORE: <span id="score">0</span></div>
            <div>DISTANCE: <span id="distance">0</span>m</div>
            <div>LIVES: <span id="lives"></span></div>
        </div>
        
        <!-- Notifica checkpoint -->
        <div id="checkpoint">CHECKPOINT!</div>
    </div>
    
    <!-- Menu iniziale -->
    <div id="startMenu">
        <h1>CYBERSCAPE</h1>
        <p style="margin: 20px 0; color: #ff66cc;">Navigate the digital realm</p>
        <button class="button" onclick="startGame()">START GAME</button>
        <p style="margin-top: 20px; font-size: 0.9em;">
            Desktop: Use ← → or A/D to move<br>
            Mobile: Swipe or use buttons
        </p>
    </div>
    
    <!-- Menu game over -->
    <div id="gameOverMenu" style="display: none;">
        <h2>GAME OVER</h2>
        <p style="margin: 20px 0; font-size: 1.2em;">
            Final Score: <span id="finalScore">0</span><br>
            Distance: <span id="finalDistance">0</span>m
        </p>
        <button class="button" onclick="restartGame()">RESTART</button>
        <button class="button" onclick="location.href='../index.php'">MAIN MENU</button>
    </div>
    
    <!-- Controlli mobile -->
    <div id="mobileControls">
        <div class="control-btn" ontouchstart="movePlayer(-1)" ontouchend="stopMove()">◀</div>
        <div class="control-btn" ontouchstart="activateBoost()" style="background: rgba(255, 102, 204, 0.2); border-color: #ff66cc;">⚡</div>
        <div class="control-btn" ontouchstart="movePlayer(1)" ontouchend="stopMove()">▶</div>
    </div>

    <script>
        // ===== INIZIALIZZAZIONE CANVAS E VARIABILI GLOBALI =====
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        
        // Resize canvas
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        // Variabili di gioco
        let gameRunning = false;
        let score = 0;
        let distance = 0;
        let lives = 3;
        let speed = 5;
        let maxSpeed = 20;
        let acceleration = 0.0005;
        let lastCheckpoint = 0;
        let animationId = null;
        let lastTime = 0;
        
        // Player
        const player = {
            lane: 1, // 0 = sinistra, 1 = centro, 2 = destra
            targetLane: 1,
            x: 0,
            y: 0,
            size: 30,
            moving: false,
            boost: false,
            boostTime: 0,
            invulnerable: false,
            invulnerableTime: 0,
            shield: false,
            scoreMultiplier: 1,
            scoreMultiplierTime: 0,
            slowMotion: false,
            slowMotionTime: 0
        };
        
        // Ostacoli
        let obstacles = [];
        const obstacleTypes = ['firewall', 'laser', 'glitch', 'cube'];
        
        // Power-ups
        let powerUps = [];
        const powerUpTypes = {
            speedBoost: { symbol: '⚡', color: '#ffff00', glow: '#ffff88' },
            shield: { symbol: '🛡️', color: '#00ff00', glow: '#88ff88' },
            scoreMultiplier: { symbol: '💎', color: '#ff00ff', glow: '#ff88ff' },
            slowMotion: { symbol: '🌟', color: '#00ffff', glow: '#88ffff' },
            laneSwap: { symbol: '🔄', color: '#ff8800', glow: '#ffaa44' }
        };
        
        // Particelle per effetti
        let particles = [];
        
        // Tunnel prospettico
        const tunnel = {
            segments: 20,
            depth: 1000,
            width: 300,
            height: 200
        };
        
        // Input handling
        const keys = {};
        let touchStartX = null;
        let moveDirection = 0;
        
        // ===== GESTIONE INPUT =====
        
        // Keyboard
        document.addEventListener('keydown', (e) => {
            keys[e.key] = true;
            
            if (gameRunning) {
                if (e.key === 'ArrowLeft' || e.key === 'a' || e.key === 'A') {
                    movePlayer(-1);
                } else if (e.key === 'ArrowRight' || e.key === 'd' || e.key === 'D') {
                    movePlayer(1);
                } else if (e.key === ' ') {
                    activateBoost();
                }
            }
        });
        
        document.addEventListener('keyup', (e) => {
            keys[e.key] = false;
        });
        
        // Touch/Swipe per mobile
        canvas.addEventListener('touchstart', (e) => {
            if (!gameRunning) return;
            touchStartX = e.touches[0].clientX;
        });
        
        canvas.addEventListener('touchmove', (e) => {
            if (!gameRunning || touchStartX === null) return;
            e.preventDefault();
            
            const touchX = e.touches[0].clientX;
            const diffX = touchX - touchStartX;
            
            if (Math.abs(diffX) > 50) {
                if (diffX > 0) {
                    movePlayer(1);
                } else {
                    movePlayer(-1);
                }
                touchStartX = touchX;
            }
        });
        
        canvas.addEventListener('touchend', () => {
            touchStartX = null;
        });
        
        // ===== FUNZIONI DI CONTROLLO PLAYER =====
        
        function movePlayer(direction) {
            if (!gameRunning || player.moving) return;
            
            const newLane = player.lane + direction;
            if (newLane >= 0 && newLane <= 2) {
                player.targetLane = newLane;
                player.moving = true;
                
                // Effetto particelle movimento
                createMoveParticles();
            }
        }
        
        function stopMove() {
            moveDirection = 0;
        }
        
        function activateBoost() {
            if (!gameRunning || player.boost || player.boostTime > 0) return;
            
            player.boost = true;
            player.boostTime = 120; // 2 secondi a 60fps
            speed = Math.min(speed * 1.5, maxSpeed);
            
            // Effetto boost
            createBoostParticles();
        }
        
        // ===== INIZIALIZZAZIONE GIOCO =====
        
        function initGame() {
            score = 0;
            distance = 0;
            lives = 3;
            speed = 5;
            lastCheckpoint = 0;
            obstacles = [];
            particles = [];
            powerUps = [];
            
            player.lane = 1;
            player.targetLane = 1;
            player.moving = false;
            player.boost = false;
            player.boostTime = 0;
            player.invulnerable = false;
            player.invulnerableTime = 0;
            player.shield = false;
            player.scoreMultiplier = 1;
            player.scoreMultiplierTime = 0;
            player.slowMotion = false;
            player.slowMotionTime = 0;
            
            updateLivesDisplay();
        }
        
        function startGame() {
            initGame();
            gameRunning = true;
            
            document.getElementById('startMenu').style.display = 'none';
            document.getElementById('gameStats').style.display = 'block';
            document.getElementById('gameOverMenu').style.display = 'none';
            
            lastTime = performance.now();
            gameLoop();
        }
        
        function restartGame() {
            startGame();
        }
        
        // ===== GAME LOOP PRINCIPALE =====
        
        function gameLoop(currentTime) {
            if (!gameRunning) return;
            
            const deltaTime = currentTime - lastTime;
            lastTime = currentTime;
            
            // Update
            update(deltaTime);
            
            // Render
            render();
            
            animationId = requestAnimationFrame(gameLoop);
        }
        
        // ===== UPDATE LOGIC =====
        
        function update(deltaTime) {
            // Applica slow motion se attivo
            const timeScale = player.slowMotion ? 0.3 : 1;
            
            // Update velocità
            if (!player.boost) {
                speed = Math.min(speed + acceleration * deltaTime * timeScale, maxSpeed);
            }
            
            // Update distanza e score
            distance += speed * 0.1 * timeScale;
            score += Math.floor(speed * 0.5 * player.scoreMultiplier);
            
            // Update player
            updatePlayer();
            
            // Update ostacoli
            updateObstacles(timeScale);
            
            // Update power-ups
            updatePowerUps(timeScale);
            
            // Update particelle
            updateParticles();
            
            // Check checkpoint
            checkCheckpoint();
            
            // Update UI
            updateUI();
        }
        
        function updatePlayer() {
            // Movimento fluido tra corsie
            if (player.moving) {
                const lanePositions = [-100, 0, 100];
                const targetX = lanePositions[player.targetLane];
                const moveSpeed = 8;
                
                if (Math.abs(player.x - targetX) > moveSpeed) {
                    player.x += (targetX - player.x) * 0.2;
                } else {
                    player.x = targetX;
                    player.lane = player.targetLane;
                    player.moving = false;
                }
            }
            
            // Gestione boost
            if (player.boostTime > 0) {
                player.boostTime--;
                if (player.boostTime === 0) {
                    player.boost = false;
                    speed = speed / 1.5;
                }
            }
            
            // Gestione invulnerabilità
            if (player.invulnerableTime > 0) {
                player.invulnerableTime--;
                if (player.invulnerableTime === 0) {
                    player.invulnerable = false;
                }
            }
            
            // Gestione score multiplier
            if (player.scoreMultiplierTime > 0) {
                player.scoreMultiplierTime--;
                if (player.scoreMultiplierTime === 0) {
                    player.scoreMultiplier = 1;
                }
            }
            
            // Gestione slow motion
            if (player.slowMotionTime > 0) {
                player.slowMotionTime--;
                if (player.slowMotionTime === 0) {
                    player.slowMotion = false;
                }
            }
            
            // Posizione Y fissa
            player.y = canvas.height * 0.75;
        }
        
        function updateObstacles(timeScale = 1) {
            // Genera nuovi ostacoli
            if (Math.random() < 0.02 + (speed / 1000)) {
                const lane = Math.floor(Math.random() * 3);
                const type = obstacleTypes[Math.floor(Math.random() * obstacleTypes.length)];
                
                obstacles.push({
                    lane: lane,
                    z: tunnel.depth,
                    type: type,
                    hit: false
                });
            }
            
            // Update ostacoli esistenti
            obstacles = obstacles.filter(obstacle => {
                obstacle.z -= speed * timeScale;
                
                // Check collisione
                if (!obstacle.hit && !player.invulnerable && !player.shield && obstacle.z < 100 && obstacle.z > -100) {
                    if (obstacle.lane === player.lane) {
                        // Collisione!
                        obstacle.hit = true;
                        
                        if (player.shield) {
                            // Lo scudo protegge
                            player.shield = false;
                            createShieldBreakParticles();
                        } else {
                            lives--;
                            player.invulnerable = true;
                            player.invulnerableTime = 120; // 2 secondi
                            
                            createExplosionParticles(player.x, player.y);
                            
                            if (lives <= 0) {
                                gameOver();
                            }
                        }
                        
                        updateLivesDisplay();
                    }
                }
                
                return obstacle.z > -200;
            });
        }
        
        function updatePowerUps(timeScale = 1) {
            // Genera nuovi power-up
            if (Math.random() < 0.005) { // Meno frequenti degli ostacoli
                const lane = Math.floor(Math.random() * 3);
                const types = Object.keys(powerUpTypes);
                const type = types[Math.floor(Math.random() * types.length)];
                
                powerUps.push({
                    lane: lane,
                    z: tunnel.depth,
                    type: type,
                    collected: false,
                    rotation: 0
                });
            }
            
            // Update power-ups esistenti
            powerUps = powerUps.filter(powerUp => {
                powerUp.z -= speed * timeScale * 0.8; // Più lenti degli ostacoli
                powerUp.rotation += 0.05;
                
                // Check collezione
                if (!powerUp.collected && powerUp.z < 100 && powerUp.z > -100) {
                    if (powerUp.lane === player.lane) {
                        // Raccolto!
                        powerUp.collected = true;
                        collectPowerUp(powerUp.type);
                        createPowerUpParticles(player.x, player.y, powerUpTypes[powerUp.type].color);
                    }
                }
                
                return powerUp.z > -200;
            });
        }
        
        function collectPowerUp(type) {
            switch(type) {
                case 'speedBoost':
                    player.boost = true;
                    player.boostTime = 180; // 3 secondi
                    speed = Math.min(speed * 1.5, maxSpeed * 1.5);
                    break;
                    
                case 'shield':
                    player.shield = true;
                    break;
                    
                case 'scoreMultiplier':
                    player.scoreMultiplier = 3;
                    player.scoreMultiplierTime = 600; // 10 secondi
                    break;
                    
                case 'slowMotion':
                    player.slowMotion = true;
                    player.slowMotionTime = 300; // 5 secondi
                    break;
                    
                case 'laneSwap':
                    // Scambia posizione di tutti gli ostacoli
                    obstacles.forEach(obstacle => {
                        obstacle.lane = (obstacle.lane + 1) % 3;
                    });
                    createLaneSwapEffect();
                    break;
            }
        }
        
        function updateParticles() {
            const timeScale = player.slowMotion ? 0.3 : 1;
            
            particles = particles.filter(particle => {
                particle.x += particle.vx * timeScale;
                particle.y += particle.vy * timeScale;
                particle.z -= speed * timeScale;
                particle.life--;
                
                if (particle.vy) particle.vy += 0.5 * timeScale; // gravità
                
                return particle.life > 0 && particle.z > -200;
            });
        }
        
        // ===== RENDERING =====
        
        function render() {
            // Clear canvas
            ctx.fillStyle = '#0a0f1c';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Effetto slow motion
            if (player.slowMotion) {
                ctx.fillStyle = 'rgba(0, 255, 255, 0.1)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
            }
            
            // Salva stato
            ctx.save();
            
            // Centra la vista
            ctx.translate(canvas.width / 2, canvas.height / 2);
            
            // Disegna tunnel
            drawTunnel();
            
            // Disegna power-ups
            drawPowerUps();
            
            // Disegna ostacoli
            drawObstacles();
            
            // Disegna particelle
            drawParticles();
            
            // Disegna player
            drawPlayer();
            
            // Ripristina stato
            ctx.restore();
            
            // Effetti post-processing
            if (player.boost) {
                drawBoostEffect();
            }
            
            if (player.invulnerable) {
                drawInvulnerableEffect();
            }
            
            // UI per power-up attivi
            drawActivePowerUps();
        }
        
        function drawTunnel() {
            ctx.strokeStyle = '#00ffff';
            ctx.lineWidth = 2;
            
            for (let i = 0; i < tunnel.segments; i++) {
                const z = (i / tunnel.segments) * tunnel.depth;
                const scale = 1 / (1 + z / 100);
                const alpha = 1 - (i / tunnel.segments) * 0.8;
                
                ctx.globalAlpha = alpha;
                ctx.strokeStyle = i % 2 === 0 ? '#00ffff' : '#ff66cc';
                
                // Rettangolo prospettico
                const w = tunnel.width * scale;
                const h = tunnel.height * scale;
                
                ctx.strokeRect(-w, -h, w * 2, h * 2);
                
                // Linee di fuga
                if (i % 4 === 0) {
                    ctx.beginPath();
                    // Linea superiore sinistra
                    ctx.moveTo(-tunnel.width, -tunnel.height);
                    ctx.lineTo(-w, -h);
                    // Linea superiore destra
                    ctx.moveTo(tunnel.width, -tunnel.height);
                    ctx.lineTo(w, -h);
                    // Linea inferiore sinistra
                    ctx.moveTo(-tunnel.width, tunnel.height);
                    ctx.lineTo(-w, h);
                    // Linea inferiore destra
                    ctx.moveTo(tunnel.width, tunnel.height);
                    ctx.lineTo(w, h);
                    ctx.stroke();
                }
            }
            
            ctx.globalAlpha = 1;
        }
        
        function drawObstacles() {
            const lanePositions = [-100, 0, 100];
            
            obstacles.forEach(obstacle => {
                const scale = 1 / (1 + obstacle.z / 100);
                const x = lanePositions[obstacle.lane] * scale;
                const y = 0;
                const size = 40 * scale;
                const alpha = Math.min(1, 1 - (obstacle.z / tunnel.depth));
                
                ctx.globalAlpha = alpha;
                
                switch(obstacle.type) {
                    case 'firewall':
                        // Barriera verticale
                        ctx.fillStyle = '#ff0066';
                        ctx.fillRect(x - size/2, y - size * 2, size, size * 4);
                        ctx.strokeStyle = '#ff66cc';
                        ctx.lineWidth = 2;
                        ctx.strokeRect(x - size/2, y - size * 2, size, size * 4);
                        break;
                        
                    case 'laser':
                        // Laser orizzontale
                        ctx.strokeStyle = '#00ff00';
                        ctx.lineWidth = size / 10;
                        ctx.shadowBlur = 20;
                        ctx.shadowColor = '#00ff00';
                        ctx.beginPath();
                        ctx.moveTo(-tunnel.width * scale, y);
                        ctx.lineTo(tunnel.width * scale, y);
                        ctx.stroke();
                        ctx.shadowBlur = 0;
                        break;
                        
                    case 'glitch':
                        // Cubo glitchato
                        for (let i = 0; i < 3; i++) {
                            ctx.fillStyle = ['#ff0000', '#00ff00', '#0000ff'][i];
                            ctx.globalAlpha = alpha * 0.5;
                            ctx.fillRect(
                                x - size/2 + Math.random() * 10 - 5,
                                y - size/2 + Math.random() * 10 - 5,
                                size,
                                size
                            );
                        }
                        break;
                        
                    case 'cube':
                        // Cubo standard
                        ctx.fillStyle = '#00ffff';
                        ctx.fillRect(x - size/2, y - size/2, size, size);
                        ctx.strokeStyle = '#ffffff';
                        ctx.lineWidth = 2;
                        ctx.strokeRect(x - size/2, y - size/2, size, size);
                        break;
                }
            });
            
            ctx.globalAlpha = 1;
        }
        
        function drawPlayer() {
            ctx.save();
            ctx.translate(player.x, player.y - canvas.height/2);
            
            // Effetto lampeggio quando invulnerabile
            if (player.invulnerable && Math.floor(player.invulnerableTime / 10) % 2 === 0) {
                ctx.globalAlpha = 0.5;
            }
            
            // Disegna shield se attivo
            if (player.shield) {
                ctx.strokeStyle = '#00ff00';
                ctx.lineWidth = 3;
                ctx.shadowBlur = 30;
                ctx.shadowColor = '#00ff00';
                ctx.globalAlpha = 0.6 + Math.sin(Date.now() * 0.01) * 0.2;
                
                ctx.beginPath();
                ctx.arc(0, 0, player.size * 1.5, 0, Math.PI * 2);
                ctx.stroke();
                
                // Esagono interno
                ctx.beginPath();
                for (let i = 0; i < 6; i++) {
                    const angle = (Math.PI * 2 / 6) * i;
                    const x = Math.cos(angle) * player.size * 1.3;
                    const y = Math.sin(angle) * player.size * 1.3;
                    if (i === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                }
                ctx.closePath();
                ctx.stroke();
                
                ctx.globalAlpha = 1;
                ctx.shadowBlur = 0;
            }
            
            // Triangolo/navicella del player
            ctx.fillStyle = '#ff66cc';
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.shadowBlur = 20;
            ctx.shadowColor = '#ff66cc';
            
            ctx.beginPath();
            ctx.moveTo(0, -player.size);
            ctx.lineTo(-player.size * 0.7, player.size * 0.7);
            ctx.lineTo(player.size * 0.7, player.size * 0.7);
            ctx.closePath();
            ctx.fill();
            ctx.stroke();
            
            // Core luminoso
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(0, 0, player.size * 0.3, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.restore();
        }
        
        function drawPowerUps() {
            const lanePositions = [-100, 0, 100];
            
            powerUps.forEach(powerUp => {
                const scale = 1 / (1 + powerUp.z / 100);
                const x = lanePositions[powerUp.lane] * scale;
                const y = -50 * scale;
                const size = 30 * scale;
                const alpha = Math.min(1, 1 - (powerUp.z / tunnel.depth));
                
                ctx.save();
                ctx.globalAlpha = alpha;
                ctx.translate(x, y);
                ctx.rotate(powerUp.rotation);
                
                const powerUpData = powerUpTypes[powerUp.type];
                
                // Alone luminoso
                ctx.shadowBlur = 30 * scale;
                ctx.shadowColor = powerUpData.glow;
                
                // Cerchio esterno rotante
                ctx.strokeStyle = powerUpData.color;
                ctx.lineWidth = 3;
                ctx.beginPath();
                ctx.arc(0, 0, size, 0, Math.PI * 2);
                ctx.stroke();
                
                // Stella interna
                ctx.fillStyle = powerUpData.color;
                ctx.beginPath();
                for (let i = 0; i < 8; i++) {
                    const angle = (Math.PI * 2 / 8) * i;
                    const radius = i % 2 === 0 ? size * 0.8 : size * 0.4;
                    const px = Math.cos(angle) * radius;
                    const py = Math.sin(angle) * radius;
                    if (i === 0) ctx.moveTo(px, py);
                    else ctx.lineTo(px, py);
                }
                ctx.closePath();
                ctx.fill();
                
                // Simbolo centrale
                ctx.font = `${20 * scale}px Arial`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle = '#ffffff';
                ctx.fillText(powerUpData.symbol, 0, 0);
                
                ctx.restore();
            });
        }
        
        function drawActivePowerUps() {
            let yOffset = 150;
            
            // Shield indicator
            if (player.shield) {
                ctx.fillStyle = '#00ff00';
                ctx.font = '16px monospace';
                ctx.textAlign = 'left';
                ctx.fillText('🛡️ SHIELD ACTIVE', 20, yOffset);
                yOffset += 30;
            }
            
            // Score multiplier
            if (player.scoreMultiplier > 1) {
                ctx.fillStyle = '#ff00ff';
                ctx.font = '16px monospace';
                ctx.fillText(`💎 SCORE x${player.scoreMultiplier} (${Math.ceil(player.scoreMultiplierTime / 60)}s)`, 20, yOffset);
                yOffset += 30;
            }
            
            // Slow motion
            if (player.slowMotion) {
                ctx.fillStyle = '#00ffff';
                ctx.font = '16px monospace';
                ctx.fillText(`🌟 SLOW MOTION (${Math.ceil(player.slowMotionTime / 60)}s)`, 20, yOffset);
                yOffset += 30;
            }
            
            // Speed boost (oltre al normale boost)
            if (player.boost && player.boostTime > 120) {
                ctx.fillStyle = '#ffff00';
                ctx.font = '16px monospace';
                ctx.fillText(`⚡ SPEED BOOST (${Math.ceil(player.boostTime / 60)}s)`, 20, yOffset);
            }
        }
        
        function drawParticles() {
            particles.forEach(particle => {
                const scale = 1 / (1 + particle.z / 100);
                const x = particle.x * scale;
                const y = particle.y * scale;
                const size = particle.size * scale;
                
                ctx.globalAlpha = particle.alpha * (particle.life / particle.maxLife);
                ctx.fillStyle = particle.color;
                ctx.fillRect(x - size/2, y - size/2, size, size);
            });
            
            ctx.globalAlpha = 1;
        }
        
        function drawBoostEffect() {
            ctx.save();
            ctx.globalAlpha = 0.3;
            ctx.fillStyle = '#ff66cc';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Linee di velocità
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.globalAlpha = 0.5;
            
            for (let i = 0; i < 20; i++) {
                const x = Math.random() * canvas.width;
                const y = Math.random() * canvas.height;
                const length = 50 + Math.random() * 100;
                
                ctx.beginPath();
                ctx.moveTo(x, y);
                ctx.lineTo(x, y + length);
                ctx.stroke();
            }
            
            ctx.restore();
        }
        
        function drawInvulnerableEffect() {
            ctx.save();
            ctx.globalAlpha = 0.1;
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.restore();
        }
        
        // ===== EFFETTI PARTICELLE =====
        
        function createMoveParticles() {
            for (let i = 0; i < 5; i++) {
                particles.push({
                    x: player.x + (Math.random() - 0.5) * 20,
                    y: player.y - canvas.height/2,
                    z: 0,
                    vx: (Math.random() - 0.5) * 2,
                    vy: Math.random() * -2,
                    size: 5 + Math.random() * 5,
                    color: '#00ffff',
                    alpha: 1,
                    life: 30,
                    maxLife: 30
                });
            }
        }
        
        function createBoostParticles() {
            for (let i = 0; i < 20; i++) {
                particles.push({
                    x: player.x + (Math.random() - 0.5) * 40,
                    y: player.y - canvas.height/2,
                    z: 0,
                    vx: (Math.random() - 0.5) * 5,
                    vy: Math.random() * -5,
                    size: 8 + Math.random() * 8,
                    color: '#ff66cc',
                    alpha: 1,
                    life: 40,
                    maxLife: 40
                });
            }
        }
        
        function createExplosionParticles(x, y) {
            for (let i = 0; i < 30; i++) {
                const angle = (Math.PI * 2 / 30) * i;
                const speed = 5 + Math.random() * 5;
                
                particles.push({
                    x: x,
                    y: y - canvas.height/2,
                    z: 0,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed,
                    size: 10 + Math.random() * 10,
                    color: ['#ff0000', '#ff6600', '#ffff00'][Math.floor(Math.random() * 3)],
                    alpha: 1,
                    life: 60,
                    maxLife: 60
                });
            }
        }
        
        function createPowerUpParticles(x, y, color) {
            for (let i = 0; i < 20; i++) {
                const angle = (Math.PI * 2 / 20) * i;
                const speed = 3 + Math.random() * 3;
                
                particles.push({
                    x: x,
                    y: y - canvas.height/2,
                    z: 0,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed,
                    size: 5 + Math.random() * 5,
                    color: color,
                    alpha: 1,
                    life: 40,
                    maxLife: 40
                });
            }
        }
        
        function createShieldBreakParticles() {
            for (let i = 0; i < 40; i++) {
                const angle = Math.random() * Math.PI * 2;
                const speed = 2 + Math.random() * 8;
                
                particles.push({
                    x: player.x,
                    y: player.y - canvas.height/2,
                    z: 0,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed,
                    size: 3 + Math.random() * 7,
                    color: '#00ff00',
                    alpha: 1,
                    life: 50,
                    maxLife: 50
                });
            }
        }
        
        function createLaneSwapEffect() {
            // Effetto visivo per lane swap
            for (let lane = 0; lane < 3; lane++) {
                const laneX = [-100, 0, 100][lane];
                for (let i = 0; i < 10; i++) {
                    particles.push({
                        x: laneX,
                        y: -canvas.height/2 + i * canvas.height/10,
                        z: 0,
                        vx: 0,
                        vy: 0,
                        size: 20,
                        color: '#ff8800',
                        alpha: 0.5,
                        life: 30,
                        maxLife: 30
                    });
                }
            }
        }
        
        // ===== GESTIONE UI E CHECKPOINT =====
        
        function updateUI() {
            document.getElementById('score').textContent = Math.floor(score);
            document.getElementById('distance').textContent = Math.floor(distance);
        }
        
        function updateLivesDisplay() {
            const livesContainer = document.getElementById('lives');
            livesContainer.innerHTML = '';
            
            for (let i = 0; i < lives; i++) {
                const life = document.createElement('span');
                life.className = 'life';
                livesContainer.appendChild(life);
            }
        }
        
        function checkCheckpoint() {
            const checkpointInterval = 30000; // 30 secondi
            const currentCheckpoint = Math.floor(distance / 300); // Ogni 300m
            
            if (currentCheckpoint > lastCheckpoint) {
                lastCheckpoint = currentCheckpoint;
                showCheckpoint();
                
                // Bonus punti
                score += 1000;
                
                // Piccolo heal
                if (lives < 3) {
                    lives++;
                    updateLivesDisplay();
                }
            }
        }
        
        function showCheckpoint() {
            const checkpoint = document.getElementById('checkpoint');
            checkpoint.classList.add('checkpoint-active');
            
            setTimeout(() => {
                checkpoint.classList.remove('checkpoint-active');
            }, 2000);
        }
        
        // ===== GAME OVER =====
        
        function gameOver() {
            gameRunning = false;
            
            if (animationId) {
                cancelAnimationFrame(animationId);
            }
            
            document.getElementById('finalScore').textContent = Math.floor(score);
            document.getElementById('finalDistance').textContent = Math.floor(distance);
            document.getElementById('gameOverMenu').style.display = 'block';
        }
        
        // ===== PREVENZIONE SCROLL E ZOOM MOBILE =====
        
        document.addEventListener('touchmove', (e) => {
            e.preventDefault();
        }, { passive: false });
        
        document.addEventListener('gesturestart', (e) => {
            e.preventDefault();
        });
        
        // Previeni doppio tap zoom
        let lastTouchEnd = 0;
        document.addEventListener('touchend', (e) => {
            const now = Date.now();
            if (now - lastTouchEnd <= 300) {
                e.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
</body>
</html>
