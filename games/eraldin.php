<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Eraldin - G Tech Arcade</title>
    <style>
        /* Reset e stili base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }

        body {
            background: #0c0c1e;
            color: #44f1ff;
            font-family: 'Courier New', monospace;
            overflow: hidden;
            position: fixed;
            width: 100%;
            height: 100%;
            touch-action: none;
        }

        /* Container principale */
        #gameContainer {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at center, #1a1a3e 0%, #0c0c1e 100%);
        }

        /* Canvas di gioco */
        #gameCanvas {
            max-width: 100%;
            max-height: 100%;
            display: block;
            image-rendering: crisp-edges;
            image-rendering: -moz-crisp-edges;
            image-rendering: pixelated;
            box-shadow: 0 0 30px rgba(68, 241, 255, 0.5);
        }

        /* HUD (Heads Up Display) */
        #hud {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 10px;
            background: rgba(12, 12, 30, 0.8);
            border: 2px solid #44f1ff;
            border-radius: 5px;
            font-size: 14px;
            text-shadow: 0 0 5px #44f1ff;
            z-index: 10;
        }

        .hud-item {
            margin: 5px 0;
            display: flex;
            align-items: center;
        }

        .heart {
            color: #ff2080;
            margin-right: 5px;
        }

        /* Barra esperienza */
        #expBar {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 300px;
            height: 20px;
            background: rgba(12, 12, 30, 0.8);
            border: 2px solid #44f1ff;
            border-radius: 10px;
            overflow: hidden;
            z-index: 10;
        }

        #expFill {
            height: 100%;
            background: linear-gradient(90deg, #44f1ff, #ff2080);
            width: 0%;
            transition: width 0.3s;
        }

        #levelText {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 12px;
            font-weight: bold;
        }

        /* Minimap */
        #minimap {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 120px;
            height: 120px;
            background: rgba(12, 12, 30, 0.8);
            border: 2px solid #44f1ff;
            border-radius: 5px;
            z-index: 10;
        }

        /* Inventario */
        #inventory {
            position: absolute;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 5px;
            background: rgba(12, 12, 30, 0.8);
            padding: 5px;
            border: 2px solid #44f1ff;
            border-radius: 5px;
            z-index: 10;
        }

        .inventory-slot {
            width: 40px;
            height: 40px;
            border: 1px solid #44f1ff;
            border-radius: 3px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            background: rgba(68, 241, 255, 0.1);
        }

        .inventory-slot.active {
            background: rgba(255, 32, 128, 0.3);
            border-color: #ff2080;
        }

        /* Notifiche */
        #notification {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background: rgba(68, 241, 255, 0.1);
            border: 2px solid #44f1ff;
            border-radius: 10px;
            font-size: 18px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
            z-index: 20;
        }

        #notification.show {
            opacity: 1;
        }

        /* Level Up */
        #levelUpScreen {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(12, 12, 30, 0.95);
            padding: 30px;
            border: 3px solid #ff2080;
            border-radius: 15px;
            text-align: center;
            display: none;
            z-index: 50;
        }

        #levelUpScreen.show {
            display: block;
            animation: levelUpPulse 0.5s ease-out;
        }

        @keyframes levelUpPulse {
            0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0; }
            50% { transform: translate(-50%, -50%) scale(1.1); }
            100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        }

        /* Dialoghi */
        #dialogue {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 600px;
            padding: 15px;
            background: rgba(12, 12, 30, 0.95);
            border: 2px solid #ff2080;
            border-radius: 10px;
            display: none;
            z-index: 15;
        }

        #dialogue.show {
            display: block;
        }

        .dialogue-speaker {
            color: #ff2080;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Missioni */
        #questLog {
            position: absolute;
            top: 150px;
            left: 10px;
            width: 200px;
            padding: 10px;
            background: rgba(12, 12, 30, 0.8);
            border: 2px solid #ffff00;
            border-radius: 5px;
            font-size: 12px;
            z-index: 10;
        }

        .quest-item {
            margin: 5px 0;
            padding-left: 15px;
            position: relative;
        }

        .quest-item.complete {
            color: #00ff00;
            text-decoration: line-through;
        }

        .quest-item:before {
            content: '▶';
            position: absolute;
            left: 0;
            color: #ffff00;
        }

        /* Controlli touch */
        #touchControls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
            display: none;
            pointer-events: none;
            z-index: 5;
        }

        /* Joystick */
        #joystick {
            position: absolute;
            left: 50px;
            bottom: 50px;
            width: 120px;
            height: 120px;
            pointer-events: all;
        }

        .joystick-base {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 3px solid rgba(68, 241, 255, 0.3);
            border-radius: 50%;
            background: rgba(12, 12, 30, 0.5);
        }

        .joystick-stick {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 50px;
            height: 50px;
            margin: -25px;
            background: radial-gradient(circle, #44f1ff 0%, #1a5a6e 100%);
            border-radius: 50%;
            box-shadow: 0 0 10px #44f1ff;
            transition: none;
        }

        /* Pulsanti azione */
        .action-buttons {
            position: absolute;
            right: 50px;
            bottom: 50px;
            pointer-events: all;
        }

        .touch-button {
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, #ff2080 0%, #801040 100%);
            border: 3px solid rgba(255, 32, 128, 0.5);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 0 15px #ff2080;
            margin: 5px;
            float: left;
        }

        .touch-button:active {
            transform: scale(0.9);
        }

        /* Combo indicator */
        #comboIndicator {
            position: absolute;
            top: 80px;
            right: 10px;
            font-size: 24px;
            font-weight: bold;
            color: #ffff00;
            text-shadow: 0 0 10px #ffff00;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 10;
        }

        #comboIndicator.show {
            opacity: 1;
            animation: comboPulse 0.5s;
        }

        @keyframes comboPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* Boss health bar */
        #bossHealthBar {
            position: absolute;
            top: 50px;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            height: 30px;
            background: rgba(12, 12, 30, 0.9);
            border: 3px solid #ff0000;
            border-radius: 15px;
            display: none;
            z-index: 10;
        }

        #bossHealthBar.show {
            display: block;
        }

        #bossHealthFill {
            height: 100%;
            background: linear-gradient(90deg, #ff0000, #ff6600);
            width: 100%;
            border-radius: 12px;
            transition: width 0.3s;
        }

        #bossName {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
            text-shadow: 2px 2px 4px #000;
        }

        /* Schermo game over */
        #gameOver {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(12, 12, 30, 0.95);
            padding: 30px;
            border: 3px solid #ff2080;
            border-radius: 15px;
            text-align: center;
            display: none;
            z-index: 100;
        }

        #gameOver.show {
            display: block;
        }

        #gameOver h2 {
            color: #ff2080;
            margin-bottom: 20px;
            text-shadow: 0 0 10px #ff2080;
        }

        .button {
            padding: 10px 20px;
            background: #44f1ff;
            color: #0c0c1e;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin: 5px;
            transition: all 0.3s;
        }

        .button:hover {
            background: #ff2080;
            transform: scale(1.1);
        }

        /* Victory screen */
        #victoryScreen {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(12, 12, 30, 0.95);
            padding: 40px;
            border: 3px solid #00ff00;
            border-radius: 15px;
            text-align: center;
            display: none;
            z-index: 100;
        }

        #victoryScreen.show {
            display: block;
            animation: victoryGlow 2s ease-in-out infinite;
        }

        @keyframes victoryGlow {
            0%, 100% { box-shadow: 0 0 20px #00ff00; }
            50% { box-shadow: 0 0 40px #00ff00, 0 0 60px #00ff00; }
        }

        /* Media queries per responsive */
        @media (hover: none) and (pointer: coarse) {
            #touchControls {
                display: block;
            }
        }

        /* Samsung Fold optimization */
        @media screen and (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
            #hud {
                font-size: 16px;
                padding: 15px;
            }
            
            #joystick {
                width: 140px;
                height: 140px;
            }
            
            .touch-button {
                width: 80px;
                height: 80px;
                font-size: 24px;
            }
        }

        /* Landscape mobile */
        @media screen and (max-height: 500px) and (orientation: landscape) {
            #joystick {
                bottom: 20px;
                left: 20px;
                width: 100px;
                height: 100px;
            }
            
            .action-buttons {
                bottom: 20px;
                right: 20px;
            }
            
            .touch-button {
                width: 50px;
                height: 50px;
                font-size: 18px;
            }
            
            #hud {
                font-size: 12px;
                padding: 5px;
            }
            
            #questLog {
                display: none;
            }
        }

        /* Animazioni UI */
        @keyframes damage-flash {
            0%, 100% { filter: none; }
            50% { filter: brightness(2) hue-rotate(180deg); }
        }
        
        .damage-flash {
            animation: damage-flash 0.2s;
        }
    </style>
