<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Packet Runner - G Tech Arcade</title>
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
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background: #0e0e20;
            color: #00ffff;
            font-family: 'Courier New', monospace;
            overflow: hidden;
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

        #ui {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        #score {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 18px;
            text-shadow: 0 0 10px #00ffff;
        }

        #timer {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 18px;
            text-shadow: 0 0 10px #ff0033;
        }

        #level {
            position: absolute;
            top: 40px;
            left: 10px;
            font-size: 16px;
            text-shadow: 0 0 8px #ffee00;
        }

        #startScreen, #gameOverScreen {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: rgba(14, 14, 32, 0.95);
            padding: 30px;
            border: 2px solid #00ffff;
            border-radius: 10px;
            box-shadow: 0 0 30px #00ffff;
            pointer-events: all;
        }

        h1 {
            color: #00ffff;
            margin-bottom: 20px;
            text-shadow: 0 0 20px #00ffff;
        }

        button {
            background: #00ffff;
            color: #0e0e20;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            font-family: inherit;
            cursor: pointer;
            margin: 5px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        button:hover {
            background: #ffee00;
            transform: scale(1.1);
            box-shadow: 0 0 20px #ffee00;
        }

        .instructions {
            margin: 20px 0;
            font-size: 14px;
            line-height: 1.5;
        }

        #touchControls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: none;
            pointer-events: all;
        }

        .touchButton {
            position: absolute;
            width: 60px;
            height: 60px;
            background: rgba(0, 255, 255, 0.2);
            border: 2px solid #00ffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #00ffff;
            transition: all 0.1s;
        }

        .touchButton:active {
            background: rgba(0, 255, 255, 0.5);
            transform: scale(0.9);
        }

        #upBtn { top: -70px; left: 0; }
        #downBtn { top: 70px; left: 0; }
        #leftBtn { top: 0; left: -70px; }
        #rightBtn { top: 0; left: 70px; }

        @media (hover: none) and (pointer: coarse) {
            #touchControls {
                display: block;
            }
        }

        .hidden {
            display: none !important;
        }

        #packetStatus {
            position: absolute;
            bottom: 10px;
            left: 10px;
            font-size: 14px;
            color: #ffee00;
        }

        #activePowerUps {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            pointer-events: none;
            z-index: 10;
        }
        
        .powerup-indicator {
            background: rgba(14, 14, 32, 0.9);
            border: 2px solid;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 0 10px currentColor;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }
        
        #latencyIndicator {
            position: absolute;
            top: 70px;
            left: 10px;
            font-size: 14px;
            color: #ff8800;
        }
    </style>
