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

        /* Pulsante azione */
        #actionButton {
            position: absolute;
            right: 50px;
            bottom: 50px;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, #ff2080 0%, #801040 100%);
            border: 3px solid rgba(255, 32, 128, 0.5);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            font-weight: bold;
            pointer-events: all;
            box-shadow: 0 0 15px #ff2080;
        }

        #actionButton:active {
            transform: scale(0.9);
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

        .retry-button {
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

        .retry-button:hover {
            background: #ff2080;
            transform: scale(1.1);
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
            
            #actionButton {
                width: 100px;
                height: 100px;
                font-size: 28px;
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
            
            #actionButton {
                bottom: 20px;
                right: 20px;
                width: 70px;
                height: 70px;
            }
            
            #hud {
                font-size: 12px;
                padding: 5px;
            }
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
                <span id="health">3</span>
            </div>
            <div class="hud-item">
                <span>📦 Patch: </span>
                <span id="patches">0</span>
            </div>
            <div class="hud-item">
                <span>🔑 Chiavi: </span>
                <span id="keys">0</span>
            </div>
        </div>
        
        <!-- Notifiche -->
        <div id="notification"></div>
        
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
            <div id="actionButton">A</div>
        </div>
        
        <!-- Game Over -->
        <div id="gameOver">
            <h2>SISTEMA CORROTTO!</h2>
            <p>Hai raccolto <span id="finalPatches">0</span> patch di sistema.</p>
            <button class="retry-button" onclick="location.reload()">🔄 Riprova</button>
            <button class="retry-button" onclick="location.href='../index.php'">🏠 Arcade</button>
        </div>
    </div>

    <script>
        // ===== INIZIALIZZAZIONE CANVAS E VARIABILI GLOBALI =====
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        
        // Dimensioni base del gioco
        const GAME_WIDTH = 800;
        const GAME_HEIGHT = 600;
        const TILE_WIDTH = 64;
        const TILE_HEIGHT = 32;
        
        // Stato del gioco
        let gameRunning = true;
        let gameFrame = 0;
        
        // Camera e viewport
        let camera = {
            x: 0,
            y: 0,
            zoom: 1
        };
        
        // Input
        let keys = {};
        let touchInput = { x: 0, y: 0, active: false };
        let actionPressed = false;
        
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
            health: 3,
            maxHealth: 3,
            speed: 0.1,
            patches: 0,
            keys: 0,
            direction: 'down',
            animFrame: 0,
            moving: false,
            invulnerable: 0
        };
        
        // ===== MAPPA DEL MONDO =====
        const WORLD_SIZE = 20;
        const world = {
            tiles: [],
            objects: [],
            npcs: [],
            enemies: []
        };
        
        // Genera mappa
        function generateWorld() {
            // Inizializza tiles
            for (let x = 0; x < WORLD_SIZE; x++) {
                world.tiles[x] = [];
                for (let y = 0; y < WORLD_SIZE; y++) {
                    // Bordi = muri, interno = pavimento
                    if (x === 0 || y === 0 || x === WORLD_SIZE - 1 || y === WORLD_SIZE - 1) {
                        world.tiles[x][y] = 'wall';
                    } else {
                        world.tiles[x][y] = 'floor';
                    }
                }
            }
            
            // Aggiungi alcuni muri interni
            world.tiles[10][10] = 'wall';
            world.tiles[10][11] = 'wall';
            world.tiles[11][10] = 'wall';
            
            // Aggiungi oggetti collezionabili
            world.objects = [
                { x: 3, y: 3, type: 'patch', collected: false },
                { x: 15, y: 15, type: 'patch', collected: false },
                { x: 8, y: 12, type: 'key', collected: false },
                { x: 17, y: 3, type: 'patch', collected: false }
            ];
            
            // Aggiungi NPC
            world.npcs = [
                {
                    x: 7,
                    y: 7,
                    name: 'Sistema AI-42',
                    dialogue: [
                        'Benvenuto, Eraldin. La rete è corrotta.',
                        'Raccogli le patch di sistema per ripristinare il network.',
                        'Attento ai bug, danneggiano i tuoi protocolli!'
                    ],
                    dialogueIndex: 0
                }
            ];
            
            // Aggiungi nemici
            world.enemies = [
                { x: 12, y: 5, type: 'bug', health: 1, speed: 0.02, direction: { x: 1, y: 0 } },
                { x: 5, y: 15, type: 'glitch', health: 2, speed: 0.03, direction: { x: 0, y: 1 } },
                { x: 15, y: 10, type: 'virus', health: 1, speed: 0.04, direction: { x: -1, y: 1 } }
            ];
        }
        
        // ===== INPUT HANDLING =====
        
        // Keyboard
        window.addEventListener('keydown', (e) => {
            keys[e.key.toLowerCase()] = true;
            if (e.key === ' ') {
                e.preventDefault();
                actionPressed = true;
            }
        });
        
        window.addEventListener('keyup', (e) => {
            keys[e.key.toLowerCase()] = false;
            if (e.key === ' ') {
                actionPressed = false;
            }
        });
        
        // Touch controls
        const isTouchDevice = 'ontouchstart' in window;
        
        if (isTouchDevice) {
            const joystick = document.getElementById('joystick');
            const stick = document.getElementById('joystickStick');
            const actionBtn = document.getElementById('actionButton');
            
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
            
            // Action button
            actionBtn.addEventListener('touchstart', (e) => {
                e.preventDefault();
                actionPressed = true;
            });
            
            actionBtn.addEventListener('touchend', (e) => {
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
                // Converti input joystick in movimento isometrico
                dx = touchInput.x;
                dy = touchInput.y;
            }
            
            // Normalizza movimento diagonale
            if (dx !== 0 && dy !== 0) {
                dx *= 0.707;
                dy *= 0.707;
            }
            
            // Movimento
            if (dx !== 0 || dy !== 0) {
                const newX = player.x + dx * player.speed;
                const newY = player.y + dy * player.speed;
                
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
            
            // Invulnerabilità
            if (player.invulnerable > 0) {
                player.invulnerable--;
            }
            
            // Camera segue il player
            const screenPos = worldToScreen(player.x, player.y);
            camera.x += (screenPos.x - GAME_WIDTH / 2 - camera.x) * 0.1;
            camera.y += (screenPos.y - GAME_HEIGHT / 2 - camera.y) * 0.1;
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
            
            return true;
        }
        
        function updateEnemies() {
            world.enemies.forEach(enemy => {
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
                
                // AI semplice: insegue il player se vicino
                const dist = Math.sqrt(Math.pow(enemy.x - player.x, 2) + Math.pow(enemy.y - player.y, 2));
                if (dist < 5) {
                    const angle = Math.atan2(player.y - enemy.y, player.x - enemy.x);
                    enemy.direction.x = Math.cos(angle);
                    enemy.direction.y = Math.sin(angle);
                }
                
                // Collisione con player
                if (dist < 0.8 && player.invulnerable === 0) {
                    player.health--;
                    player.invulnerable = 120; // 2 secondi a 60fps
                    showNotification('Sistema danneggiato!', '#ff2080');
                    updateHUD();
                    
                    if (player.health <= 0) {
                        gameOver();
                    }
                }
            });
        }
        
        function checkInteractions() {
            if (!actionPressed) return;
            actionPressed = false; // Previeni spam
            
            // Controlla oggetti
            world.objects.forEach(obj => {
                if (!obj.collected) {
                    const dist = Math.sqrt(Math.pow(obj.x - player.x, 2) + Math.pow(obj.y - player.y, 2));
                    if (dist < 1) {
                        obj.collected = true;
                        
                        if (obj.type === 'patch') {
                            player.patches++;
                            showNotification('Patch di sistema raccolta!', '#44f1ff');
                        } else if (obj.type === 'key') {
                            player.keys++;
                            showNotification('Chiave digitale ottenuta!', '#ffff00');
                        }
                        
                        updateHUD();
                    }
                }
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
                ctx.strokeStyle = '#44f1ff33';
                ctx.lineWidth = 1;
                ctx.stroke();
            } else if (type === 'wall') {
                // Muro
                const height = 40;
                
                // Faccia superiore
                ctx.fillStyle = '#2a2a4e';
                ctx.beginPath();
                ctx.moveTo(0, -height);
                ctx.lineTo(TILE_WIDTH/2, TILE_HEIGHT/2 - height);
                ctx.lineTo(0, TILE_HEIGHT - height);
                ctx.lineTo(-TILE_WIDTH/2, TILE_HEIGHT/2 - height);
                ctx.closePath();
                ctx.fill();
                
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
                ctx.beginPath();
                ctx.moveTo(0, -height);
                ctx.lineTo(TILE_WIDTH/2, TILE_HEIGHT/2 - height);
                ctx.lineTo(0, TILE_HEIGHT - height);
                ctx.lineTo(-TILE_WIDTH/2, TILE_HEIGHT/2 - height);
                ctx.closePath();
                ctx.stroke();
            }
            
            ctx.restore();
        }
        
        function drawEntity(x, y, color, size = 20, glow = false) {
            const pos = worldToScreen(x, y);
            
            ctx.save();
            ctx.translate(pos.x, pos.y);
            
            if (glow) {
                // Effetto glow
                ctx.shadowBlur = 20;
                ctx.shadowColor = color;
            }
            
            // Forma geometrica semplice
            ctx.fillStyle = color;
            ctx.beginPath();
            ctx.arc(0, -size/2, size/2, 0, Math.PI * 2);
            ctx.fill();
            
            // Dettagli
            ctx.strokeStyle = '#ffffff44';
            ctx.lineWidth = 2;
            ctx.stroke();
            
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
            
            // Glow del personaggio
            ctx.shadowBlur = 15;
            ctx.shadowColor = '#44f1ff';
            
            // Corpo principale (forma geometrica cyber)
            ctx.fillStyle = '#44f1ff';
            ctx.beginPath();
            ctx.moveTo(0, -20);
            ctx.lineTo(10, -10);
            ctx.lineTo(10, 5);
            ctx.lineTo(0, 10);
            ctx.lineTo(-10, 5);
            ctx.lineTo(-10, -10);
            ctx.closePath();
            ctx.fill();
            
            // Dettagli cyber
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.stroke();
            
            // Occhi/sensori
            ctx.fillStyle = '#ff2080';
            ctx.shadowColor = '#ff2080';
            ctx.beginPath();
            ctx.arc(-4, -12, 3, 0, Math.PI * 2);
            ctx.arc(4, -12, 3, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.restore();
        }
        
        function drawObject(obj) {
            if (obj.collected) return;
            
            const pos = worldToScreen(obj.x, obj.y);
            const float = Math.sin(gameFrame * 0.05) * 5;
            
            ctx.save();
            ctx.translate(pos.x, pos.y + float);
            
            if (obj.type === 'patch') {
                // Patch di sistema
                ctx.shadowBlur = 15;
                ctx.shadowColor = '#44f1ff';
                ctx.fillStyle = '#44f1ff';
                ctx.fillRect(-10, -20, 20, 20);
                
                // Simbolo
                ctx.fillStyle = '#0c0c1e';
                ctx.font = 'bold 16px monospace';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText('P', 0, -10);
            } else if (obj.type === 'key') {
                // Chiave digitale
                ctx.shadowBlur = 15;
                ctx.shadowColor = '#ffff00';
                ctx.strokeStyle = '#ffff00';
                ctx.lineWidth = 3;
                
                // Forma chiave
                ctx.beginPath();
                ctx.arc(0, -15, 8, 0, Math.PI * 2);
                ctx.moveTo(0, -7);
                ctx.lineTo(0, 5);
                ctx.lineTo(-3, 5);
                ctx.lineTo(-3, 0);
                ctx.lineTo(3, 0);
                ctx.lineTo(3, 5);
                ctx.stroke();
            }
            
            ctx.restore();
        }
        
        function drawNPC(npc) {
            drawEntity(npc.x, npc.y, '#ff2080', 25, true);
            
            // Nome sopra l'NPC
            const pos = worldToScreen(npc.x, npc.y);
            ctx.save();
            ctx.translate(pos.x, pos.y - 40);
            ctx.fillStyle = '#ff2080';
            ctx.font = '12px monospace';
            ctx.textAlign = 'center';
            ctx.fillText(npc.name, 0, 0);
            ctx.restore();
        }
        
        function drawEnemy(enemy) {
            let color = '#ff0000';
            let symbol = '!';
            
            switch(enemy.type) {
                case 'bug':
                    color = '#ff4444';
                    symbol = 'B';
                    break;
                case 'glitch':
                    color = '#ff00ff';
                    symbol = 'G';
                    break;
                case 'virus':
                    color = '#00ff00';
                    symbol = 'V';
                    break;
            }
            
            const pos = worldToScreen(enemy.x, enemy.y);
            
            ctx.save();
            ctx.translate(pos.x, pos.y);
            
            // Animazione nemico
            const pulse = Math.sin(gameFrame * 0.1) * 0.2 + 1;
            ctx.scale(pulse, pulse);
            
            // Corpo nemico
            ctx.shadowBlur = 20;
            ctx.shadowColor = color;
            ctx.fillStyle = color;
            ctx.beginPath();
            ctx.moveTo(0, -15);
            ctx.lineTo(12, 0);
            ctx.lineTo(0, 15);
            ctx.lineTo(-12, 0);
            ctx.closePath();
            ctx.fill();
            
            // Simbolo
            ctx.fillStyle = '#000';
            ctx.font = 'bold 14px monospace';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(symbol, 0, 0);
            
            ctx.restore();
        }
        
        function render() {
            // Clear canvas
            ctx.fillStyle = '#0c0c1e';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Effetto griglia di sfondo
            ctx.strokeStyle = '#1a1a3e';
            ctx.lineWidth = 1;
            for (let x = 0; x < canvas.width; x += 50) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, canvas.height);
                ctx.stroke();
            }
            for (let y = 0; y < canvas.height; y += 50) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(canvas.width, y);
                ctx.stroke();
            }
            
            // Ordina elementi per profondità (Y isometrico)
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
            
            // Ordina per profondità
            renderOrder.sort((a, b) => a.depth - b.depth);
            
            // Renderizza in ordine
            renderOrder.forEach(item => {
                switch(item.type) {
                    case 'tile':
                        drawTile(item.x, item.y, item.tileType);
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
                }
            });
            
            // Effetti particellari (opzionale)
            ctx.save();
            ctx.globalAlpha = 0.5;
            for (let i = 0; i < 5; i++) {
                const x = (Math.sin(gameFrame * 0.01 + i * 2) + 1) * canvas.width / 2;
                const y = (Math.cos(gameFrame * 0.015 + i * 3) + 1) * canvas.height / 2;
                const size = Math.sin(gameFrame * 0.02 + i) * 20 + 30;
                
                const gradient = ctx.createRadialGradient(x, y, 0, x, y, size);
                gradient.addColorStop(0, '#44f1ff22');
                gradient.addColorStop(1, 'transparent');
                
                ctx.fillStyle = gradient;
                ctx.fillRect(x - size, y - size, size * 2, size * 2);
            }
            ctx.restore();
        }
        
        // ===== UI E NOTIFICHE =====
        
        function updateHUD() {
            document.getElementById('health').textContent = player.health;
            document.getElementById('patches').textContent = player.patches;
            document.getElementById('keys').textContent = player.keys;
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
        
        function gameOver() {
            gameRunning = false;
            document.getElementById('finalPatches').textContent = player.patches;
            document.getElementById('gameOver').classList.add('show');
        }
        
        // ===== GAME LOOP =====
        
        function gameLoop() {
            if (!gameRunning) return;
            
            // Update
            updatePlayer();
            updateEnemies();
            checkInteractions();
            
            // Render
            render();
            
            // Incrementa frame
            gameFrame++;
            
            requestAnimationFrame(gameLoop);
        }
        
        // ===== INIZIALIZZAZIONE =====
        
        function init() {
            resizeCanvas();
            generateWorld();
            updateHUD();
            gameLoop();
        }
        
        // Avvia il gioco quando la pagina è caricata
        window.addEventListener('load', init);
    </script>
</body>
</html>