</head>
<body>
    <div id="gameContainer">
        <canvas id="gameCanvas"></canvas>
        
        <!-- HUD -->
        <div id="hud">
            <div class="hud-item">
                <span class="heart">❤️</span>
                <span id="health">5</span>/<span id="maxHealth">5</span>
            </div>
            <div class="hud-item">
                <span>⚡ Energia: </span>
                <span id="energy">100</span>
            </div>
            <div class="hud-item">
                <span>📦 Patch: </span>
                <span id="patches">0</span>
            </div>
            <div class="hud-item">
                <span>🔑 Chiavi: </span>
                <span id="keys">0</span>
            </div>
            <div class="hud-item">
                <span>💎 Cristalli: </span>
                <span id="crystals">0</span>
            </div>
        </div>
        
        <!-- Barra esperienza -->
        <div id="expBar">
            <div id="expFill"></div>
            <div id="levelText">Livello 1</div>
        </div>
        
        <!-- Minimap -->
        <canvas id="minimap"></canvas>
        
        <!-- Quest Log -->
        <div id="questLog">
            <h3 style="color: #ffff00; margin-bottom: 10px;">📜 Missioni</h3>
            <div id="questList"></div>
        </div>
        
        <!-- Inventario -->
        <div id="inventory">
            <div class="inventory-slot" data-slot="0"></div>
            <div class="inventory-slot" data-slot="1"></div>
            <div class="inventory-slot" data-slot="2"></div>
            <div class="inventory-slot" data-slot="3"></div>
            <div class="inventory-slot" data-slot="4"></div>
        </div>
        
        <!-- Combo Indicator -->
        <div id="comboIndicator">COMBO x<span id="comboCount">0</span>!</div>
        
        <!-- Boss Health Bar -->
        <div id="bossHealthBar">
            <div id="bossHealthFill"></div>
            <div id="bossName">MEGA VIRUS</div>
        </div>
        
        <!-- Notifiche -->
        <div id="notification"></div>
        
        <!-- Level Up Screen -->
        <div id="levelUpScreen">
            <h2 style="color: #ff2080; margin-bottom: 20px;">LIVELLO SUPERIORE!</h2>
            <p>Scegli un potenziamento:</p>
            <button class="button" onclick="selectUpgrade('health')">❤️ +2 Vita Max</button>
            <button class="button" onclick="selectUpgrade('damage')">⚔️ +1 Danno</button>
            <button class="button" onclick="selectUpgrade('speed')">💨 +10% Velocità</button>
        </div>
        
        <!-- Dialoghi -->
        <div id="dialogue">
            <div class="dialogue-speaker" id="speaker"></div>
            <div id="dialogueText"></div>
        </div>
        
        <!-- Controlli touch -->
        <div id="touchControls">
            <div id="joystick">
                <div class="joystick-base"></div>
                <div class="joystick-stick" id="joystickStick"></div>
            </div>
            <div class="action-buttons">
                <div class="touch-button" id="attackButton">⚔️</div>
                <div class="touch-button" id="dashButton">💨</div>
                <div class="touch-button" id="interactButton">💬</div>
            </div>
        </div>
        
        <!-- Game Over -->
        <div id="gameOver">
            <h2>SISTEMA CORROTTO!</h2>
            <p>Livello raggiunto: <span id="finalLevel">1</span></p>
            <p>Patch raccolte: <span id="finalPatches">0</span></p>
            <p>Nemici sconfitti: <span id="enemiesDefeated">0</span></p>
            <button class="button" onclick="location.reload()">🔄 Riprova</button>
            <button class="button" onclick="location.href='../index.php'">🏠 Arcade</button>
        </div>
        
        <!-- Victory Screen -->
        <div id="victoryScreen">
            <h1 style="color: #00ff00; margin-bottom: 20px;">VITTORIA!</h1>
            <p>Hai salvato il sistema dalla corruzione!</p>
            <p>Tempo completamento: <span id="completionTime">00:00</span></p>
            <p>Punteggio finale: <span id="finalScore">0</span></p>
            <button class="button" onclick="location.reload()">🔄 Nuova Partita</button>
            <button class="button" onclick="location.href='../index.php'">🏠 Arcade</button>
        </div>
    </div>

    <script>
        // ===== INIZIALIZZAZIONE CANVAS E VARIABILI GLOBALI =====
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        const minimapCanvas = document.getElementById('minimap');
        const minimapCtx = minimapCanvas.getContext('2d');
        
        // Dimensioni base del gioco
        const GAME_WIDTH = 800;
        const GAME_HEIGHT = 600;
        const TILE_WIDTH = 64;
        const TILE_HEIGHT = 32;
        
        // Stato del gioco
        let gameRunning = true;
        let gameFrame = 0;
        let gameStartTime = Date.now();
        let score = 0;
        let defeatedEnemies = 0;
        
        // Camera e viewport
        let camera = {
            x: 0,
            y: 0,
            zoom: 1,
            shake: 0
        };
        
        // Particelle
        let particles = [];
        
        // Input
        let keys = {};
        let touchInput = { x: 0, y: 0, active: false };
        let actionPressed = false;
        let attackPressed = false;
        let dashPressed = false;
        
        // Audio Context per effetti sonori
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        // ===== SISTEMA AUDIO =====
        function playSound(type) {
            if (!audioContext) return;
            
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            switch(type) {
                case 'hit':
                    oscillator.frequency.setValueAtTime(200, audioContext.currentTime);
                    oscillator.frequency.exponentialRampToValueAtTime(100, audioContext.currentTime + 0.1);
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.1);
                    break;
                    
                case 'collect':
                    oscillator.frequency.setValueAtTime(400, audioContext.currentTime);
                    oscillator.frequency.exponentialRampToValueAtTime(800, audioContext.currentTime + 0.2);
                    gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
                    oscillator.type = 'sine';
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.2);
                    break;
                    
                case 'levelup':
                    oscillator.frequency.setValueAtTime(300, audioContext.currentTime);
                    oscillator.frequency.setValueAtTime(400, audioContext.currentTime + 0.1);
                    oscillator.frequency.setValueAtTime(500, audioContext.currentTime + 0.2);
                    oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.3);
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.4);
                    oscillator.type = 'sine';
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.4);
                    break;
                    
                case 'dash':
                    const noise = audioContext.createBufferSource();
                    const buffer = audioContext.createBuffer(1, audioContext.sampleRate * 0.1, audioContext.sampleRate);
                    const data = buffer.getChannelData(0);
                    for (let i = 0; i < data.length; i++) {
                        data[i] = Math.random() * 2 - 1;
                    }
                    noise.buffer = buffer;
                    
                    const filter = audioContext.createBiquadFilter();
                    filter.type = 'highpass';
                    filter.frequency.setValueAtTime(1000, audioContext.currentTime);
                    
                    noise.connect(filter);
                    filter.connect(gainNode);
                    gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
                    
                    noise.start(audioContext.currentTime);
                    break;
            }
        }
        
        // ===== SETUP RESPONSIVE CANVAS =====
        function resizeCanvas() {
            const container = document.getElementById('gameContainer');
            const containerWidth = container.clientWidth;
            const containerHeight = container.clientHeight;
            
            // Calcola scala mantenendo aspect ratio
            const scaleX = containerWidth / GAME_WIDTH;
            const scaleY = containerHeight / GAME_HEIGHT;
            const scale = Math.min(scaleX, scaleY);
            
            // Imposta dimensioni canvas
            canvas.width = GAME_WIDTH;
            canvas.height = GAME_HEIGHT;
            canvas.style.width = (GAME_WIDTH * scale) + 'px';
            canvas.style.height = (GAME_HEIGHT * scale) + 'px';
            
            // Centra il canvas
            canvas.style.position = 'absolute';
            canvas.style.left = ((containerWidth - GAME_WIDTH * scale) / 2) + 'px';
            canvas.style.top = ((containerHeight - GAME_HEIGHT * scale) / 2) + 'px';
            
            // Setup minimap
            minimapCanvas.width = 120;
            minimapCanvas.height = 120;
        }
        
        window.addEventListener('resize', resizeCanvas);
        window.addEventListener('orientationchange', () => {
            setTimeout(resizeCanvas, 100);
        });
        
        // ===== CONVERSIONI COORDINATE ISOMETRICHE =====
        function worldToScreen(worldX, worldY) {
            const screenX = (worldX - worldY) * TILE_WIDTH / 2 + GAME_WIDTH / 2 - camera.x;
            const screenY = (worldX + worldY) * TILE_HEIGHT / 2 + 100 - camera.y;
            return { x: screenX, y: screenY };
        }
        
        function screenToWorld(screenX, screenY) {
            screenX = screenX - GAME_WIDTH / 2 + camera.x;
            screenY = screenY - 100 + camera.y;
            const worldX = (screenX / (TILE_WIDTH / 2) + screenY / (TILE_HEIGHT / 2)) / 2;
            const worldY = (screenY / (TILE_HEIGHT / 2) - screenX / (TILE_WIDTH / 2)) / 2;
            return { x: Math.floor(worldX), y: Math.floor(worldY) };
        }
        
        // ===== PLAYER (ERALDIN) =====
        const player = {
            x: 5,
            y: 5,
            health: 5,
            maxHealth: 5,
            energy: 100,
            maxEnergy: 100,
            speed: 0.1,
            damage: 1,
            level: 1,
            exp: 0,
            expToNext: 100,
            patches: 0,
            keys: 0,
            crystals: 0,
            direction: 'down',
            animFrame: 0,
            moving: false,
            attacking: false,
            attackCooldown: 0,
            invulnerable: 0,
            dashCooldown: 0,
            combo: 0,
            comboTimer: 0,
            inventory: [null, null, null, null, null],
            activeSlot: 0
        };
        
        // ===== SISTEMA DI COMBATTIMENTO =====
        let projectiles = [];
        
        function createProjectile(x, y, targetX, targetY, damage, color, owner) {
            const angle = Math.atan2(targetY - y, targetX - x);
            projectiles.push({
                x: x,
                y: y,
                vx: Math.cos(angle) * 0.3,
                vy: Math.sin(angle) * 0.3,
                damage: damage,
                color: color,
                owner: owner,
                lifetime: 60
            });
        }
        
        // ===== MAPPA DEL MONDO =====
        const WORLD_SIZE = 30;
        const world = {
            tiles: [],
            objects: [],
            npcs: [],
            enemies: [],
            doors: [],
            decorations: []
        };
        
        // ===== MISSIONI =====
        const quests = [
            { id: 'collect_patches', name: 'Raccogli 5 Patch', target: 5, current: 0, complete: false },
            { id: 'defeat_bugs', name: 'Elimina 10 Bug', target: 10, current: 0, complete: false },
            { id: 'find_boss_key', name: 'Trova la Chiave del Boss', target: 1, current: 0, complete: false },
            { id: 'defeat_boss', name: 'Sconfiggi il Mega Virus', target: 1, current: 0, complete: false }
        ];
        
        // Genera mappa migliorata
        function generateWorld() {
            // Inizializza tiles con pattern più complesso
            for (let x = 0; x < WORLD_SIZE; x++) {
                world.tiles[x] = [];
                for (let y = 0; y < WORLD_SIZE; y++) {
                    // Bordi = muri
                    if (x === 0 || y === 0 || x === WORLD_SIZE - 1 || y === WORLD_SIZE - 1) {
                        world.tiles[x][y] = 'wall';
                    } else {
                        // Pattern interno
                        if (Math.random() < 0.1 && Math.abs(x - 15) > 5 && Math.abs(y - 15) > 5) {
                            world.tiles[x][y] = 'wall';
                        } else if (Math.random() < 0.05) {
                            world.tiles[x][y] = 'corrupted';
                        } else {
                            world.tiles[x][y] = 'floor';
                        }
                    }
                }
            }
            
            // Crea stanze
            createRoom(5, 5, 8, 8);
            createRoom(20, 5, 7, 7);
            createRoom(10, 18, 10, 8);
            createRoom(12, 12, 6, 6); // Stanza boss
            
            // Aggiungi porte
            world.doors = [
                { x: 15, y: 12, locked: true, keyRequired: 'boss_key' }
            ];
            
            // Aggiungi decorazioni
            for (let i = 0; i < 50; i++) {
                const x = Math.floor(Math.random() * (WORLD_SIZE - 2)) + 1;
                const y = Math.floor(Math.random() * (WORLD_SIZE - 2)) + 1;
                if (world.tiles[x][y] === 'floor') {
                    world.decorations.push({
                        x: x + Math.random() * 0.8 - 0.4,
                        y: y + Math.random() * 0.8 - 0.4,
                        type: Math.random() < 0.5 ? 'circuit' : 'data_stream'
                    });
                }
            }
            
            // Aggiungi oggetti collezionabili
            world.objects = [
                { x: 3, y: 3, type: 'patch', collected: false },
                { x: 23, y: 7, type: 'patch', collected: false },
                { x: 8, y: 22, type: 'health_potion', collected: false },
                { x: 17, y: 3, type: 'patch', collected: false },
                { x: 25, y: 25, type: 'patch', collected: false },
                { x: 7, y: 15, type: 'energy_crystal', collected: false },
                { x: 22, y: 22, type: 'patch', collected: false },
                { x: 10, y: 10, type: 'boss_key', collected: false },
                { x: 5, y: 25, type: 'damage_upgrade', collected: false }
            ];
            
            // Aggiungi NPC
            world.npcs = [
                {
                    x: 7,
                    y: 7,
                    name: 'Sistema AI-42',
                    dialogue: [
                        'Benvenuto, Eraldin. Il sistema è sotto attacco!',
                        'Un Mega Virus ha corrotto il core centrale.',
                        'Raccogli patch e potenziamenti per fermarlo!',
                        'La chiave del boss è nascosta da qualche parte...'
                    ],
                    dialogueIndex: 0
                },
                {
                    x: 23,
                    y: 6,
                    name: 'Debug Protocol',
                    dialogue: [
                        'I bug si moltiplicano velocemente.',
                        'Usa il dash (tasto destro) per schivarli!',
                        'Combina gli attacchi per fare combo.'
                    ],
                    dialogueIndex: 0
                }
            ];
            
            // Aggiungi nemici variati
            spawnEnemies();
            
            // Boss nella stanza centrale
            world.enemies.push({
                x: 15,
                y: 15,
                type: 'boss',
                subtype: 'mega_virus',
                health: 50,
                maxHealth: 50,
                speed: 0.02,
                damage: 3,
                direction: { x: 1, y: 0 },
                attackCooldown: 0,
                phase: 1,
                isBoss: true
            });
        }
        
        function createRoom(x, y, width, height) {
            for (let rx = x; rx < x + width; rx++) {
                for (let ry = y; ry < y + height; ry++) {
                    if (rx < WORLD_SIZE && ry < WORLD_SIZE) {
                        if (rx === x || rx === x + width - 1 || ry === y || ry === y + height - 1) {
                            world.tiles[rx][ry] = 'wall';
                        } else {
                            world.tiles[rx][ry] = 'floor';
                        }
                    }
                }
            }
        }
        
        function spawnEnemies() {
            const enemyTypes = [
                { type: 'bug', health: 2, speed: 0.03, damage: 1, exp: 20 },
                { type: 'glitch', health: 3, speed: 0.025, damage: 1, exp: 30 },
                { type: 'virus', health: 4, speed: 0.04, damage: 2, exp: 40 },
                { type: 'trojan', health: 5, speed: 0.02, damage: 2, exp: 50 },
                { type: 'worm', health: 3, speed: 0.05, damage: 1, exp: 35 }
            ];
            
            for (let i = 0; i < 20; i++) {
                const x = Math.floor(Math.random() * (WORLD_SIZE - 2)) + 1;
                const y = Math.floor(Math.random() * (WORLD_SIZE - 2)) + 1;
                
                if (world.tiles[x][y] === 'floor' && Math.abs(x - player.x) > 5 && Math.abs(y - player.y) > 5) {
                    const enemyType = enemyTypes[Math.floor(Math.random() * enemyTypes.length)];
                    world.enemies.push({
                        x: x,
                        y: y,
                        ...enemyType,
                        maxHealth: enemyType.health,
                        direction: { 
                            x: Math.random() * 2 - 1, 
                            y: Math.random() * 2 - 1 
                        },
                        attackCooldown: 0,
                        stunned: 0
                    });
                }
            }
        }
        
        // ===== INPUT HANDLING =====
        
        // Keyboard
        window.addEventListener('keydown', (e) => {
            keys[e.key.toLowerCase()] = true;
            if (e.key === ' ') {
                e.preventDefault();
                actionPressed = true;
            }
            if (e.key === 'z' || e.key === 'x') {
                attackPressed = true;
            }
            if (e.key === 'shift') {
                dashPressed = true;
            }
        });
        
        window.addEventListener('keyup', (e) => {
            keys[e.key.toLowerCase()] = false;
            if (e.key === ' ') {
                actionPressed = false;
            }
            if (e.key === 'z' || e.key === 'x') {
                attackPressed = false;
            }
            if (e.key === 'shift') {
                dashPressed = false;
            }
        });
        
        // Touch controls
        const isTouchDevice = 'ontouchstart' in window;
        
        if (isTouchDevice) {
            const joystick = document.getElementById('joystick');
            const stick = document.getElementById('joystickStick');
            const attackBtn = document.getElementById('attackButton');
            const dashBtn = document.getElementById('dashButton');
            const interactBtn = document.getElementById('interactButton');
            
            let joystickActive = false;
            let joystickStartX = 0;
            let joystickStartY = 0;
            
            // Joystick events
            joystick.addEventListener('touchstart', (e) => {
                e.preventDefault();
                joystickActive = true;
                const touch = e.touches[0];
                const rect = joystick.getBoundingClientRect();
                joystickStartX = rect.left + rect.width / 2;
                joystickStartY = rect.top + rect.height / 2;
            });
            
            window.addEventListener('touchmove', (e) => {
                if (!joystickActive) return;
                
                const touch = e.touches[0];
                let deltaX = touch.clientX - joystickStartX;
                let deltaY = touch.clientY - joystickStartY;
                
                // Limita il movimento del joystick
                const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
                const maxDistance = 40;
                
                if (distance > maxDistance) {
                    deltaX = (deltaX / distance) * maxDistance;
                    deltaY = (deltaY / distance) * maxDistance;
                }
                
                // Muovi lo stick visivamente
                stick.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
                
                // Imposta input
                touchInput.x = deltaX / maxDistance;
                touchInput.y = deltaY / maxDistance;
                touchInput.active = true;
            });
            
            window.addEventListener('touchend', () => {
                if (joystickActive) {
                    joystickActive = false;
                    stick.style.transform = 'translate(0, 0)';
                    touchInput.active = false;
                    touchInput.x = 0;
                    touchInput.y = 0;
                }
            });
            
            // Action buttons
            attackBtn.addEventListener('touchstart', (e) => {
                e.preventDefault();
                attackPressed = true;
            });
            
            attackBtn.addEventListener('touchend', (e) => {
                e.preventDefault();
                attackPressed = false;
            });
            
            dashBtn.addEventListener('touchstart', (e) => {
                e.preventDefault();
                dashPressed = true;
            });
            
            dashBtn.addEventListener('touchend', (e) => {
                e.preventDefault();
                dashPressed = false;
            });
            
            interactBtn.addEventListener('touchstart', (e) => {
                e.preventDefault();
                actionPressed = true;
            });
            
            interactBtn.addEventListener('touchend', (e) => {
                e.preventDefault();
                actionPressed = false;
            });
        }
        
        // ===== LOGICA DI GIOCO =====
        
        function updatePlayer() {
            if (!gameRunning) return;
            
            let dx = 0;
            let dy = 0;
            
            // Input da tastiera
            if (keys['w'] || keys['arrowup']) dy = -1;
            if (keys['s'] || keys['arrowdown']) dy = 1;
            if (keys['a'] || keys['arrowleft']) dx = -1;
            if (keys['d'] || keys['arrowright']) dx = 1;
            
            // Input da touch
            if (touchInput.active) {
                dx = touchInput.x;
                dy = touchInput.y;
            }
            
            // Normalizza movimento diagonale
            if (dx !== 0 && dy !== 0) {
                dx *= 0.707;
                dy *= 0.707;
            }
            
            // Dash
            if (dashPressed && player.dashCooldown <= 0 && player.energy >= 20) {
                player.energy -= 20;
                player.dashCooldown = 30;
                dx *= 5;
                dy *= 5;
                playSound('dash');
                
                // Effetto dash
                for (let i = 0; i < 10; i++) {
                    particles.push({
                        x: player.x,
                        y: player.y,
                        vx: (Math.random() - 0.5) * 0.2,
                        vy: (Math.random() - 0.5) * 0.2,
                        color: '#44f1ff',
                        size: 5,
                        lifetime: 20
                    });
                }
            }
            
            // Movimento
            if (dx !== 0 || dy !== 0) {
                const speedMultiplier = 1 + (player.level - 1) * 0.1;
                const newX = player.x + dx * player.speed * speedMultiplier;
                const newY = player.y + dy * player.speed * speedMultiplier;
                
                // Controlla collisioni
                if (canMoveTo(newX, newY)) {
                    player.x = newX;
                    player.y = newY;
                    player.moving = true;
                    
                    // Aggiorna direzione
                    if (Math.abs(dx) > Math.abs(dy)) {
                        player.direction = dx > 0 ? 'right' : 'left';
                    } else {
                        player.direction = dy > 0 ? 'down' : 'up';
                    }
                } else {
                    player.moving = false;
                }
            } else {
                player.moving = false;
            }
            
            // Animazione
            if (player.moving) {
                player.animFrame = (player.animFrame + 0.2) % 4;
            } else {
                player.animFrame = 0;
            }
            
            // Attacco
            if (attackPressed && player.attackCooldown <= 0) {
                performAttack();
                player.attackCooldown = 20;
                player.attacking = true;
            }
            
            // Cooldowns
            if (player.invulnerable > 0) player.invulnerable--;
            if (player.attackCooldown > 0) player.attackCooldown--;
            if (player.dashCooldown > 0) player.dashCooldown--;
            if (player.attacking && player.attackCooldown < 15) player.attacking = false;
            
            // Combo timer
            if (player.comboTimer > 0) {
                player.comboTimer--;
                if (player.comboTimer === 0) {
                    player.combo = 0;
                    document.getElementById('comboIndicator').classList.remove('show');
                }
            }
            
            // Rigenera energia
            if (player.energy < player.maxEnergy) {
                player.energy += 0.2;
                player.energy = Math.min(player.energy, player.maxEnergy);
            }
            
            // Camera segue il player
            const screenPos = worldToScreen(player.x, player.y);
            camera.x += (screenPos.x - GAME_WIDTH / 2 - camera.x) * 0.1;
            camera.y += (screenPos.y - GAME_HEIGHT / 2 - camera.y) * 0.1;
            
            // Camera shake
            if (camera.shake > 0) {
                camera.shake *= 0.9;
                camera.x += (Math.random() - 0.5) * camera.shake;
                camera.y += (Math.random() - 0.5) * camera.shake;
            }
        }
        
        function canMoveTo(x, y) {
            const tileX = Math.floor(x);
            const tileY = Math.floor(y);
            
            // Controlla limiti mappa
            if (tileX < 0 || tileY < 0 || tileX >= WORLD_SIZE || tileY >= WORLD_SIZE) {
                return false;
            }
            
            // Controlla tile
            if (world.tiles[tileX][tileY] === 'wall') {
                return false;
            }
            
            // Controlla porte
            for (const door of world.doors) {
                if (Math.abs(door.x - x) < 0.8 && Math.abs(door.y - y) < 0.8 && door.locked) {
                    return false;
                }
            }
            
            return true;
        }
        
        function performAttack() {
            playSound('hit');
            
            // Area di attacco basata sulla direzione
            const attackRange = 1.5;
            let hitSomething = false;
            
            // Controlla nemici colpiti
            world.enemies = world.enemies.filter(enemy => {
                const dist = Math.sqrt(Math.pow(enemy.x - player.x, 2) + Math.pow(enemy.y - player.y, 2));
                
                if (dist < attackRange) {
                    // Calcola danno con combo
                    const damage = player.damage + Math.floor(player.combo / 3);
                    enemy.health -= damage;
                    enemy.stunned = 10;
                    hitSomething = true;
                    
                    // Knockback
                    const angle = Math.atan2(enemy.y - player.y, enemy.x - player.x);
                    enemy.x += Math.cos(angle) * 0.5;
                    enemy.y += Math.sin(angle) * 0.5;
                    
                    // Effetti visivi
                    createDamageNumber(enemy.x, enemy.y, damage);
                    createHitEffect(enemy.x, enemy.y);
                    
                    if (enemy.health <= 0) {
                        // Enemy sconfitto
                        defeatedEnemies++;
                        player.exp += enemy.exp || 20;
                        score += 100 * (1 + player.combo * 0.1);
                        
                        // Drop items
                        if (Math.random() < 0.3) {
                            world.objects.push({
                                x: enemy.x,
                                y: enemy.y,
                                type: Math.random() < 0.7 ? 'energy_crystal' : 'health_potion',
                                collected: false
                            });
                        }
                        
                        // Update quest
                        updateQuest('defeat_bugs', 1);
                        
                        // Boss sconfitto?
                        if (enemy.isBoss) {
                            updateQuest('defeat_boss', 1);
                            setTimeout(() => showVictory(), 1000);
                        }
                        
                        createExplosion(enemy.x, enemy.y);
                        return false;
                    }
                }
                return true;
            });
            
            // Aggiorna combo
            if (hitSomething) {
                player.combo++;
                player.comboTimer = 120;
                
                if (player.combo > 1) {
                    document.getElementById('comboCount').textContent = player.combo;
                    document.getElementById('comboIndicator').classList.add('show');
                }
            }
            
            // Effetto slash
            const slashAngle = {
                'up': -Math.PI/2,
                'down': Math.PI/2,
                'left': Math.PI,
                'right': 0
            }[player.direction];
            
            for (let i = 0; i < 5; i++) {
                const angle = slashAngle + (Math.random() - 0.5) * 0.5;
                particles.push({
                    x: player.x + Math.cos(angle) * 0.5,
                    y: player.y + Math.sin(angle) * 0.5,
                    vx: Math.cos(angle) * 0.2,
                    vy: Math.sin(angle) * 0.2,
                    color: '#ffffff',
                    size: 8,
                    lifetime: 10
                });
            }
        }
        
        function updateEnemies() {
            world.enemies.forEach(enemy => {
                if (enemy.stunned > 0) {
                    enemy.stunned--;
                    return;
                }
                
                // Comportamento specifico per tipo
                if (enemy.isBoss) {
                    updateBoss(enemy);
                } else {
                    // Movimento base
                    enemy.x += enemy.direction.x * enemy.speed;
                    enemy.y += enemy.direction.y * enemy.speed;
                    
                    // Rimbalza sui muri
                    if (!canMoveTo(enemy.x, enemy.y)) {
                        enemy.direction.x *= -1;
                        enemy.direction.y *= -1;
                        enemy.x += enemy.direction.x * enemy.speed * 2;
                        enemy.y += enemy.direction.y * enemy.speed * 2;
                    }
                    
                    // AI: insegue il player se vicino
                    const dist = Math.sqrt(Math.pow(enemy.x - player.x, 2) + Math.pow(enemy.y - player.y, 2));
                    
                    if (enemy.type === 'trojan' && dist < 8) {
                        // Trojan spara proiettili
                        if (enemy.attackCooldown <= 0) {
                            createProjectile(enemy.x, enemy.y, player.x, player.y, 1, '#ff0000', 'enemy');
                            enemy.attackCooldown = 60;
                        }
                    } else if (dist < 5) {
                        // Insegue
                        const angle = Math.atan2(player.y - enemy.y, player.x - enemy.x);
                        enemy.direction.x = Math.cos(angle);
                        enemy.direction.y = Math.sin(angle);
                    }
                    
                    // Collisione con player
                    if (dist < 0.8 && player.invulnerable === 0) {
                        player.health -= enemy.damage || 1;
                        player.invulnerable = 60;
                        camera.shake = 10;
                        
                        showNotification(`-${enemy.damage || 1} HP!`, '#ff2080');
                        document.getElementById('gameContainer').classList.add('damage-flash');
                        setTimeout(() => {
                            document.getElementById('gameContainer').classList.remove('damage-flash');
                        }, 200);
                        
                        updateHUD();
                        
                        if (player.health <= 0) {
                            gameOver();
                        }
                    }
                }
                
                if (enemy.attackCooldown > 0) enemy.attackCooldown--;
            });
        }
        
        function updateBoss(boss) {
            // Boss AI
            const dist = Math.sqrt(Math.pow(boss.x - player.x, 2) + Math.pow(boss.y - player.y, 2));
            
            // Mostra barra vita boss
            if (dist < 10) {
                document.getElementById('bossHealthBar').classList.add('show');
                const healthPercent = boss.health / boss.maxHealth * 100;
                document.getElementById('bossHealthFill').style.width = healthPercent + '%';
            }
            
            // Fasi del boss
            if (boss.health < boss.maxHealth * 0.5 && boss.phase === 1) {
                boss.phase = 2;
                boss.speed *= 1.5;
                showNotification('IL MEGA VIRUS SI POTENZIA!', '#ff0000');
            }
            
            // Pattern di attacco
            if (boss.attackCooldown <= 0) {
                if (boss.phase === 1) {
                    // Fase 1: proiettili singoli
                    createProjectile(boss.x, boss.y, player.x, player.y, 2, '#ff00ff', 'enemy');
                    boss.attackCooldown = 40;
                } else {
                    // Fase 2: proiettili multipli
                    for (let i = 0; i < 8; i++) {
                        const angle = (Math.PI * 2 / 8) * i;
                        const targetX = boss.x + Math.cos(angle) * 10;
                        const targetY = boss.y + Math.sin(angle) * 10;
                        createProjectile(boss.x, boss.y, targetX, targetY, 1, '#ff00ff', 'enemy');
                    }
                    boss.attackCooldown = 60;
                }
            }
            
            // Movimento del boss
            if (dist > 3) {
                const angle = Math.atan2(player.y - boss.y, player.x - boss.x);
                boss.x += Math.cos(angle) * boss.speed;
                boss.y += Math.sin(angle) * boss.speed;
            } else {
                // Movimento circolare
                const circleAngle = gameFrame * 0.02;
                boss.x += Math.cos(circleAngle) * boss.speed;
                boss.y += Math.sin(circleAngle) * boss.speed;
            }
        }
        
        function updateProjectiles() {
            projectiles = projectiles.filter(proj => {
                proj.x += proj.vx;
                proj.y += proj.vy;
                proj.lifetime--;
                
                // Collisione con player
                if (proj.owner === 'enemy') {
                    const dist = Math.sqrt(Math.pow(proj.x - player.x, 2) + Math.pow(proj.y - player.y, 2));
                    if (dist < 0.5 && player.invulnerable === 0) {
                        player.health -= proj.damage;
                        player.invulnerable = 30;
                        createHitEffect(player.x, player.y);
                        updateHUD();
                        
                        if (player.health <= 0) {
                            gameOver();
                        }
                        return false;
                    }
                }
                
                // Collisione con muri
                const tileX = Math.floor(proj.x);
                const tileY = Math.floor(proj.y);
                if (world.tiles[tileX] && world.tiles[tileX][tileY] === 'wall') {
                    createHitEffect(proj.x, proj.y);
                    return false;
                }
                
                return proj.lifetime > 0;
            });
        }
        
        function updateParticles() {
            particles = particles.filter(particle => {
                particle.x += particle.vx;
                particle.y += particle.vy;
                particle.vx *= 0.95;
                particle.vy *= 0.95;
                particle.lifetime--;
                particle.size *= 0.95;
                
                return particle.lifetime > 0 && particle.size > 0.1;
            });
        }
        
        function checkInteractions() {
            if (!actionPressed) return;
            actionPressed = false;
            
            // Controlla oggetti
            world.objects = world.objects.filter(obj => {
                if (obj.collected) return true;
                
                const dist = Math.sqrt(Math.pow(obj.x - player.x, 2) + Math.pow(obj.y - player.y, 2));
                if (dist < 1) {
                    obj.collected = true;
                    playSound('collect');
                    
                    switch(obj.type) {
                        case 'patch':
                            player.patches++;
                            showNotification('Patch di sistema raccolta!', '#44f1ff');
                            updateQuest('collect_patches', 1);
                            score += 50;
                            break;
                            
                        case 'boss_key':
                            player.keys++;
                            showNotification('Chiave del Boss ottenuta!', '#ffff00');
                            updateQuest('find_boss_key', 1);
                            // Sblocca porta
                            world.doors.forEach(door => {
                                if (door.keyRequired === 'boss_key') {
                                    door.locked = false;
                                    showNotification('Porta del Boss sbloccata!', '#00ff00');
                                }
                            });
                            break;
                            
                        case 'health_potion':
                            player.health = Math.min(player.maxHealth, player.health + 2);
                            showNotification('+2 HP!', '#ff2080');
                            createHealEffect(player.x, player.y);
                            break;
                            
                        case 'energy_crystal':
                            player.crystals++;
                            player.energy = player.maxEnergy;
                            showNotification('Energia ripristinata!', '#44f1ff');
                            break;
                            
                        case 'damage_upgrade':
                            player.damage++;
                            showNotification('Danno aumentato!', '#ff6600');
                            break;
                    }
                    
                    updateHUD();
                    return false;
                }
                return true;
            });
            
            // Controlla NPC
            world.npcs.forEach(npc => {
                const dist = Math.sqrt(Math.pow(npc.x - player.x, 2) + Math.pow(npc.y - player.y, 2));
                if (dist < 2) {
                    showDialogue(npc.name, npc.dialogue[npc.dialogueIndex]);
                    npc.dialogueIndex = (npc.dialogueIndex + 1) % npc.dialogue.length;
                }
            });
        }
        
        function checkLevelUp() {
            if (player.exp >= player.expToNext) {
                player.level++;
                player.exp -= player.expToNext;
                player.expToNext = player.level * 150;
                
                playSound('levelup');
                showNotification(`LIVELLO ${player.level}!`, '#ffff00');
                document.getElementById('levelUpScreen').classList.add('show');
                gameRunning = false;
            }
        }
        
        // ===== EFFETTI VISIVI =====
        
        function createHitEffect(x, y) {
            for (let i = 0; i < 8; i++) {
                const angle = (Math.PI * 2 / 8) * i;
                particles.push({
                    x: x,
                    y: y,
                    vx: Math.cos(angle) * 0.1,
                    vy: Math.sin(angle) * 0.1,
                    color: '#ffffff',
                    size: 5,
                    lifetime: 15
                });
            }
        }
        
        function createHealEffect(x, y) {
            for (let i = 0; i < 12; i++) {
                particles.push({
                    x: x + (Math.random() - 0.5) * 0.5,
                    y: y + (Math.random() - 0.5) * 0.5,
                    vx: 0,
                    vy: -0.05,
                    color: '#00ff00',
                    size: 8,
                    lifetime: 30
                });
            }
        }
        
        function createExplosion(x, y) {
            camera.shake = 5;
            for (let i = 0; i < 20; i++) {
                const angle = Math.random() * Math.PI * 2;
                const speed = Math.random() * 0.2 + 0.1;
                particles.push({
                    x: x,
                    y: y,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed,
                    color: ['#ff0000', '#ff6600', '#ffff00'][Math.floor(Math.random() * 3)],
                    size: 10,
                    lifetime: 30
                });
            }
        }
        
        function createDamageNumber(x, y, damage) {
            particles.push({
                x: x,
                y: y,
                vx: (Math.random() - 0.5) * 0.02,
                vy: -0.05,
                color: '#ffff00',
                size: 20,
                lifetime: 30,
                text: damage.toString()
            });
        }
        
        // ===== RENDERING =====
        
        function drawTile(x, y, type) {
            const pos = worldToScreen(x, y);
            
            ctx.save();
            ctx.translate(pos.x, pos.y);
            
            if (type === 'floor') {
                // Pavimento cyber
                ctx.fillStyle = '#1a1a3e';
                ctx.beginPath();
                ctx.moveTo(0, 0);
                ctx.lineTo(TILE_WIDTH/2, TILE_HEIGHT/2);
                ctx.lineTo(0, TILE_HEIGHT);
                ctx.lineTo(-TILE_WIDTH/2, TILE_HEIGHT/2);
                ctx.closePath();
                ctx.fill();
                
                // Griglia
                ctx.strokeStyle = '#44f1ff22';
                ctx.lineWidth = 1;
                ctx.stroke();
                
                // Pattern digitale
                if ((x + y) % 3 === 0) {
                    ctx.fillStyle = '#44f1ff11';
                    ctx.fillRect(-5, TILE_HEIGHT/2 - 5, 10, 10);
                }
            } else if (type === 'wall') {
                // Muro con altezza
                const height = 50;
                
                // Faccia superiore
                ctx.fillStyle = '#2a2a4e';
                ctx.beginPath();
                ctx.moveTo(0, -height);
                ctx.lineTo(TILE_WIDTH/2, TILE_HEIGHT/2 - height);
                ctx.lineTo(0, TILE_HEIGHT - height);
                ctx.lineTo(-TILE_WIDTH/2, TILE_HEIGHT/2 - height);
                ctx.closePath();
                ctx.fill();
                
                // Pattern circuiti
                ctx.strokeStyle = '#44f1ff44';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(-TILE_WIDTH/4, TILE_HEIGHT/2 - height);
                ctx.lineTo(TILE_WIDTH/4, TILE_HEIGHT/2 - height);
                ctx.stroke();
                
                // Faccia sinistra
                ctx.fillStyle = '#1f1f3a';
                ctx.beginPath();
                ctx.moveTo(-TILE_WIDTH/2, TILE_HEIGHT/2 - height);
                ctx.lineTo(-TILE_WIDTH/2, TILE_HEIGHT/2);
                ctx.lineTo(0, TILE_HEIGHT);
                ctx.lineTo(0, TILE_HEIGHT - height);
                ctx.closePath();
                ctx.fill();
                
                // Faccia destra
                ctx.fillStyle = '#252545';
                ctx.beginPath();
                ctx.moveTo(0, TILE_HEIGHT - height);
                ctx.lineTo(0, TILE_HEIGHT);
                ctx.lineTo(TILE_WIDTH/2, TILE_HEIGHT/2);
                ctx.lineTo(TILE_WIDTH/2, TILE_HEIGHT/2 - height);
                ctx.closePath();
                ctx.fill();
                
                // Bordi neon
                ctx.strokeStyle = '#44f1ff66';
                ctx.lineWidth = 2;
                ctx.shadowBlur = 10;
                ctx.shadowColor = '#44f1ff';
                ctx.beginPath();
                ctx.moveTo(0, -height);
                ctx.lineTo(TILE_WIDTH/2, TILE_HEIGHT/2 - height);
                ctx.lineTo(0, TILE_HEIGHT - height);
                ctx.lineTo(-TILE_WIDTH/2, TILE_HEIGHT/2 - height);
                ctx.closePath();
                ctx.stroke();
            } else if (type === 'corrupted') {
                // Tile corrotto
                ctx.fillStyle = '#2a1a3e';
                ctx.beginPath();
                ctx.moveTo(0, 0);
                ctx.lineTo(TILE_WIDTH/2, TILE_HEIGHT/2);
                ctx.lineTo(0, TILE_HEIGHT);
                ctx.lineTo(-TILE_WIDTH/2, TILE_HEIGHT/2);
                ctx.closePath();
                ctx.fill();
                
                // Effetto glitch
                ctx.fillStyle = `rgba(255, 0, 255, ${Math.sin(gameFrame * 0.1 + x + y) * 0.2 + 0.2})`;
                ctx.fill();
                
                // Simboli corrotti
                ctx.fillStyle = '#ff00ff';
                ctx.font = '10px monospace';
                ctx.textAlign = 'center';
                ctx.fillText(['0', '1', 'X', '!'][Math.floor(gameFrame / 30 + x + y) % 4], 0, TILE_HEIGHT/2);
            }
            
            ctx.restore();
        }
        
        function drawDecoration(deco) {
            const pos = worldToScreen(deco.x, deco.y);
            
            ctx.save();
            ctx.translate(pos.x, pos.y);
            
            if (deco.type === 'circuit') {
                // Circuito decorativo
                ctx.strokeStyle = '#44f1ff33';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(-10, 0);
                ctx.lineTo(10, 0);
                ctx.moveTo(0, -10);
                ctx.lineTo(0, 10);
                ctx.stroke();
                
                ctx.fillStyle = '#44f1ff66';
                ctx.fillRect(-3, -3, 6, 6);
            } else if (deco.type === 'data_stream') {
                // Flusso di dati
                const offset = (gameFrame * 0.05) % 1;
                ctx.fillStyle = `rgba(68, 241, 255, ${0.3 - offset * 0.3})`;
                ctx.fillRect(-2, -20 + offset * 40, 4, 10);
            }
            
            ctx.restore();
        }
        
        function drawDoor(door) {
            const pos = worldToScreen(door.x, door.y);
            
            ctx.save();
            ctx.translate(pos.x, pos.y);
            
            // Porta
            ctx.fillStyle = door.locked ? '#ff0000' : '#00ff00';
            ctx.fillRect(-TILE_WIDTH/2, -30, TILE_WIDTH, 60);
            
            // Simbolo
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 20px monospace';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(door.locked ? '🔒' : '🔓', 0, 0);
            
            // Glow
            ctx.shadowBlur = 20;
            ctx.shadowColor = door.locked ? '#ff0000' : '#00ff00';
            ctx.strokeStyle = door.locked ? '#ff0000' : '#00ff00';
            ctx.lineWidth = 3;
            ctx.strokeRect(-TILE_WIDTH/2, -30, TILE_WIDTH, 60);
            
            ctx.restore();
        }
        
        function drawPlayer() {
            const pos = worldToScreen(player.x, player.y);
            
            ctx.save();
            ctx.translate(pos.x, pos.y);
            
            // Effetto invulnerabilità
            if (player.invulnerable > 0 && Math.floor(player.invulnerable / 10) % 2 === 0) {
                ctx.globalAlpha = 0.5;
            }
            
            // Ombra
            ctx.fillStyle = '#00000066';
            ctx.beginPath();
            ctx.ellipse(0, 10, 15, 8, 0, 0, Math.PI * 2);
            ctx.fill();
            
            // Corpo di Eraldin
            const bounce = player.moving ? Math.sin(player.animFrame) * 2 : 0;
            ctx.translate(0, -20 + bounce);
            
            // Effetto attacco
            if (player.attacking) {
                ctx.save();
                const attackOffset = {
                    'up': { x: 0, y: -20 },
                    'down': { x: 0, y: 20 },
                    'left': { x: -20, y: 0 },
                    'right': { x: 20, y: 0 }
                }[player.direction];
                
                ctx.translate(attackOffset.x, attackOffset.y);
                ctx.rotate(Math.random() * 0.2 - 0.1);
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 3;
                ctx.shadowBlur = 20;
                ctx.shadowColor = '#ffffff';
                ctx.beginPath();
                ctx.arc(0, 0, 15, 0, Math.PI * 0.5);
                ctx.stroke();
                ctx.restore();
            }
            
            // Glow del personaggio
            ctx.shadowBlur = 15;
            ctx.shadowColor = '#44f1ff';
            
            // Corpo principale
            const gradient = ctx.createLinearGradient(-10, -10, 10, 10);
            gradient.addColorStop(0, '#44f1ff');
            gradient.addColorStop(0.5, '#66f1ff');
            gradient.addColorStop(1, '#2288ff');
            
            ctx.fillStyle = gradient;
            ctx.beginPath();
            ctx.moveTo(0, -20);
            ctx.lineTo(10, -10);
            ctx.lineTo(10, 5);
            ctx.lineTo(0, 10);
            ctx.lineTo(-10, 5);
            ctx.lineTo(-10, -10);
            ctx.closePath();
            ctx.fill();
            
            // Dettagli armatura
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.stroke();
            
            // Pattern cyber
            ctx.strokeStyle = '#ffffff44';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(-5, -15);
            ctx.lineTo(5, -15);
            ctx.moveTo(0, -10);
            ctx.lineTo(0, 0);
            ctx.stroke();
            
            // Occhi
            ctx.fillStyle = '#ff2080';
            ctx.shadowColor = '#ff2080';
            ctx.shadowBlur = 10;
            ctx.beginPath();
            ctx.arc(-4, -12, 3, 0, Math.PI * 2);
            ctx.arc(4, -12, 3, 0, Math.PI * 2);
            ctx.fill();
            
            // Livello del giocatore
            ctx.fillStyle = '#ffff00';
            ctx.font = 'bold 10px monospace';
            ctx.textAlign = 'center';
            ctx.shadowColor = '#ffff00';
            ctx.fillText(`LV${player.level}`, 0, -30);
            
            ctx.restore();
        }
        
        function drawEnemy(enemy) {
            const pos = worldToScreen(enemy.x, enemy.y);
            
            ctx.save();
            ctx.translate(pos.x, pos.y);
            
            // Effetto stun
            if (enemy.stunned > 0) {
                ctx.rotate(Math.sin(enemy.stunned * 0.5) * 0.2);
            }
            
            // Ombra
            ctx.fillStyle = '#00000066';
            ctx.beginPath();
            ctx.ellipse(0, 10, 12, 6, 0, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.translate(0, -15);
            
            // Animazione
            const pulse = Math.sin(gameFrame * 0.1 + enemy.x + enemy.y) * 0.1 + 1;
            ctx.scale(pulse, pulse);
            
            let color = '#ff0000';
            let shape = 'diamond';
            let size = 15;
            
            // Aspetto basato sul tipo
            switch(enemy.type) {
                case 'bug':
                    color = '#ff4444';
                    shape = 'triangle';
                    size = 12;
                    break;
                case 'glitch':
                    color = '#ff00ff';
                    shape = 'square';
                    size = 14;
                    break;
                case 'virus':
                    color = '#00ff00';
                    shape = 'spike';
                    size = 13;
                    break;
                case 'trojan':
                    color = '#ff8800';
                    shape = 'hexagon';
                    size = 16;
                    break;
                case 'worm':
                    color = '#8800ff';
                    shape = 'circle';
                    size = 14;
                    break;
                case 'boss':
                    color = '#ff0066';
                    shape = 'star';
                    size = 30;
                    break;
            }
            
            // Disegna forma nemica
            ctx.shadowBlur = 20;
            ctx.shadowColor = color;
            ctx.fillStyle = color;
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            
            ctx.beginPath();
            switch(shape) {
                case 'triangle':
                    ctx.moveTo(0, -size);
                    ctx.lineTo(size, size);
                    ctx.lineTo(-size, size);
                    ctx.closePath();
                    break;
                case 'square':
                    ctx.rect(-size, -size, size*2, size*2);
                    break;
                case 'diamond':
                    ctx.moveTo(0, -size);
                    ctx.lineTo(size, 0);
                    ctx.lineTo(0, size);
                    ctx.lineTo(-size, 0);
                    ctx.closePath();
                    break;
                case 'hexagon':
                    for (let i = 0; i < 6; i++) {
                        const angle = (Math.PI / 3) * i;
                        const x = Math.cos(angle) * size;
                        const y = Math.sin(angle) * size;
                        if (i === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.closePath();
                    break;
                case 'spike':
                    for (let i = 0; i < 8; i++) {
                        const angle = (Math.PI / 4) * i;
                        const r = i % 2 === 0 ? size : size/2;
                        const x = Math.cos(angle) * r;
                        const y = Math.sin(angle) * r;
                        if (i === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.closePath();
                    break;
                case 'star':
                    for (let i = 0; i < 10; i++) {
                        const angle = (Math.PI / 5) * i;
                        const r = i % 2 === 0 ? size : size/2;
                        const x = Math.cos(angle) * r;
                        const y = Math.sin(angle) * r;
                        if (i === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.closePath();
                    break;
                default:
                    ctx.arc(0, 0, size, 0, Math.PI * 2);
            }
            ctx.fill();
            ctx.stroke();
            
            // Simbolo tipo
            ctx.fillStyle = '#000000';
            ctx.font = `bold ${size}px monospace`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            const symbols = {
                'bug': 'B',
                'glitch': 'G',
                'virus': 'V',
                'trojan': 'T',
                'worm': 'W',
                'boss': '!'
            };
            ctx.fillText(symbols[enemy.type] || '?', 0, 0);
            
            // Barra vita (se danneggiato)
            if (enemy.health < enemy.maxHealth) {
                ctx.globalAlpha = 0.8;
                ctx.fillStyle = '#000000';
                ctx.fillRect(-20, -size - 15, 40, 6);
                
                const healthPercent = enemy.health / enemy.maxHealth;
                ctx.fillStyle = healthPercent > 0.5 ? '#00ff00' : healthPercent > 0.25 ? '#ffff00' : '#ff0000';
                ctx.fillRect(-20, -size - 15, 40 * healthPercent, 6);
                
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 1;
                ctx.strokeRect(-20, -size - 15, 40, 6);
            }
            
            ctx.restore();
        }
        
        function drawObject(obj) {
            if (obj.collected) return;
            
            const pos = worldToScreen(obj.x, obj.y);
            const float = Math.sin(gameFrame * 0.05 + obj.x * obj.y) * 5;
            
            ctx.save();
            ctx.translate(pos.x, pos.y + float);
            
            // Glow effetto
            const glow = Math.sin(gameFrame * 0.1) * 0.2 + 0.8;
            
            switch(obj.type) {
                case 'patch':
                    // Patch di sistema
                    ctx.shadowBlur = 20;
                    ctx.shadowColor = '#44f1ff';
                    ctx.fillStyle = '#44f1ff';
                    ctx.fillRect(-15, -20, 30, 30);
                    
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 2;
                    ctx.strokeRect(-15, -20, 30, 30);
                    
                    // Simbolo
                    ctx.fillStyle = '#000000';
                    ctx.font = 'bold 20px monospace';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('P', 0, -5);
                    break;
                    
                case 'boss_key':
                    // Chiave del boss
                    ctx.shadowBlur = 30;
                    ctx.shadowColor = '#ffff00';
                    ctx.strokeStyle = '#ffff00';
                    ctx.fillStyle = '#ffff00';
                    ctx.lineWidth = 4;
                    
                    // Testa della chiave
                    ctx.beginPath();
                    ctx.arc(0, -15, 12, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.strokeStyle = '#ff0000';
                    ctx.stroke();
                    
                    // Corpo della chiave
                    ctx.fillRect(-3, -5, 6, 20);
                    ctx.fillRect(-8, 10, 16, 4);
                    ctx.fillRect(-8, 15, 6, 4);
                    ctx.fillRect(2, 15, 6, 4);
                    
                    // Simbolo boss
                    ctx.fillStyle = '#ff0000';
                    ctx.font = 'bold 12px monospace';
                    ctx.fillText('!', 0, -15);
                    break;
                    
                case 'health_potion':
                    // Pozione vita
                    ctx.shadowBlur = 20;
                    ctx.shadowColor = '#ff2080';
                    
                    // Bottiglia
                    ctx.fillStyle = '#ff208066';
                    ctx.beginPath();
                    ctx.arc(0, -5, 10, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.fillRect(-5, -15, 10, 15);
                    
                    ctx.strokeStyle = '#ff2080';
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.arc(0, -5, 10, 0, Math.PI * 2);
                    ctx.stroke();
                    ctx.strokeRect(-5, -15, 10, 15);
                    
                    // Simbolo
                    ctx.fillStyle = '#ffffff';
                    ctx.font = 'bold 12px monospace';
                    ctx.fillText('♥', 0, -5);
                    break;
                    
                case 'energy_crystal':
                    // Cristallo energia
                    ctx.save();
                    ctx.rotate(gameFrame * 0.02);
                    
                    ctx.shadowBlur = 25;
                    ctx.shadowColor = '#00ffff';
                    ctx.fillStyle = '#00ffff88';
                    
                    // Forma cristallo
                    ctx.beginPath();
                    ctx.moveTo(0, -15);
                    ctx.lineTo(8, -5);
                    ctx.lineTo(8, 5);
                    ctx.lineTo(0, 15);
                    ctx.lineTo(-8, 5);
                    ctx.lineTo(-8, -5);
                    ctx.closePath();
                    ctx.fill();
                    
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 2;
                    ctx.stroke();
                    
                    // Brillio interno
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(-2, -10, 4, 20);
                    
                    ctx.restore();
                    break;
                    
                case 'damage_upgrade':
                    // Potenziamento danno
                    ctx.shadowBlur = 20;
                    ctx.shadowColor = '#ff6600';
                    
                    // Spada stilizzata
                    ctx.fillStyle = '#ff6600';
                    ctx.fillRect(-3, -20, 6, 30);
                    ctx.fillRect(-10, -10, 20, 6);
                    
                    ctx.strokeStyle = '#ffffff';
                    ctx.lineWidth = 2;
                    ctx.strokeRect(-3, -20, 6, 30);
                    ctx.strokeRect(-10, -10, 20, 6);
                    
                    // Simbolo potenziamento
                    ctx.fillStyle = '#ffff00';
                    ctx.font = 'bold 12px monospace';
                    ctx.fillText('+', 0, -25);
                    break;
            }
            
            ctx.restore();
        }
        
        function drawNPC(npc) {
            const pos = worldToScreen(npc.x, npc.y);
            
            ctx.save();
            ctx.translate(pos.x, pos.y);
            
            // Ombra
            ctx.fillStyle = '#00000066';
            ctx.beginPath();
            ctx.ellipse(0, 10, 15, 8, 0, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.translate(0, -20);
            
            // Corpo NPC
            ctx.shadowBlur = 20;
            ctx.shadowColor = '#ff2080';
            
            // Ologramma effetto
            ctx.globalAlpha = 0.8 + Math.sin(gameFrame * 0.05) * 0.2;
            
            // Forma geometrica
            ctx.fillStyle = '#ff2080';
            ctx.beginPath();
            ctx.arc(0, 0, 15, 0, Math.PI * 2);
            ctx.fill();
            
            // Pattern interno
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.arc(0, 0, 10, 0, Math.PI * 2);
            ctx.stroke();
            
            // Simbolo AI
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 12px monospace';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('AI', 0, 0);
            
            // Indicatore dialogo
            const dist = Math.sqrt(Math.pow(npc.x - player.x, 2) + Math.pow(npc.y - player.y, 2));
            if (dist < 3) {
                ctx.globalAlpha = 1;
                ctx.fillStyle = '#ffff00';
                ctx.font = '16px monospace';
                ctx.fillText('💬', 0, -30);
            }
            
            // Nome
            ctx.globalAlpha = 1;
            ctx.fillStyle = '#ff2080';
            ctx.font = '10px monospace';
            ctx.fillText(npc.name, 0, 25);
            
            ctx.restore();
        }
        
        function drawProjectile(proj) {
            const pos = worldToScreen(proj.x, proj.y);
            
            ctx.save();
            ctx.translate(pos.x, pos.y - 20);
            
            // Trail
            const gradient = ctx.createLinearGradient(
                -proj.vx * 20, -proj.vy * 20,
                0, 0
            );
            gradient.addColorStop(0, 'transparent');
            gradient.addColorStop(1, proj.color);
            
            ctx.strokeStyle = gradient;
            ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.moveTo(-proj.vx * 20, -proj.vy * 20);
            ctx.lineTo(0, 0);
            ctx.stroke();
            
            // Core
            ctx.shadowBlur = 15;
            ctx.shadowColor = proj.color;
            ctx.fillStyle = proj.color;
            ctx.beginPath();
            ctx.arc(0, 0, 5, 0, Math.PI * 2);
            ctx.fill();
            
            // Centro brillante
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(0, 0, 2, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.restore();
        }
        
        function drawParticle(particle) {
            const pos = worldToScreen(particle.x, particle.y);
            
            ctx.save();
            ctx.translate(pos.x, pos.y - 20);
            
            ctx.globalAlpha = particle.lifetime / 30;
            
            if (particle.text) {
                // Numero di danno
                ctx.fillStyle = particle.color;
                ctx.font = `bold ${particle.size}px monospace`;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.shadowBlur = 10;
                ctx.shadowColor = particle.color;
                ctx.fillText(particle.text, 0, 0);
            } else {
                // Particella normale
                ctx.fillStyle = particle.color;
                ctx.shadowBlur = 10;
                ctx.shadowColor = particle.color;
                ctx.fillRect(-particle.size/2, -particle.size/2, particle.size, particle.size);
            }
            
            ctx.restore();
        }
        
        function render() {
            // Clear canvas
            ctx.fillStyle = '#0c0c1e';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Effetto griglia di sfondo
            ctx.strokeStyle = '#1a1a3e';
            ctx.lineWidth = 1;
            const gridOffset = gameFrame * 0.5 % 50;
            for (let x = -gridOffset; x < canvas.width + 50; x += 50) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, canvas.height);
                ctx.stroke();
            }
            for (let y = -gridOffset; y < canvas.height + 50; y += 50) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(canvas.width, y);
                ctx.stroke();
            }
            
            // Ordina elementi per profondità
            const renderOrder = [];
            
            // Aggiungi tiles
            for (let y = 0; y < WORLD_SIZE; y++) {
                for (let x = 0; x < WORLD_SIZE; x++) {
                    renderOrder.push({
                        type: 'tile',
                        x: x,
                        y: y,
                        depth: x + y,
                        tileType: world.tiles[x][y]
                    });
                }
            }
            
            // Aggiungi decorazioni
            world.decorations.forEach(deco => {
                renderOrder.push({
                    type: 'decoration',
                    data: deco,
                    depth: deco.x + deco.y - 0.5
                });
            });
            
            // Aggiungi porte
            world.doors.forEach(door => {
                renderOrder.push({
                    type: 'door',
                    data: door,
                    depth: door.x + door.y
                });
            });
            
            // Aggiungi oggetti
            world.objects.forEach(obj => {
                if (!obj.collected) {
                    renderOrder.push({
                        type: 'object',
                        data: obj,
                        depth: obj.x + obj.y
                    });
                }
            });
            
            // Aggiungi NPC
            world.npcs.forEach(npc => {
                renderOrder.push({
                    type: 'npc',
                    data: npc,
                    depth: npc.x + npc.y
                });
            });
            
            // Aggiungi nemici
            world.enemies.forEach(enemy => {
                renderOrder.push({
                    type: 'enemy',
                    data: enemy,
                    depth: enemy.x + enemy.y
                });
            });
            
            // Aggiungi player
            renderOrder.push({
                type: 'player',
                depth: player.x + player.y
            });
            
            // Aggiungi proiettili
            projectiles.forEach(proj => {
                renderOrder.push({
                    type: 'projectile',
                    data: proj,
                    depth: proj.x + proj.y
                });
            });
            
            // Aggiungi particelle
            particles.forEach(particle => {
                renderOrder.push({
                    type: 'particle',
                    data: particle,
                    depth: particle.x + particle.y + 10
                });
            });
            
            // Ordina per profondità
            renderOrder.sort((a, b) => a.depth - b.depth);
            
            // Renderizza in ordine
            renderOrder.forEach(item => {
                switch(item.type) {
                    case 'tile':
                        drawTile(item.x, item.y, item.tileType);
                        break;
                    case 'decoration':
                        drawDecoration(item.data);
                        break;
                    case 'door':
                        drawDoor(item.data);
                        break;
                    case 'object':
                        drawObject(item.data);
                        break;
                    case 'npc':
                        drawNPC(item.data);
                        break;
                    case 'enemy':
                        drawEnemy(item.data);
                        break;
                    case 'player':
                        drawPlayer();
                        break;
                    case 'projectile':
                        drawProjectile(item.data);
                        break;
                    case 'particle':
                        drawParticle(item.data);
                        break;
                }
            });
            
            // Effetti ambientali
            ctx.save();
            ctx.globalAlpha = 0.05;
            ctx.fillStyle = `rgba(68, 241, 255, ${Math.sin(gameFrame * 0.01) * 0.5 + 0.5})`;
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.restore();
            
            // Minimap
            drawMinimap();
        }
        
        function drawMinimap() {
            minimapCtx.fillStyle = '#0c0c1e';
            minimapCtx.fillRect(0, 0, 120, 120);
            
            const scale = 120 / WORLD_SIZE;
            
            // Tiles
            for (let x = 0; x < WORLD_SIZE; x++) {
                for (let y = 0; y < WORLD_SIZE; y++) {
                    if (world.tiles[x][y] === 'wall') {
                        minimapCtx.fillStyle = '#444466';
                        minimapCtx.fillRect(x * scale, y * scale, scale, scale);
                    } else if (world.tiles[x][y] === 'corrupted') {
                        minimapCtx.fillStyle = '#660066';
                        minimapCtx.fillRect(x * scale, y * scale, scale, scale);
                    }
                }
            }
            
            // Oggetti non raccolti
            world.objects.forEach(obj => {
                if (!obj.collected) {
                    minimapCtx.fillStyle = '#44f1ff';
                    minimapCtx.fillRect(obj.x * scale - 1, obj.y * scale - 1, 3, 3);
                }
            });
            
            // Nemici
            world.enemies.forEach(enemy => {
                minimapCtx.fillStyle = enemy.isBoss ? '#ff0066' : '#ff0000';
                minimapCtx.fillRect(enemy.x * scale - 1, enemy.y * scale - 1, 3, 3);
            });
            
            // Player
            minimapCtx.fillStyle = '#00ff00';
            minimapCtx.beginPath();
            minimapCtx.arc(player.x * scale, player.y * scale, 3, 0, Math.PI * 2);
            minimapCtx.fill();
            
            // Bordo
            minimapCtx.strokeStyle = '#44f1ff';
            minimapCtx.lineWidth = 2;
            minimapCtx.strokeRect(0, 0, 120, 120);
        }
        
        // ===== UI E NOTIFICHE =====
        
        function updateHUD() {
            document.getElementById('health').textContent = Math.max(0, player.health);
            document.getElementById('maxHealth').textContent = player.maxHealth;
            document.getElementById('energy').textContent = Math.floor(player.energy);
            document.getElementById('patches').textContent = player.patches;
            document.getElementById('keys').textContent = player.keys;
            document.getElementById('crystals').textContent = player.crystals;
            
            // Barra esperienza
            const expPercent = (player.exp / player.expToNext) * 100;
            document.getElementById('expFill').style.width = expPercent + '%';
            document.getElementById('levelText').textContent = `Livello ${player.level}`;
            
            // Inventario
            const slots = document.querySelectorAll('.inventory-slot');
            slots.forEach((slot, index) => {
                slot.textContent = player.inventory[index] || '';
                slot.classList.toggle('active', index === player.activeSlot);
            });
        }
        
        function updateQuestLog() {
            const questList = document.getElementById('questList');
            questList.innerHTML = '';
            
            quests.forEach(quest => {
                const questDiv = document.createElement('div');
                questDiv.className = 'quest-item' + (quest.complete ? ' complete' : '');
                questDiv.textContent = `${quest.name} (${quest.current}/${quest.target})`;
                questList.appendChild(questDiv);
            });
        }
        
        function updateQuest(questId, amount) {
            const quest = quests.find(q => q.id === questId);
            if (quest && !quest.complete) {
                quest.current += amount;
                if (quest.current >= quest.target) {
                    quest.complete = true;
                    showNotification('Missione completata!', '#00ff00');
                    player.exp += 100;
                    score += 500;
                }
                updateQuestLog();
            }
        }
        
        function showNotification(text, color = '#44f1ff') {
            const notification = document.getElementById('notification');
            notification.textContent = text;
            notification.style.color = color;
            notification.style.borderColor = color;
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 2000);
        }
        
        function showDialogue(speaker, text) {
            const dialogue = document.getElementById('dialogue');
            document.getElementById('speaker').textContent = speaker + ':';
            document.getElementById('dialogueText').textContent = text;
            dialogue.classList.add('show');
            
            setTimeout(() => {
                dialogue.classList.remove('show');
            }, 3000);
        }
        
        function selectUpgrade(type) {
            switch(type) {
                case 'health':
                    player.maxHealth += 2;
                    player.health += 2;
                    showNotification('+2 Vita Massima!', '#ff2080');
                    break;
                case 'damage':
                    player.damage += 1;
                    showNotification('+1 Danno!', '#ff6600');
                    break;
                case 'speed':
                    player.speed *= 1.1;
                    showNotification('+10% Velocità!', '#00ffff');
                    break;
            }
            
            document.getElementById('levelUpScreen').classList.remove('show');
            gameRunning = true;
            updateHUD();
        }
        
        function gameOver() {
            gameRunning = false;
            document.getElementById('finalLevel').textContent = player.level;
            document.getElementById('finalPatches').textContent = player.patches;
            document.getElementById('enemiesDefeated').textContent = defeatedEnemies;
            document.getElementById('gameOver').classList.add('show');
        }
        
        function showVictory() {
            gameRunning = false;
            const time = Date.now() - gameStartTime;
            const minutes = Math.floor(time / 60000);
            const seconds = Math.floor((time % 60000) / 1000);
            document.getElementById('completionTime').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            document.getElementById('finalScore').textContent = score;
            document.getElementById('victoryScreen').classList.add('show');
        }
        
        // ===== GAME LOOP =====
        
        function gameLoop() {
            if (!gameRunning) {
                requestAnimationFrame(gameLoop);
                return;
            }
            
            // Update
            updatePlayer();
            updateEnemies();
            updateProjectiles();
            updateParticles();
            checkInteractions();
            checkLevelUp();
            
            // Render
            render();
            
            // Update UI
            updateHUD();
            
            // Incrementa frame
            gameFrame++;
            
            requestAnimationFrame(gameLoop);
        }
        
        // ===== INIZIALIZZAZIONE =====
        
        function init() {
            resizeCanvas();
            generateWorld();
            updateHUD();
            updateQuestLog();
            
            // Tutorial iniziale
            setTimeout(() => {
                showNotification('Usa WASD per muoverti, SPAZIO per interagire', '#44f1ff');
            }, 1000);
            
            gameLoop();
        }
        
        // Avvia il gioco quando la pagina è caricata
        window.addEventListener('load', init);
    </script>
</body>
</html>