</head>
<body>
    <canvas id="gameCanvas"></canvas>
    
    <div id="ui">
        <div id="score">Hops: 0</div>
        <div id="timer">Time: 60s</div>
        <div id="level">Level: 1</div>
        <div id="latencyIndicator">Ping: 0ms</div>
        <div id="packetStatus">Status: ACTIVE</div>
        <div id="activePowerUps"></div>
        
        <div id="startScreen">
            <h1>PACKET RUNNER</h1>
            <div class="instructions">
                <p>Guide your TCP/IP packet through the network!</p>
                <p>🎯 Reach the destination host</p>
                <p>⚠️ Avoid routers, firewalls, and timeouts</p>
                <p>⏱️ Beat the clock!</p>
                <br>
                <p><strong>Power-ups:</strong></p>
                <p>⚡ Speed Boost | 🛡️ Shield | 📦 Compress</p>
                <p>🎯 Multi-hop | ❄️ Freeze</p>
                <br>
                <p>Desktop: Use WASD or Arrow keys</p>
                <p>Mobile: Swipe or use buttons</p>
            </div>
            <button onclick="startGame()">START TRANSMISSION</button>
        </div>
        
        <div id="gameOverScreen" class="hidden">
            <h1 id="gameOverTitle">PACKET LOST!</h1>
            <p id="gameOverMessage"></p>
            <p>Score: <span id="finalScore">0</span></p>
            <p>Power-ups Collected: <span id="powerUpsCollected">0</span></p>
            <button onclick="restartGame()">RETRANSMIT</button>
            <button onclick="location.href='../index.php'">MAIN MENU</button>
        </div>
        
        <div id="touchControls">
            <div class="touchButton" id="upBtn">↑</div>
            <div class="touchButton" id="downBtn">↓</div>
            <div class="touchButton" id="leftBtn">←</div>
            <div class="touchButton" id="rightBtn">→</div>
        </div>
    </div>

    <script>
        // ===== GAME VARIABLES =====
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        
        let gameState = 'menu'; // menu, playing, gameOver
        let currentLevel = 1;
        let score = 0;
        let timeLeft = 60;
        let gameTimer = null;
        let animationId = null;
        
        // Grid settings
        const GRID_SIZE = 15;
        let cellSize = 40;
        let gridOffsetX = 0;
        let gridOffsetY = 0;
        
        // Player (packet)
        let packet = {
            x: 1,
            y: 1,
            targetX: 1,
            targetY: 1,
            moving: false,
            moveSpeed: 0.2,
            trail: [],
            status: 'active',
            corruption: 0,
            retransmissions: 0,
            hops: 0,
            glow: 0,
            glowDirection: 1
        };
        
        // Level data
        let level = {
            grid: [],
            destination: { x: 13, y: 13 },
            routers: [],
            firewalls: [],
            natTunnels: [],
            highPingZones: [],
            packetLossZones: [],
            powerUps: []
        };
        
        // Power-up types
        const POWERUP_TYPES = {
            SPEED: { icon: '⚡', color: '#ffff00', duration: 300 },
            SHIELD: { icon: '🛡️', color: '#00ff00', duration: 0 },
            COMPRESS: { icon: '📦', color: '#ff00ff', duration: 200 },
            MULTIHOP: { icon: '🎯', color: '#00ffff', duration: 150 },
            FREEZE: { icon: '❄️', color: '#ffffff', duration: 300 }
        };
        
        // Active power-ups
        let activePowerUps = {
            speed: 0,
            shield: false,
            compress: 0,
            multihop: 0,
            freeze: 0
        };
        
        // Stats tracking
        let stats = {
            powerUpsCollected: 0,
            routersPassed: 0,
            perfectMoves: 0
        };
        
        // Animation variables
        let particles = [];
        let networkLines = [];
        let pulseAnimation = 0;
        
        // Touch/Input handling
        let touchStartX = null;
        let touchStartY = null;
        let keys = {};
        
        // ===== INITIALIZATION =====
        function init() {
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
            
            // Keyboard controls
            document.addEventListener('keydown', (e) => {
                keys[e.key.toLowerCase()] = true;
                handleKeyboard(e.key.toLowerCase());
            });
            
            document.addEventListener('keyup', (e) => {
                keys[e.key.toLowerCase()] = false;
            });
            
            // Touch controls
            setupTouchControls();
            
            // Initialize network background
            initNetworkBackground();
            
            // Start animation loop
            animate();
        }
        
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            // Calculate cell size based on screen size
            const maxCellWidth = Math.floor((canvas.width - 40) / GRID_SIZE);
            const maxCellHeight = Math.floor((canvas.height - 120) / GRID_SIZE);
            cellSize = Math.min(maxCellWidth, maxCellHeight, 50);
            
            // Center the grid
            gridOffsetX = (canvas.width - (GRID_SIZE * cellSize)) / 2;
            gridOffsetY = (canvas.height - (GRID_SIZE * cellSize)) / 2 + 40;
        }
        
        function setupTouchControls() {
            // Touch swipe controls
            canvas.addEventListener('touchstart', (e) => {
                const touch = e.touches[0];
                touchStartX = touch.clientX;
                touchStartY = touch.clientY;
            });
            
            canvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
            });
            
            canvas.addEventListener('touchend', (e) => {
                if (!touchStartX || !touchStartY || gameState !== 'playing') return;
                
                const touchEndX = e.changedTouches[0].clientX;
                const touchEndY = e.changedTouches[0].clientY;
                
                const diffX = touchEndX - touchStartX;
                const diffY = touchEndY - touchStartY;
                
                if (Math.abs(diffX) > Math.abs(diffY)) {
                    if (diffX > 30) movePacket(1, 0);
                    else if (diffX < -30) movePacket(-1, 0);
                } else {
                    if (diffY > 30) movePacket(0, 1);
                    else if (diffY < -30) movePacket(0, -1);
                }
                
                touchStartX = null;
                touchStartY = null;
            });
            
            // Virtual button controls
            document.getElementById('upBtn').addEventListener('click', () => movePacket(0, -1));
            document.getElementById('downBtn').addEventListener('click', () => movePacket(0, 1));
            document.getElementById('leftBtn').addEventListener('click', () => movePacket(-1, 0));
            document.getElementById('rightBtn').addEventListener('click', () => movePacket(1, 0));
        }
        
        function handleKeyboard(key) {
            if (gameState !== 'playing') return;
            
            switch(key) {
                case 'arrowup':
                case 'w':
                    movePacket(0, -1);
                    break;
                case 'arrowdown':
                case 's':
                    movePacket(0, 1);
                    break;
                case 'arrowleft':
                case 'a':
                    movePacket(-1, 0);
                    break;
                case 'arrowright':
                case 'd':
                    movePacket(1, 0);
                    break;
            }
        }
        
        // ===== GAME LOGIC =====
        function startGame() {
            document.getElementById('startScreen').classList.add('hidden');
            document.getElementById('gameOverScreen').classList.add('hidden');
            
            gameState = 'playing';
            currentLevel = 1;
            score = 0;
            timeLeft = 60;
            
            // Reset stats
            stats = {
                powerUpsCollected: 0,
                routersPassed: 0,
                perfectMoves: 0
            };
            
            initLevel();
            startTimer();
        }
        
        function restartGame() {
            startGame();
        }
        
        function initLevel() {
            // Reset packet
            packet = {
                x: 1,
                y: 1,
                targetX: 1,
                targetY: 1,
                moving: false,
                moveSpeed: 0.2,
                trail: [],
                status: 'active',
                corruption: 0,
                retransmissions: 0,
                hops: 0,
                glow: 0,
                glowDirection: 1
            };
            
            // Reset power-ups
            activePowerUps = {
                speed: 0,
                shield: false,
                compress: 0,
                multihop: 0,
                freeze: 0
            };
            packet.moveSpeed = 0.2;
            
            // Initialize empty grid
            level.grid = [];
            for (let y = 0; y < GRID_SIZE; y++) {
                level.grid[y] = [];
                for (let x = 0; x < GRID_SIZE; x++) {
                    level.grid[y][x] = 0; // 0 = empty
                }
            }
            
            // Set destination
            level.destination = { x: GRID_SIZE - 2, y: GRID_SIZE - 2 };
            
            // Clear obstacles
            level.routers = [];
            level.firewalls = [];
            level.natTunnels = [];
            level.highPingZones = [];
            level.packetLossZones = [];
            
            // Generate level based on difficulty
            generateLevel();
        }
        
        function generateLevel() {
            const difficulty = Math.min(currentLevel, 10);
            
            // Add routers (block movement)
            const routerCount = 5 + difficulty * 2;
            for (let i = 0; i < routerCount; i++) {
                let x, y;
                do {
                    x = Math.floor(Math.random() * GRID_SIZE);
                    y = Math.floor(Math.random() * GRID_SIZE);
                } while (
                    (x === 1 && y === 1) || // Start position
                    (x === level.destination.x && y === level.destination.y) || // End position
                    level.grid[y][x] !== 0
                );
                
                level.routers.push({
                    x: x,
                    y: y,
                    unstable: Math.random() < 0.3, // 30% chance to be unstable
                    blockTimer: 0,
                    maxBlockTime: 60 + Math.random() * 60
                });
                level.grid[y][x] = 1;
            }
            
            // Add firewalls (moving obstacles)
            const firewallCount = Math.floor(difficulty / 2);
            for (let i = 0; i < firewallCount; i++) {
                level.firewalls.push({
                    x: Math.floor(Math.random() * GRID_SIZE),
                    y: Math.floor(Math.random() * GRID_SIZE),
                    direction: Math.random() < 0.5 ? 'horizontal' : 'vertical',
                    speed: 0.02 + Math.random() * 0.03,
                    range: 3 + Math.floor(Math.random() * 4)
                });
            }
            
            // Add NAT tunnels (change direction)
            const natCount = 2 + Math.floor(difficulty / 3);
            for (let i = 0; i < natCount; i++) {
                let x, y;
                do {
                    x = Math.floor(Math.random() * GRID_SIZE);
                    y = Math.floor(Math.random() * GRID_SIZE);
                } while (level.grid[y][x] !== 0);
                
                level.natTunnels.push({
                    x: x,
                    y: y,
                    exitX: Math.floor(Math.random() * GRID_SIZE),
                    exitY: Math.floor(Math.random() * GRID_SIZE)
                });
            }
            
            // Add high ping zones (slow movement)
            const pingZoneCount = 1 + Math.floor(difficulty / 4);
            for (let i = 0; i < pingZoneCount; i++) {
                level.highPingZones.push({
                    x: Math.floor(Math.random() * (GRID_SIZE - 3)),
                    y: Math.floor(Math.random() * (GRID_SIZE - 3)),
                    width: 3 + Math.floor(Math.random() * 3),
                    height: 3 + Math.floor(Math.random() * 3),
                    latency: 100 + Math.random() * 200
                });
            }
            
            // Add power-ups
            const powerUpCount = 2 + Math.floor(difficulty / 3);
            const powerUpTypes = Object.keys(POWERUP_TYPES);
            
            for (let i = 0; i < powerUpCount; i++) {
                let x, y;
                do {
                    x = Math.floor(Math.random() * GRID_SIZE);
                    y = Math.floor(Math.random() * GRID_SIZE);
                } while (
                    (x === 1 && y === 1) ||
                    (x === level.destination.x && y === level.destination.y) ||
                    level.grid[y][x] !== 0
                );
                
                const type = powerUpTypes[Math.floor(Math.random() * powerUpTypes.length)];
                level.powerUps.push({
                    x: x,
                    y: y,
                    type: type,
                    collected: false,
                    pulse: 0
                });
            }
        }
        
        function movePacket(dx, dy) {
            if (packet.moving || packet.status !== 'active') return;
            
            const newX = packet.x + dx;
            const newY = packet.y + dy;
            
            // Check boundaries
            if (newX < 0 || newX >= GRID_SIZE || newY < 0 || newY >= GRID_SIZE) {
                return;
            }
            
            // Check for router collision
            for (let router of level.routers) {
                if (router.x === newX && router.y === newY) {
                    // Check if router is temporarily passable (unstable) or if we have compress power-up
                    if (activePowerUps.compress > 0) {
                        // Pass through with compress!
                        createCompressEffect(newX, newY);
                    } else if (!router.unstable || router.blockTimer > router.maxBlockTime / 2) {
                        // Blocked!
                        if (activePowerUps.shield) {
                            // Shield protects us
                            activePowerUps.shield = false;
                            createShieldBreakEffect(newX, newY);
                        } else {
                            createCollisionEffect(newX, newY);
                            packet.corruption += 5;
                            updatePacketStatus();
                            return;
                        }
                    }
                }
            }
            
            // Check firewall collision
            for (let firewall of level.firewalls) {
                const fwX = Math.floor(firewall.x);
                const fwY = Math.floor(firewall.y);
                if (fwX === newX && fwY === newY) {
                    if (activePowerUps.shield) {
                        // Shield protects us
                        activePowerUps.shield = false;
                        createShieldBreakEffect(newX, newY);
                    } else {
                        // Firewall hit!
                        packet.status = 'blocked';
                        packet.retransmissions++;
                        createFirewallEffect(newX, newY);
                        setTimeout(() => {
                            packet.x = 1;
                            packet.y = 1;
                            packet.targetX = 1;
                            packet.targetY = 1;
                            packet.status = 'active';
                            packet.trail = [];
                            updatePacketStatus();
                        }, 1000);
                        return;
                    }
                }
            }
            
            // Check NAT tunnel
            for (let nat of level.natTunnels) {
                if (nat.x === newX && nat.y === newY) {
                    // Teleport through NAT
                    packet.targetX = nat.exitX;
                    packet.targetY = nat.exitY;
                    packet.moving = true;
                    packet.hops++;
                    createNATEffect(newX, newY, nat.exitX, nat.exitY);
                    updateUI();
                    return;
                }
            }
            
            // Move is valid
            if (activePowerUps.multihop > 0) {
                // Check if we can jump 2 squares
                const jumpX = packet.x + dx * 2;
                const jumpY = packet.y + dy * 2;
                
                if (jumpX >= 0 && jumpX < GRID_SIZE && jumpY >= 0 && jumpY < GRID_SIZE) {
                    packet.targetX = jumpX;
                    packet.targetY = jumpY;
                } else {
                    // Fall back to normal move if out of bounds
                    packet.targetX = newX;
                    packet.targetY = newY;
                }
            } else {
                packet.targetX = newX;
                packet.targetY = newY;
            }
            
            packet.moving = true;
            packet.hops++;
            
            // Check for power-up collection
            level.powerUps.forEach(powerUp => {
                if (!powerUp.collected && powerUp.x === packet.targetX && powerUp.y === packet.targetY) {
                    collectPowerUp(powerUp);
                }
            });
            
            // Add to trail
            packet.trail.push({ x: packet.x, y: packet.y, life: 20 });
            if (packet.trail.length > 10) {
                packet.trail.shift();
            }
            
            updateUI();
        }
        
        function collectPowerUp(powerUp) {
            powerUp.collected = true;
            stats.powerUpsCollected++;
            createPowerUpEffect(powerUp.x, powerUp.y, POWERUP_TYPES[powerUp.type].color);
            
            switch(powerUp.type) {
                case 'SPEED':
                    activePowerUps.speed = POWERUP_TYPES.SPEED.duration;
                    packet.moveSpeed = 0.4; // Double speed
                    break;
                case 'SHIELD':
                    activePowerUps.shield = true;
                    break;
                case 'COMPRESS':
                    activePowerUps.compress = POWERUP_TYPES.COMPRESS.duration;
                    break;
                case 'MULTIHOP':
                    activePowerUps.multihop = POWERUP_TYPES.MULTIHOP.duration;
                    break;
                case 'FREEZE':
                    activePowerUps.freeze = POWERUP_TYPES.FREEZE.duration;
                    break;
            }
            
            score += 250;
            updateUI();
        }
        
        function updatePowerUps() {
            // Update power-up timers
            if (activePowerUps.speed > 0) {
                activePowerUps.speed--;
                if (activePowerUps.speed === 0) {
                    packet.moveSpeed = 0.2; // Reset to normal speed
                }
            }
            
            if (activePowerUps.compress > 0) {
                activePowerUps.compress--;
            }
            
            if (activePowerUps.multihop > 0) {
                activePowerUps.multihop--;
            }
            
            if (activePowerUps.freeze > 0) {
                activePowerUps.freeze--;
            }
            
            // Update power-up pulse animation
            level.powerUps.forEach(powerUp => {
                if (!powerUp.collected) {
                    powerUp.pulse += 0.1;
                }
            });
        }
        
        function updatePacket() {
            // Smooth movement
            if (packet.moving) {
                const dx = packet.targetX - packet.x;
                const dy = packet.targetY - packet.y;
                
                // Check if in high ping zone
                let moveSpeed = packet.moveSpeed;
                for (let zone of level.highPingZones) {
                    if (packet.x >= zone.x && packet.x < zone.x + zone.width &&
                        packet.y >= zone.y && packet.y < zone.y + zone.height) {
                        moveSpeed *= 0.3; // Slow down in high ping zone
                        document.getElementById('latencyIndicator').textContent = `Ping: ${Math.floor(zone.latency)}ms`;
                        document.getElementById('latencyIndicator').style.color = '#ff0033';
                        break;
                    } else {
                        document.getElementById('latencyIndicator').textContent = 'Ping: 20ms';
                        document.getElementById('latencyIndicator').style.color = '#00ff00';
                    }
                }
                
                if (Math.abs(dx) > 0.1) {
                    packet.x += dx * moveSpeed;
                } else {
                    packet.x = packet.targetX;
                }
                
                if (Math.abs(dy) > 0.1) {
                    packet.y += dy * moveSpeed;
                } else {
                    packet.y = packet.targetY;
                }
                
                if (packet.x === packet.targetX && packet.y === packet.targetY) {
                    packet.moving = false;
                    
                    // Check if reached destination
                    if (packet.x === level.destination.x && packet.y === level.destination.y) {
                        levelComplete();
                    }
                    
                    // Check packet loss zone
                    for (let zone of level.packetLossZones) {
                        if (packet.x >= zone.x && packet.x < zone.x + zone.width &&
                            packet.y >= zone.y && packet.y < zone.y + zone.height) {
                            if (Math.random() < zone.lossRate) {
                                // Packet lost!
                                packet.status = 'lost';
                                createPacketLossEffect(packet.x, packet.y);
                                setTimeout(() => {
                                    packet.x = 1;
                                    packet.y = 1;
                                    packet.targetX = 1;
                                    packet.targetY = 1;
                                    packet.status = 'active';
                                    packet.retransmissions++;
                                    packet.trail = [];
                                    updatePacketStatus();
                                }, 1500);
                            }
                        }
                    }
                }
            }
            
            // Update glow animation
            packet.glow += 0.05 * packet.glowDirection;
            if (packet.glow > 1 || packet.glow < 0) {
                packet.glowDirection *= -1;
            }
            
            // Update trail
            packet.trail = packet.trail.filter(t => {
                t.life--;
                return t.life > 0;
            });
            
            // Check corruption
            if (packet.corruption >= 100) {
                gameOver('Packet corrupted! Too many collisions.');
            }
        }
        
        function updateObstacles() {
            // Update unstable routers
            level.routers.forEach(router => {
                if (router.unstable) {
                    router.blockTimer = (router.blockTimer + 1) % router.maxBlockTime;
                }
            });
            
            // Update moving firewalls (freeze if power-up active)
            if (activePowerUps.freeze === 0) {
                level.firewalls.forEach(firewall => {
                    if (firewall.direction === 'horizontal') {
                        firewall.x += firewall.speed;
                        if (firewall.x > GRID_SIZE - 1 || firewall.x < 0) {
                            firewall.speed *= -1;
                        }
                    } else {
                        firewall.y += firewall.speed;
                        if (firewall.y > GRID_SIZE - 1 || firewall.y < 0) {
                            firewall.speed *= -1;
                        }
                    }
                });
            }
        }
        
        function updatePacketStatus() {
            const statusText = `Status: ${packet.status.toUpperCase()} | Corruption: ${packet.corruption}% | Retrans: ${packet.retransmissions}`;
            document.getElementById('packetStatus').textContent = statusText;
            
            if (packet.corruption > 50) {
                document.getElementById('packetStatus').style.color = '#ff0033';
            } else if (packet.corruption > 25) {
                document.getElementById('packetStatus').style.color = '#ff8800';
            } else {
                document.getElementById('packetStatus').style.color = '#ffee00';
            }
        }
        
        function levelComplete() {
            // Count uncollected power-ups for bonus
            const uncollectedPowerUps = level.powerUps.filter(p => !p.collected).length;
            const powerUpBonus = (level.powerUps.length - uncollectedPowerUps) * 100;
            
            score += 1000 + (timeLeft * 10) - (packet.retransmissions * 50) - (packet.corruption * 5) + powerUpBonus;
            currentLevel++;
            
            // Bonus time for next level
            timeLeft = Math.min(timeLeft + 30, 90);
            
            createSuccessEffect();
            
            setTimeout(() => {
                initLevel();
            }, 1500);
        }
        
        function gameOver(message) {
            gameState = 'gameOver';
            clearInterval(gameTimer);
            
            document.getElementById('gameOverTitle').textContent = timeLeft <= 0 ? 'TIMEOUT!' : 'PACKET LOST!';
            document.getElementById('gameOverMessage').textContent = message || 'Connection terminated.';
            document.getElementById('finalScore').textContent = score;
            document.getElementById('powerUpsCollected').textContent = stats.powerUpsCollected;
            document.getElementById('gameOverScreen').classList.remove('hidden');
        }
        
        function startTimer() {
            if (gameTimer) clearInterval(gameTimer);
            
            gameTimer = setInterval(() => {
                timeLeft--;
                updateUI();
                
                if (timeLeft <= 0) {
                    gameOver('Connection timeout! Time limit exceeded.');
                }
            }, 1000);
        }
        
        function updateUI() {
            document.getElementById('score').textContent = `Hops: ${packet.hops}`;
            document.getElementById('timer').textContent = `Time: ${timeLeft}s`;
            document.getElementById('level').textContent = `Level: ${currentLevel}`;
            
            if (timeLeft <= 10) {
                document.getElementById('timer').style.color = '#ff0033';
            } else if (timeLeft <= 30) {
                document.getElementById('timer').style.color = '#ff8800';
            } else {
                document.getElementById('timer').style.color = '#00ff00';
            }
            
            // Update active power-ups display
            const powerUpsDiv = document.getElementById('activePowerUps');
            powerUpsDiv.innerHTML = '';
            
            if (activePowerUps.speed > 0) {
                const div = document.createElement('div');
                div.className = 'powerup-indicator';
                div.style.borderColor = POWERUP_TYPES.SPEED.color;
                div.style.color = POWERUP_TYPES.SPEED.color;
                div.innerHTML = `${POWERUP_TYPES.SPEED.icon} ${Math.ceil(activePowerUps.speed / 60)}s`;
                powerUpsDiv.appendChild(div);
            }
            
            if (activePowerUps.shield) {
                const div = document.createElement('div');
                div.className = 'powerup-indicator';
                div.style.borderColor = POWERUP_TYPES.SHIELD.color;
                div.style.color = POWERUP_TYPES.SHIELD.color;
                div.innerHTML = `${POWERUP_TYPES.SHIELD.icon} Active`;
                powerUpsDiv.appendChild(div);
            }
            
            if (activePowerUps.compress > 0) {
                const div = document.createElement('div');
                div.className = 'powerup-indicator';
                div.style.borderColor = POWERUP_TYPES.COMPRESS.color;
                div.style.color = POWERUP_TYPES.COMPRESS.color;
                div.innerHTML = `${POWERUP_TYPES.COMPRESS.icon} ${Math.ceil(activePowerUps.compress / 60)}s`;
                powerUpsDiv.appendChild(div);
            }
            
            if (activePowerUps.multihop > 0) {
                const div = document.createElement('div');
                div.className = 'powerup-indicator';
                div.style.borderColor = POWERUP_TYPES.MULTIHOP.color;
                div.style.color = POWERUP_TYPES.MULTIHOP.color;
                div.innerHTML = `${POWERUP_TYPES.MULTIHOP.icon} ${Math.ceil(activePowerUps.multihop / 60)}s`;
                powerUpsDiv.appendChild(div);
            }
            
            if (activePowerUps.freeze > 0) {
                const div = document.createElement('div');
                div.className = 'powerup-indicator';
                div.style.borderColor = POWERUP_TYPES.FREEZE.color;
                div.style.color = POWERUP_TYPES.FREEZE.color;
                div.innerHTML = `${POWERUP_TYPES.FREEZE.icon} ${Math.ceil(activePowerUps.freeze / 60)}s`;
                powerUpsDiv.appendChild(div);
            }
        }
        
        // ===== NETWORK BACKGROUND =====
        function initNetworkBackground() {
            for (let i = 0; i < 20; i++) {
                networkLines.push({
                    x1: Math.random() * canvas.width,
                    y1: Math.random() * canvas.height,
                    x2: Math.random() * canvas.width,
                    y2: Math.random() * canvas.height,
                    speed: 0.1 + Math.random() * 0.3,
                    opacity: Math.random() * 0.3
                });
            }
        }
        
        function updateNetworkBackground() {
            networkLines.forEach(line => {
                line.opacity = Math.sin(Date.now() * 0.001 * line.speed) * 0.3 + 0.3;
            });
        }
        
        // ===== PARTICLE EFFECTS =====
        function createCollisionEffect(x, y) {
            for (let i = 0; i < 10; i++) {
                particles.push({
                    x: gridOffsetX + x * cellSize + cellSize / 2,
                    y: gridOffsetY + y * cellSize + cellSize / 2,
                    vx: (Math.random() - 0.5) * 4,
                    vy: (Math.random() - 0.5) * 4,
                    life: 30,
                    color: '#ff0033',
                    size: 3 + Math.random() * 3
                });
            }
        }
        
        function createFirewallEffect(x, y) {
            for (let i = 0; i < 20; i++) {
                particles.push({
                    x: gridOffsetX + x * cellSize + cellSize / 2,
                    y: gridOffsetY + y * cellSize + cellSize / 2,
                    vx: (Math.random() - 0.5) * 6,
                    vy: (Math.random() - 0.5) * 6,
                    life: 40,
                    color: '#ff8800',
                    size: 4 + Math.random() * 4
                });
            }
        }
        
        function createNATEffect(x1, y1, x2, y2) {
            const steps = 10;
            for (let i = 0; i < steps; i++) {
                setTimeout(() => {
                    particles.push({
                        x: gridOffsetX + (x1 + (x2 - x1) * i / steps) * cellSize + cellSize / 2,
                        y: gridOffsetY + (y1 + (y2 - y1) * i / steps) * cellSize + cellSize / 2,
                        vx: 0,
                        vy: 0,
                        life: 20,
                        color: '#00ffff',
                        size: 5
                    });
                }, i * 30);
            }
        }
        
        function createPacketLossEffect(x, y) {
            for (let i = 0; i < 15; i++) {
                particles.push({
                    x: gridOffsetX + x * cellSize + cellSize / 2,
                    y: gridOffsetY + y * cellSize + cellSize / 2,
                    vx: (Math.random() - 0.5) * 8,
                    vy: (Math.random() - 0.5) * 8,
                    life: 50,
                    color: '#ff0033',
                    size: 2 + Math.random() * 6,
                    type: 'fade'
                });
            }
        }
        
        function createSuccessEffect() {
            for (let i = 0; i < 30; i++) {
                particles.push({
                    x: gridOffsetX + level.destination.x * cellSize + cellSize / 2,
                    y: gridOffsetY + level.destination.y * cellSize + cellSize / 2,
                    vx: (Math.random() - 0.5) * 10,
                    vy: (Math.random() - 0.5) * 10,
                    life: 60,
                    color: '#00ff00',
                    size: 5 + Math.random() * 5
                });
            }
        }
        
        function createPowerUpEffect(x, y, color) {
            for (let i = 0; i < 20; i++) {
                particles.push({
                    x: gridOffsetX + x * cellSize + cellSize / 2,
                    y: gridOffsetY + y * cellSize + cellSize / 2,
                    vx: (Math.random() - 0.5) * 8,
                    vy: (Math.random() - 0.5) * 8,
                    life: 40,
                    color: color,
                    size: 6 + Math.random() * 4,
                    type: 'sparkle'
                });
            }
        }
        
        function createShieldBreakEffect(x, y) {
            for (let i = 0; i < 15; i++) {
                const angle = (i / 15) * Math.PI * 2;
                particles.push({
                    x: gridOffsetX + x * cellSize + cellSize / 2,
                    y: gridOffsetY + y * cellSize + cellSize / 2,
                    vx: Math.cos(angle) * 5,
                    vy: Math.sin(angle) * 5,
                    life: 30,
                    color: '#00ff00',
                    size: 4
                });
            }
        }
        
        function createCompressEffect(x, y) {
            for (let i = 0; i < 10; i++) {
                particles.push({
                    x: gridOffsetX + x * cellSize + cellSize / 2,
                    y: gridOffsetY + y * cellSize + cellSize / 2,
                    vx: 0,
                    vy: (Math.random() - 0.5) * 2,
                    life: 20,
                    color: '#ff00ff',
                    size: 3,
                    type: 'compress'
                });
            }
        }
        
        function updateParticles() {
            particles = particles.filter(p => {
                p.x += p.vx;
                p.y += p.vy;
                p.life--;
                
                if (p.type === 'fade') {
                    p.size *= 0.95;
                }
                
                return p.life > 0;
            });
        }
        
        // ===== RENDERING =====
        function render() {
            // Clear canvas
            ctx.fillStyle = '#0e0e20';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Draw network background
            drawNetworkBackground();
            
            if (gameState === 'playing') {
                // Draw grid
                drawGrid();
                
                // Draw obstacles
                drawObstacles();
                
                // Draw power-ups
                drawPowerUps();
                
                // Draw destination
                drawDestination();
                
                // Draw packet
                drawPacket();
                
                // Draw particles
                drawParticles();
            }
        }
        
        function drawNetworkBackground() {
            ctx.strokeStyle = '#00ffff';
            ctx.lineWidth = 1;
            
            networkLines.forEach(line => {
                ctx.globalAlpha = line.opacity * 0.2;
                ctx.beginPath();
                ctx.moveTo(line.x1, line.y1);
                ctx.lineTo(line.x2, line.y2);
                ctx.stroke();
            });
            
            ctx.globalAlpha = 1;
        }
        
        function drawGrid() {
            // Draw grid lines
            ctx.strokeStyle = '#00ffff';
            ctx.lineWidth = 1;
            ctx.globalAlpha = 0.1;
            
            for (let x = 0; x <= GRID_SIZE; x++) {
                ctx.beginPath();
                ctx.moveTo(gridOffsetX + x * cellSize, gridOffsetY);
                ctx.lineTo(gridOffsetX + x * cellSize, gridOffsetY + GRID_SIZE * cellSize);
                ctx.stroke();
            }
            
            for (let y = 0; y <= GRID_SIZE; y++) {
                ctx.beginPath();
                ctx.moveTo(gridOffsetX, gridOffsetY + y * cellSize);
                ctx.lineTo(gridOffsetX + GRID_SIZE * cellSize, gridOffsetY + y * cellSize);
                ctx.stroke();
            }
            
            ctx.globalAlpha = 1;
            
            // Draw zones
            // High ping zones
            level.highPingZones.forEach(zone => {
                ctx.fillStyle = 'rgba(255, 136, 0, 0.2)';
                ctx.fillRect(
                    gridOffsetX + zone.x * cellSize,
                    gridOffsetY + zone.y * cellSize,
                    zone.width * cellSize,
                    zone.height * cellSize
                );
                
                ctx.strokeStyle = '#ff8800';
                ctx.lineWidth = 2;
                ctx.strokeRect(
                    gridOffsetX + zone.x * cellSize,
                    gridOffsetY + zone.y * cellSize,
                    zone.width * cellSize,
                    zone.height * cellSize
                );
            });
            
            // Packet loss zones
            level.packetLossZones.forEach(zone => {
                ctx.fillStyle = 'rgba(255, 0, 51, 0.2)';
                ctx.fillRect(
                    gridOffsetX + zone.x * cellSize,
                    gridOffsetY + zone.y * cellSize,
                    zone.width * cellSize,
                    zone.height * cellSize
                );
                
                // Draw warning pattern
                ctx.strokeStyle = '#ff0033';
                ctx.lineWidth = 1;
                ctx.setLineDash([5, 5]);
                ctx.strokeRect(
                    gridOffsetX + zone.x * cellSize,
                    gridOffsetY + zone.y * cellSize,
                    zone.width * cellSize,
                    zone.height * cellSize
                );
                ctx.setLineDash([]);
            });
        }
        
        function drawObstacles() {
            // Draw routers
            level.routers.forEach(router => {
                const x = gridOffsetX + router.x * cellSize + cellSize / 2;
                const y = gridOffsetY + router.y * cellSize + cellSize / 2;
                
                ctx.save();
                ctx.translate(x, y);
                
                // Router body
                if (router.unstable) {
                    // Unstable router - flickers
                    const opacity = router.blockTimer < router.maxBlockTime / 2 ? 0.3 : 1;
                    ctx.globalAlpha = opacity;
                    ctx.fillStyle = '#ff8800';
                } else {
                    ctx.fillStyle = '#ff0033';
                }
                
                ctx.fillRect(-cellSize/3, -cellSize/3, cellSize*2/3, cellSize*2/3);
                
                // Router ports
                ctx.fillStyle = '#ffee00';
                for (let i = 0; i < 4; i++) {
                    ctx.save();
                    ctx.rotate(i * Math.PI / 2);
                    ctx.fillRect(cellSize/3 - 5, -3, 10, 6);
                    ctx.restore();
                }
                
                // Antenna
                ctx.strokeStyle = '#00ffff';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(0, -cellSize/3);
                ctx.lineTo(0, -cellSize/2);
                ctx.arc(0, -cellSize/2, 5, 0, Math.PI * 2);
                ctx.stroke();
                
                ctx.restore();
            });
            
            // Draw firewalls
            level.firewalls.forEach(firewall => {
                const x = gridOffsetX + firewall.x * cellSize + cellSize / 2;
                const y = gridOffsetY + firewall.y * cellSize + cellSize / 2;
                
                ctx.save();
                ctx.translate(x, y);
                
                // Firewall shield
                ctx.strokeStyle = activePowerUps.freeze > 0 ? '#88ccff' : '#ff8800';
                ctx.lineWidth = 3;
                ctx.shadowBlur = 10;
                ctx.shadowColor = activePowerUps.freeze > 0 ? '#88ccff' : '#ff8800';
                
                // Add ice effect if frozen
                if (activePowerUps.freeze > 0) {
                    ctx.globalAlpha = 0.5;
                    ctx.fillStyle = '#88ccff';
                    ctx.fillRect(-cellSize/3, -cellSize/3, cellSize*2/3, cellSize*2/3);
                    ctx.globalAlpha = 1;
                }
                
                ctx.beginPath();
                for (let i = 0; i < 6; i++) {
                    const angle = (i / 6) * Math.PI * 2;
                    const px = Math.cos(angle) * cellSize / 3;
                    const py = Math.sin(angle) * cellSize / 3;
                    if (i === 0) ctx.moveTo(px, py);
                    else ctx.lineTo(px, py);
                }
                ctx.closePath();
                ctx.stroke();
                
                // Firewall icon
                ctx.fillStyle = '#ff8800';
                ctx.font = `${cellSize/3}px monospace`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('🔥', 0, 0);
                
                ctx.restore();
            });
            
            // Draw NAT tunnels
            level.natTunnels.forEach(nat => {
                // Entry portal
                const x1 = gridOffsetX + nat.x * cellSize + cellSize / 2;
                const y1 = gridOffsetY + nat.y * cellSize + cellSize / 2;
                
                ctx.save();
                ctx.translate(x1, y1);
                
                // Portal effect
                ctx.strokeStyle = '#00ffff';
                ctx.lineWidth = 2;
                ctx.shadowBlur = 15;
                ctx.shadowColor = '#00ffff';
                
                for (let i = 0; i < 3; i++) {
                    ctx.globalAlpha = 1 - i * 0.3;
                    ctx.beginPath();
                    ctx.arc(0, 0, cellSize/3 - i * 5, 0, Math.PI * 2);
                    ctx.stroke();
                }
                
                // Rotating inner portal
                ctx.rotate(pulseAnimation * 0.02);
                ctx.beginPath();
                ctx.arc(0, 0, cellSize/4, 0, Math.PI);
                ctx.stroke();
                
                ctx.restore();
                
                // Exit portal
                const x2 = gridOffsetX + nat.exitX * cellSize + cellSize / 2;
                const y2 = gridOffsetY + nat.exitY * cellSize + cellSize / 2;
                
                ctx.save();
                ctx.translate(x2, y2);
                
                ctx.strokeStyle = '#ffee00';
                ctx.shadowColor = '#ffee00';
                
                ctx.beginPath();
                ctx.arc(0, 0, cellSize/3, 0, Math.PI * 2);
                ctx.stroke();
                
                ctx.restore();
                
                // Connection line
                ctx.strokeStyle = 'rgba(0, 255, 255, 0.2)';
                ctx.lineWidth = 1;
                ctx.setLineDash([5, 10]);
                ctx.beginPath();
                ctx.moveTo(x1, y1);
                ctx.lineTo(x2, y2);
                ctx.stroke();
                ctx.setLineDash([]);
            });
        }
        
        function drawPowerUps() {
            level.powerUps.forEach(powerUp => {
                if (powerUp.collected) return;
                
                const x = gridOffsetX + powerUp.x * cellSize + cellSize / 2;
                const y = gridOffsetY + powerUp.y * cellSize + cellSize / 2;
                const powerUpData = POWERUP_TYPES[powerUp.type];
                
                ctx.save();
                ctx.translate(x, y);
                
                // Pulsing effect
                const scale = 1 + Math.sin(powerUp.pulse) * 0.2;
                ctx.scale(scale, scale);
                
                // Outer glow
                const gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, cellSize / 2);
                gradient.addColorStop(0, powerUpData.color + '88');
                gradient.addColorStop(0.7, powerUpData.color + '44');
                gradient.addColorStop(1, powerUpData.color + '00');
                
                ctx.fillStyle = gradient;
                ctx.fillRect(-cellSize / 2, -cellSize / 2, cellSize, cellSize);
                
                // Icon background
                ctx.fillStyle = 'rgba(14, 14, 32, 0.8)';
                ctx.beginPath();
                ctx.arc(0, 0, cellSize / 3, 0, Math.PI * 2);
                ctx.fill();
                
                // Border
                ctx.strokeStyle = powerUpData.color;
                ctx.lineWidth = 2;
                ctx.stroke();
                
                // Icon
                ctx.font = `${cellSize / 2}px Arial`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(powerUpData.icon, 0, 0);
                
                ctx.restore();
            });
        }
        
        function drawDestination() {
            const x = gridOffsetX + level.destination.x * cellSize + cellSize / 2;
            const y = gridOffsetY + level.destination.y * cellSize + cellSize / 2;
            
            ctx.save();
            ctx.translate(x, y);
            
            // Pulsing effect
            const pulse = Math.sin(pulseAnimation * 0.05) * 0.2 + 1;
            ctx.scale(pulse, pulse);
            
            // Server icon
            ctx.fillStyle = '#00ff00';
            ctx.fillRect(-cellSize/3, -cellSize/3, cellSize*2/3, cellSize*2/3);
            
            // Server lights
            ctx.fillStyle = '#ffee00';
            for (let i = 0; i < 3; i++) {
                const lightY = -cellSize/4 + i * cellSize/6;
                ctx.fillRect(-cellSize/4, lightY, 5, 3);
                ctx.fillRect(-cellSize/4 + 10, lightY, 5, 3);
            }
            
            // Label
            ctx.fillStyle = '#00ff00';
            ctx.font = '12px monospace';
            ctx.textAlign = 'center';
            ctx.fillText('HOST', 0, cellSize/2 + 15);
            
            ctx.restore();
        }
        
        function drawPacket() {
            if (packet.status === 'lost' || packet.status === 'blocked') return;
            
            // Draw trail
            packet.trail.forEach((t, i) => {
                const x = gridOffsetX + t.x * cellSize + cellSize / 2;
                const y = gridOffsetY + t.y * cellSize + cellSize / 2;
                
                ctx.fillStyle = `rgba(0, 255, 255, ${t.life / 40})`;
                ctx.fillRect(x - 2, y - 2, 4, 4);
            });
            
            // Draw packet
            const x = gridOffsetX + packet.x * cellSize + cellSize / 2;
            const y = gridOffsetY + packet.y * cellSize + cellSize / 2;
            
            ctx.save();
            ctx.translate(x, y);
            
            // Shield effect
            if (activePowerUps.shield) {
                ctx.strokeStyle = '#00ff00';
                ctx.lineWidth = 3;
                ctx.globalAlpha = 0.5 + Math.sin(pulseAnimation * 0.1) * 0.3;
                ctx.beginPath();
                ctx.arc(0, 0, cellSize / 2, 0, Math.PI * 2);
                ctx.stroke();
                ctx.globalAlpha = 1;
            }
            
            // Glow effect
            const glowSize = cellSize / 2 + packet.glow * 10;
            const gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, glowSize);
            gradient.addColorStop(0, 'rgba(0, 255, 255, 0.8)');
            gradient.addColorStop(0.5, 'rgba(0, 255, 255, 0.3)');
            gradient.addColorStop(1, 'rgba(0, 255, 255, 0)');
            
            ctx.fillStyle = gradient;
            ctx.fillRect(-glowSize, -glowSize, glowSize * 2, glowSize * 2);
            
            // Packet body
            ctx.fillStyle = packet.corruption > 50 ? '#ff8800' : '#00ffff';
            ctx.fillRect(-cellSize/4, -cellSize/6, cellSize/2, cellSize/3);
            
            // Packet header
            ctx.fillStyle = '#0e0e20';
            ctx.font = '10px monospace';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('TCP', 0, 0);
            
            // Corruption effect
            if (packet.corruption > 0) {
                ctx.globalAlpha = packet.corruption / 100;
                ctx.fillStyle = '#ff0033';
                ctx.fillRect(-cellSize/4, -cellSize/6, cellSize/2, cellSize/3);
            }
            
            ctx.restore();
        }
        
        function drawParticles() {
            particles.forEach(p => {
                ctx.save();
                ctx.fillStyle = p.color;
                ctx.globalAlpha = p.life / 50;
                
                if (p.type === 'sparkle') {
                    // Sparkle effect for power-ups
                    const sparkle = Math.sin(p.life * 0.3) * 0.5 + 0.5;
                    ctx.globalAlpha *= sparkle;
                    ctx.shadowBlur = 10;
                    ctx.shadowColor = p.color;
                } else if (p.type === 'compress') {
                    // Compression effect
                    ctx.scale(1, 0.5 + p.life / 40);
                }
                
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fill();
                
                ctx.restore();
            });
        
        // ===== MAIN ANIMATION LOOP =====
        function animate() {
            if (gameState === 'playing') {
                updatePacket();
                updateObstacles();
                updatePowerUps();
                updateParticles();
                updateNetworkBackground();
                pulseAnimation++;
            }
            
            render();
            animationId = requestAnimationFrame(animate);
        }
        
        // Initialize game
        init();
    </script>
</body>
</html>
