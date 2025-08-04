<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <title>Firewall Defender - G Tech Arcade</title>
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

        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: fixed;
        }

        body {
            background: #000;
            color: #0f0;
            font-family: 'Courier New', monospace;
            touch-action: none;
        }

        #gameWrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(ellipse at center, #001100 0%, #000000 100%);
            padding: env(safe-area-inset-top) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
        }

        #gameContainer {
            position: relative;
            width: 90vw;
            height: 90vh;
            max-width: 500px;
            max-height: 750px;
            border: 3px solid #0f0;
            box-shadow: 
                0 0 30px #0f0, 
                inset 0 0 30px rgba(0, 255, 0, 0.2),
                0 0 100px rgba(0, 255, 0, 0.3);
            background: #000;
            display: flex;
            flex-direction: column;
        }

        /* Portrait mode (default) */
        @media (orientation: portrait) {
            #gameContainer {
                width: min(90vw, 500px);
                height: min(85vh, 750px);
            }
        }

        /* Landscape mode */
        @media (orientation: landscape) {
            #gameContainer {
                width: min(60vh, 500px);
                height: min(90vh, 750px);
            }
        }

        /* Small phones (Galaxy Fold closed, iPhone SE) */
        @media (max-width: 375px) and (max-height: 700px) {
            #gameContainer {
                width: 95vw;
                height: 80vh;
                border-width: 2px;
            }
            
            #gameUI {
                padding: 1vw !important;
                font-size: 10px !important;
            }
            
            .ui-panel {
                padding: 0.3em 0.6em !important;
            }
        }

        /* Tablets and iPads */
        @media (min-width: 768px) and (min-height: 1000px) {
            #gameContainer {
                width: min(60vw, 500px);
                height: min(80vh, 750px);
            }
        }

        /* Large screens */
        @media (min-width: 1200px) {
            #gameContainer {
                width: 500px;
                height: 750px;
            }
        }

        #canvasWrapper {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        #gameCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: block;
            image-rendering: crisp-edges;
            image-rendering: -moz-crisp-edges;
            image-rendering: -webkit-crisp-edges;
            image-rendering: pixelated;
            cursor: none;
        }

        #gameUI {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            padding: 2vw;
            display: flex;
            justify-content: space-between;
            font-size: clamp(12px, 2vw, 18px);
            text-shadow: 0 0 10px #0f0, 0 0 20px #0f0;
            pointer-events: none;
            z-index: 10;
            font-weight: bold;
        }

        .ui-panel {
            background: rgba(0, 0, 0, 0.8);
            padding: 0.5em 1em;
            border: 2px solid #0f0;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
        }

        #gameOver, #startScreen {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background: rgba(0, 17, 0, 0.95);
            padding: clamp(20px, 5vw, 40px);
            border: 3px solid #0f0;
            border-radius: 10px;
            box-shadow: 
                0 0 50px #0f0,
                inset 0 0 50px rgba(0, 255, 0, 0.2);
            z-index: 100;
            width: 90%;
            max-width: 400px;
        }

        #gameOver { display: none; }

        h1, h2 {
            font-size: clamp(24px, 5vw, 48px);
            margin-bottom: 1em;
            text-shadow: 
                0 0 20px #0f0,
                0 0 40px #0f0,
                0 0 60px #0f0;
            animation: glow-pulse 2s ease-in-out infinite;
        }

        @keyframes glow-pulse {
            0%, 100% { 
                opacity: 1;
                text-shadow: 0 0 20px #0f0, 0 0 40px #0f0, 0 0 60px #0f0;
            }
            50% { 
                opacity: 0.8;
                text-shadow: 0 0 30px #0f0, 0 0 60px #0f0, 0 0 90px #0f0;
            }
        }

        .button {
            background: linear-gradient(145deg, #00ff00, #00cc00);
            color: #000;
            border: none;
            padding: clamp(10px, 2vw, 15px) clamp(20px, 4vw, 30px);
            font-size: clamp(14px, 2vw, 20px);
            font-weight: bold;
            cursor: pointer;
            margin: 0.5em;
            text-transform: uppercase;
            transition: all 0.3s;
            box-shadow: 
                0 0 20px #0f0,
                0 4px 0 #008800;
            border-radius: 5px;
            font-family: inherit;
            position: relative;
            overflow: hidden;
        }

        .button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
        }

        .button:hover::before {
            left: 100%;
        }

        .button:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 0 40px #0f0,
                0 6px 0 #008800;
        }

        .button:active {
            transform: translateY(2px);
            box-shadow: 
                0 0 20px #0f0,
                0 2px 0 #008800;
        }

        .instructions {
            margin-top: 2em;
            font-size: clamp(10px, 1.5vw, 14px);
            line-height: 1.8;
            color: #00ff00;
            text-shadow: 0 0 5px #0f0;
        }

        .instructions p {
            margin: 0.5em 0;
        }

        .powerup-indicator {
            position: absolute;
            bottom: 2vw;
            left: 50%;
            transform: translateX(-50%);
            font-size: clamp(16px, 3vw, 24px);
            font-weight: bold;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s;
            text-shadow: 
                0 0 20px currentColor,
                0 0 40px currentColor;
            z-index: 20;
        }

        /* Touch controls per mobile */
        #touchControls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 150px;
            display: none;
            z-index: 15;
            background: linear-gradient(to top, rgba(0, 17, 0, 0.8), transparent);
            padding-bottom: env(safe-area-inset-bottom);
        }

        @media (hover: none) and (pointer: coarse) {
            #touchControls {
                display: flex;
                justify-content: space-around;
                align-items: center;
                padding: 20px;
            }
            
            #gameCanvas {
                cursor: auto;
            }
            
            #gameContainer {
                padding-bottom: 150px;
            }
        }

        .touch-button {
            width: 80px;
            height: 80px;
            border: 3px solid rgba(0, 255, 0, 0.5);
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 255, 0, 0.2), transparent);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            color: #0f0;
            text-shadow: 0 0 10px #0f0;
            transition: all 0.1s;
            touch-action: none;
        }

        .touch-button:active {
            transform: scale(0.9);
            background: radial-gradient(circle, rgba(0, 255, 0, 0.4), transparent);
            border-color: #0f0;
        }

        .touch-button.fire {
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255, 0, 0, 0.3), transparent);
            border-color: rgba(255, 0, 0, 0.6);
            color: #ff0;
        }

        .touch-button.fire:active {
            background: radial-gradient(circle, rgba(255, 0, 0, 0.5), transparent);
            border-color: #ff0000;
        }

        /* Effetto scanlines migliorato */
        #gameContainer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                repeating-linear-gradient(
                    0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 0, 0.03) 2px,
                    rgba(0, 255, 0, 0.03) 4px
                ),
                repeating-linear-gradient(
                    90deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 255, 0, 0.01) 2px,
                    rgba(0, 255, 0, 0.01) 4px
                );
            pointer-events: none;
            z-index: 50;
            animation: scanlines 8s linear infinite;
        }

        @keyframes scanlines {
            0% { transform: translateY(0); }
            100% { transform: translateY(10px); }
        }

        /* Performance mode per dispositivi meno potenti */
        @media (max-width: 480px) {
            #gameContainer::before {
                display: none;
            }
        }

        /* Indicatore wave */
        .wave-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: clamp(24px, 5vw, 48px);
            font-weight: bold;
            color: #ffff00;
            text-shadow: 
                0 0 30px #ffff00,
                0 0 60px #ff0000;
            opacity: 0;
            z-index: 30;
            pointer-events: none;
            animation: wave-announce 2s ease-out;
        }

        @keyframes wave-announce {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.5);
            }
            50% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.2);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(1);
            }
        }
    </style>
</head>
<body>
    <div id="gameWrapper">
        <div id="gameContainer">
            <div id="canvasWrapper">
                <canvas id="gameCanvas"></canvas>
            </div>
            
            <div id="gameUI">
                <div class="ui-panel">
                    <div>SCORE: <span id="score">0</span></div>
                    <div>WAVE: <span id="wave">1</span></div>
                </div>
                <div class="ui-panel">
                    <div>FIREWALL: <span id="health">100</span>%</div>
                </div>
            </div>
            
            <div id="powerup-indicator" class="powerup-indicator"></div>
            <div id="wave-indicator" class="wave-indicator"></div>
            
            <div id="startScreen">
                <h1>FIREWALL DEFENDER</h1>
                <button class="button" onclick="startGame()">▶ AVVIA DIFESA</button>
                <div class="instructions">
                    <p>🛡️ PROTEGGI IL DATA CENTER G TECH</p>
                    <p>🖱️ MOUSE/TOUCH per muovere</p>
                    <p>🔫 CLICK/TAP per sparare</p>
                    <p>⚡ Raccogli POWER-UP per potenziarti</p>
                </div>
            </div>
            
            <div id="gameOver">
                <h2>SISTEMA COMPROMESSO!</h2>
                <p style="font-size: clamp(14px, 2vw, 20px); margin: 1em 0;">
                    Punteggio: <span id="finalScore" style="color: #ffff00;">0</span><br>
                    Malware Eliminati: <span id="enemiesKilled" style="color: #ff0000;">0</span><br>
                    Ondata Raggiunta: <span id="finalWave" style="color: #00ffff;">1</span>
                </p>
                <button class="button" onclick="restartGame()">🔄 RIPROVA</button>
                <button class="button" onclick="location.href='../index.php'">🏠 ARCADE</button>
            </div>
            
            <!-- Touch controls per mobile -->
            <div id="touchControls">
                <button class="touch-button" data-direction="left">◀</button>
                <button class="touch-button fire" data-action="fire">🔥</button>
                <button class="touch-button" data-direction="right">▶</button>
            </div>
        </div>
    </div>

    <script>
        // Firewall Defender - G Tech Arcade (Enhanced Version)
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        
        // Canvas dimensions
        const GAME_WIDTH = 400;
        const GAME_HEIGHT = 600;
        
        // Responsive canvas setup
        let canvasScale = 1;
        function resizeCanvas() {
            const wrapper = document.getElementById('canvasWrapper');
            const rect = wrapper.getBoundingClientRect();
            
            // Set canvas internal size
            canvas.width = GAME_WIDTH;
            canvas.height = GAME_HEIGHT;
            
            // Calculate scale to fit container
            const scaleX = rect.width / GAME_WIDTH;
            const scaleY = rect.height / GAME_HEIGHT;
            canvasScale = Math.min(scaleX, scaleY);
            
            // Apply scale transform
            canvas.style.width = (GAME_WIDTH * canvasScale) + 'px';
            canvas.style.height = (GAME_HEIGHT * canvasScale) + 'px';
            
            // Center canvas in wrapper
            canvas.style.position = 'absolute';
            canvas.style.left = ((rect.width - GAME_WIDTH * canvasScale) / 2) + 'px';
            canvas.style.top = ((rect.height - GAME_HEIGHT * canvasScale) / 2) + 'px';
        }
        
        window.addEventListener('resize', resizeCanvas);
        window.addEventListener('orientationchange', () => {
            setTimeout(resizeCanvas, 100);
        });
        
        // Call resize on load
        setTimeout(resizeCanvas, 100);
        
        // Game state
        let gameRunning = false;
        let score = 0;
        let enemiesKilled = 0;
        let waveNumber = 1;
        let gameFrame = 0;
        let isMobile = 'ontouchstart' in window;
        
        // Player (Firewall)
        const player = {
            x: GAME_WIDTH / 2,
            y: GAME_HEIGHT - 80,
            width: 60,
            height: 40,
            health: 100,
            maxHealth: 100,
            speed: isMobile ? 8 : 5,
            fireRate: 5,
            fireTimer: 0,
            powerUps: {
                rapidFire: 0,
                shield: 0,
                multiShot: 0
            }
        };
        
        // Game arrays
        let bullets = [];
        let enemies = [];
        let particles = [];
        let powerUps = [];
        let stars = [];
        let backgroundEffects = [];
        
        // Enhanced enemy types with better graphics
        const enemyTypes = {
            trojan: {
                width: 40,
                height: 40,
                speed: 1.5,
                health: 2,
                points: 100,
                color: '#ff0044',
                glowColor: '#ff6666',
                pattern: 'triangle'
            },
            ransomware: {
                width: 45,
                height: 45,
                speed: 1,
                health: 3,
                points: 200,
                color: '#ff00ff',
                glowColor: '#ff66ff',
                pattern: 'lock'
            },
            botnet: {
                width: 35,
                height: 35,
                speed: 2,
                health: 1,
                points: 150,
                color: '#ffff00',
                glowColor: '#ffff66',
                pattern: 'hexagon'
            },
            worm: {
                width: 50,
                height: 30,
                speed: 1.2,
                health: 4,
                points: 250,
                color: '#ff8800',
                glowColor: '#ffaa44',
                pattern: 'worm'
            },
            virus: {
                width: 30,
                height: 30,
                speed: 2.5,
                health: 1,
                points: 175,
                color: '#00ff00',
                glowColor: '#66ff66',
                pattern: 'spike'
            }
        };
        
        // Initialize background elements
        function initBackground() {
            // Stars
            stars = [];
            for (let i = 0; i < 100; i++) {
                stars.push({
                    x: Math.random() * GAME_WIDTH,
                    y: Math.random() * GAME_HEIGHT,
                    size: Math.random() * 2,
                    speed: 0.5 + Math.random() * 1.5,
                    brightness: Math.random()
                });
            }
            
            // Grid lines
            backgroundEffects = [];
            for (let i = 0; i < 10; i++) {
                backgroundEffects.push({
                    y: i * 60,
                    alpha: 0.1 + Math.random() * 0.1
                });
            }
        }
        
        // Input handling
        let inputX = GAME_WIDTH / 2;
        let firePressed = false;
        
        // Mouse controls
        if (!isMobile) {
            canvas.addEventListener('mousemove', (e) => {
                const rect = canvas.getBoundingClientRect();
                inputX = (e.clientX - rect.left) / canvasScale;
            });
            
            canvas.addEventListener('mousedown', () => firePressed = true);
            canvas.addEventListener('mouseup', () => firePressed = false);
        }
        
        // Touch controls
        if (isMobile) {
            let touchActive = false;
            let moveDirection = 0; // -1 left, 0 none, 1 right
            
            // Canvas touch for direct control
            canvas.addEventListener('touchstart', (e) => {
                e.preventDefault();
                const touch = e.touches[0];
                const rect = canvas.getBoundingClientRect();
                const x = (touch.clientX - rect.left) / canvasScale;
                inputX = x;
                touchActive = true;
            });
            
            canvas.addEventListener('touchmove', (e) => {
                e.preventDefault();
                if (e.touches.length > 0 && touchActive) {
                    const touch = e.touches[0];
                    const rect = canvas.getBoundingClientRect();
                    const x = (touch.clientX - rect.left) / canvasScale;
                    inputX = x;
                }
            });
            
            canvas.addEventListener('touchend', () => {
                touchActive = false;
            });
            
            // Virtual button controls
            const touchButtons = document.querySelectorAll('.touch-button');
            
            touchButtons.forEach(button => {
                // Touch start
                button.addEventListener('touchstart', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (button.dataset.direction === 'left') {
                        moveDirection = -1;
                    } else if (button.dataset.direction === 'right') {
                        moveDirection = 1;
                    } else if (button.dataset.action === 'fire') {
                        firePressed = true;
                    }
                });
                
                // Touch end
                button.addEventListener('touchend', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (button.dataset.direction) {
                        moveDirection = 0;
                    } else if (button.dataset.action === 'fire') {
                        firePressed = false;
                    }
                });
                
                // Prevent context menu
                button.addEventListener('contextmenu', (e) => e.preventDefault());
            });
            
            // Update input based on button press
            setInterval(() => {
                if (moveDirection !== 0) {
                    inputX = Math.max(30, Math.min(GAME_WIDTH - 30, player.x + moveDirection * 10));
                }
            }, 16);
        }
        
        // Enhanced drawing functions
        function drawPlayer() {
            ctx.save();
            
            // Shield effect
            if (player.powerUps.shield > 0) {
                // Animated shield bubble
                const shieldRadius = 45 + Math.sin(gameFrame * 0.1) * 5;
                const gradient = ctx.createRadialGradient(player.x, player.y, 0, player.x, player.y, shieldRadius);
                gradient.addColorStop(0, 'rgba(0, 255, 255, 0.3)');
                gradient.addColorStop(0.7, 'rgba(0, 255, 255, 0.1)');
                gradient.addColorStop(1, 'rgba(0, 255, 255, 0)');
                
                ctx.fillStyle = gradient;
                ctx.beginPath();
                ctx.arc(player.x, player.y, shieldRadius, 0, Math.PI * 2);
                ctx.fill();
                
                // Shield border
                ctx.strokeStyle = '#00ffff';
                ctx.lineWidth = 2;
                ctx.shadowBlur = 20;
                ctx.shadowColor = '#00ffff';
                ctx.stroke();
            }
            
            // Firewall body with gradient
            const firewallGradient = ctx.createLinearGradient(
                player.x - player.width/2, player.y - player.height/2,
                player.x + player.width/2, player.y + player.height/2
            );
            firewallGradient.addColorStop(0, '#00ff00');
            firewallGradient.addColorStop(0.5, '#00cc00');
            firewallGradient.addColorStop(1, '#008800');
            
            ctx.fillStyle = firewallGradient;
            ctx.shadowBlur = 20;
            ctx.shadowColor = '#00ff00';
            
            // Draw firewall shape
            ctx.beginPath();
            ctx.moveTo(player.x - player.width/2, player.y + player.height/2);
            ctx.lineTo(player.x - player.width/2, player.y - player.height/2);
            ctx.lineTo(player.x - player.width/2 + 10, player.y - player.height/2 - 10);
            ctx.lineTo(player.x + player.width/2 - 10, player.y - player.height/2 - 10);
            ctx.lineTo(player.x + player.width/2, player.y - player.height/2);
            ctx.lineTo(player.x + player.width/2, player.y + player.height/2);
            ctx.closePath();
            ctx.fill();
            
            // Brick pattern
            ctx.strokeStyle = '#004400';
            ctx.lineWidth = 1;
            ctx.shadowBlur = 0;
            for (let i = 0; i < 4; i++) {
                for (let j = 0; j < 3; j++) {
                    const offset = (i % 2) * 10;
                    ctx.strokeRect(
                        player.x - player.width/2 + j * 20 + offset,
                        player.y - player.height/2 + i * 10,
                        20, 10
                    );
                }
            }
            
            // Central core glow
            const coreGradient = ctx.createRadialGradient(player.x, player.y, 0, player.x, player.y, 15);
            coreGradient.addColorStop(0, '#ffffff');
            coreGradient.addColorStop(0.5, '#00ff00');
            coreGradient.addColorStop(1, 'transparent');
            
            ctx.fillStyle = coreGradient;
            ctx.beginPath();
            ctx.arc(player.x, player.y, 15, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.restore();
        }
        
        function drawEnemy(enemy) {
            ctx.save();
            
            // Enemy glow effect
            ctx.shadowBlur = 20;
            ctx.shadowColor = enemy.glowColor;
            
            // Draw based on pattern type
            switch(enemy.pattern) {
                case 'triangle':
                    // Trojan - Sharp triangle
                    ctx.fillStyle = enemy.color;
                    ctx.beginPath();
                    ctx.moveTo(enemy.x, enemy.y - enemy.height/2);
                    ctx.lineTo(enemy.x - enemy.width/2, enemy.y + enemy.height/2);
                    ctx.lineTo(enemy.x + enemy.width/2, enemy.y + enemy.height/2);
                    ctx.closePath();
                    ctx.fill();
                    
                    // Inner details
                    ctx.strokeStyle = enemy.glowColor;
                    ctx.lineWidth = 2;
                    ctx.beginPath();
                    ctx.moveTo(enemy.x, enemy.y);
                    ctx.lineTo(enemy.x - enemy.width/4, enemy.y + enemy.height/4);
                    ctx.lineTo(enemy.x + enemy.width/4, enemy.y + enemy.height/4);
                    ctx.closePath();
                    ctx.stroke();
                    break;
                    
                case 'lock':
                    // Ransomware - Lock shape
                    ctx.fillStyle = enemy.color;
                    // Lock body
                    ctx.fillRect(enemy.x - enemy.width/3, enemy.y - enemy.height/4, enemy.width*2/3, enemy.height/2);
                    // Lock shackle
                    ctx.strokeStyle = enemy.color;
                    ctx.lineWidth = 4;
                    ctx.beginPath();
                    ctx.arc(enemy.x, enemy.y - enemy.height/4, enemy.width/3, Math.PI, 0, false);
                    ctx.stroke();
                    
                    // Keyhole
                    ctx.fillStyle = '#000';
                    ctx.beginPath();
                    ctx.arc(enemy.x, enemy.y, 5, 0, Math.PI * 2);
                    ctx.fill();
                    break;
                    
                case 'hexagon':
                    // Botnet - Hexagon network
                    ctx.fillStyle = enemy.color;
                    ctx.beginPath();
                    for (let i = 0; i < 6; i++) {
                        const angle = (Math.PI / 3) * i;
                        const x = enemy.x + Math.cos(angle) * enemy.width/2;
                        const y = enemy.y + Math.sin(angle) * enemy.height/2;
                        if (i === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.closePath();
                    ctx.fill();
                    
                    // Network connections
                    ctx.strokeStyle = enemy.glowColor;
                    ctx.lineWidth = 1;
                    for (let i = 0; i < 6; i++) {
                        ctx.beginPath();
                        ctx.moveTo(enemy.x, enemy.y);
                        const angle = (Math.PI / 3) * i;
                        ctx.lineTo(
                            enemy.x + Math.cos(angle) * enemy.width/2,
                            enemy.y + Math.sin(angle) * enemy.height/2
                        );
                        ctx.stroke();
                    }
                    break;
                    
                case 'worm':
                    // Worm - Segmented body
                    ctx.strokeStyle = enemy.color;
                    ctx.lineWidth = enemy.height;
                    ctx.lineCap = 'round';
                    ctx.beginPath();
                    
                    for (let i = 0; i < 5; i++) {
                        const segX = enemy.x - enemy.width/2 + (i * enemy.width/4);
                        const segY = enemy.y + Math.sin(gameFrame * 0.1 + i) * 10;
                        if (i === 0) ctx.moveTo(segX, segY);
                        else ctx.lineTo(segX, segY);
                    }
                    ctx.stroke();
                    
                    // Eyes
                    ctx.fillStyle = '#ff0000';
                    ctx.beginPath();
                    ctx.arc(enemy.x + enemy.width/2 - 10, enemy.y - 5, 3, 0, Math.PI * 2);
                    ctx.arc(enemy.x + enemy.width/2 - 10, enemy.y + 5, 3, 0, Math.PI * 2);
                    ctx.fill();
                    break;
                    
                case 'spike':
                    // Virus - Spiky ball
                    ctx.fillStyle = enemy.color;
                    ctx.beginPath();
                    ctx.arc(enemy.x, enemy.y, enemy.width/3, 0, Math.PI * 2);
                    ctx.fill();
                    
                    // Spikes
                    ctx.strokeStyle = enemy.color;
                    ctx.lineWidth = 3;
                    for (let i = 0; i < 8; i++) {
                        const angle = (Math.PI / 4) * i + gameFrame * 0.05;
                        ctx.beginPath();
                        ctx.moveTo(enemy.x, enemy.y);
                        ctx.lineTo(
                            enemy.x + Math.cos(angle) * enemy.width/2,
                            enemy.y + Math.sin(angle) * enemy.height/2
                        );
                        ctx.stroke();
                        
                        // Spike tips
                        ctx.fillStyle = enemy.glowColor;
                        ctx.beginPath();
                        ctx.arc(
                            enemy.x + Math.cos(angle) * enemy.width/2,
                            enemy.y + Math.sin(angle) * enemy.height/2,
                            2, 0, Math.PI * 2
                        );
                        ctx.fill();
                    }
                    break;
            }
            
            // Health bar (only if damaged)
            if (enemy.health < enemy.maxHealth) {
                ctx.shadowBlur = 0;
                ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
                ctx.fillRect(enemy.x - 20, enemy.y - enemy.height/2 - 15, 40, 6);
                
                const healthPercent = enemy.health / enemy.maxHealth;
                ctx.fillStyle = healthPercent > 0.5 ? '#00ff00' : healthPercent > 0.25 ? '#ffff00' : '#ff0000';
                ctx.fillRect(enemy.x - 20, enemy.y - enemy.height/2 - 15, 40 * healthPercent, 6);
                
                ctx.strokeStyle = '#ffffff';
                ctx.lineWidth = 1;
                ctx.strokeRect(enemy.x - 20, enemy.y - enemy.height/2 - 15, 40, 6);
            }
            
            ctx.restore();
        }
        
        function drawBullet(bullet) {
            ctx.save();
            
            // Bullet trail
            const gradient = ctx.createLinearGradient(
                bullet.x, bullet.y + 10,
                bullet.x, bullet.y - 10
            );
            gradient.addColorStop(0, 'transparent');
            gradient.addColorStop(1, bullet.color);
            
            ctx.strokeStyle = gradient;
            ctx.lineWidth = 4;
            ctx.beginPath();
            ctx.moveTo(bullet.x, bullet.y + 10);
            ctx.lineTo(bullet.x, bullet.y - 10);
            ctx.stroke();
            
            // Bullet core
            ctx.fillStyle = bullet.color;
            ctx.shadowBlur = 15;
            ctx.shadowColor = bullet.color;
            ctx.beginPath();
            ctx.arc(bullet.x, bullet.y - 10, 3, 0, Math.PI * 2);
            ctx.fill();
            
            // Inner glow
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(bullet.x, bullet.y - 10, 1, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.restore();
        }
        
        function drawPowerUp(powerUp) {
            ctx.save();
            
            // Animated container
            const pulse = Math.sin(gameFrame * 0.1) * 5;
            const rotation = gameFrame * 0.02;
            
            ctx.translate(powerUp.x, powerUp.y);
            ctx.rotate(rotation);
            
            // Outer glow
            const glowGradient = ctx.createRadialGradient(0, 0, 0, 0, 0, 25 + pulse);
            glowGradient.addColorStop(0, powerUp.color + '66');
            glowGradient.addColorStop(0.5, powerUp.color + '33');
            glowGradient.addColorStop(1, 'transparent');
            
            ctx.fillStyle = glowGradient;
            ctx.beginPath();
            ctx.arc(0, 0, 25 + pulse, 0, Math.PI * 2);
            ctx.fill();
            
            // Container hexagon
            ctx.strokeStyle = powerUp.color;
            ctx.lineWidth = 3;
            ctx.shadowBlur = 20;
            ctx.shadowColor = powerUp.color;
            ctx.beginPath();
            for (let i = 0; i < 6; i++) {
                const angle = (Math.PI / 3) * i;
                const x = Math.cos(angle) * (20 + pulse/2);
                const y = Math.sin(angle) * (20 + pulse/2);
                if (i === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
            }
            ctx.closePath();
            ctx.stroke();
            
            // Inner symbol background
            ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
            ctx.beginPath();
            ctx.arc(0, 0, 15, 0, Math.PI * 2);
            ctx.fill();
            
            // Symbol
            ctx.font = 'bold 20px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#ffffff';
            ctx.fillText(powerUp.symbol, 0, 0);
            
            ctx.restore();
        }
        
        function drawBackground() {
            // Clear with gradient
            const bgGradient = ctx.createLinearGradient(0, 0, 0, GAME_HEIGHT);
            bgGradient.addColorStop(0, '#000511');
            bgGradient.addColorStop(0.5, '#001122');
            bgGradient.addColorStop(1, '#000511');
            ctx.fillStyle = bgGradient;
            ctx.fillRect(0, 0, GAME_WIDTH, GAME_HEIGHT);
            
            // Moving grid
            ctx.strokeStyle = 'rgba(0, 255, 0, 0.05)';
            ctx.lineWidth = 1;
            backgroundEffects.forEach(line => {
                ctx.beginPath();
                ctx.moveTo(0, line.y);
                ctx.lineTo(GAME_WIDTH, line.y);
                ctx.stroke();
                
                line.y += 0.5;
                if (line.y > GAME_HEIGHT) line.y = -10;
            });
            
            // Animated stars
            stars.forEach(star => {
                const twinkle = Math.sin(gameFrame * 0.05 + star.x * star.y) * 0.5 + 0.5;
                ctx.fillStyle = `rgba(255, 255, 255, ${star.brightness * twinkle})`;
                ctx.fillRect(star.x, star.y, star.size, star.size);
                
                star.y += star.speed;
                if (star.y > GAME_HEIGHT) {
                    star.y = -10;
                    star.x = Math.random() * GAME_WIDTH;
                }
            });
        }
        
        function drawParticle(particle) {
            ctx.save();
            
            ctx.globalAlpha = particle.alpha;
            ctx.fillStyle = particle.color;
            ctx.shadowBlur = 10;
            ctx.shadowColor = particle.color;
            
            if (particle.type === 'explosion') {
                // Explosion particles with trails
                const trailLength = particle.speed * 3;
                const gradient = ctx.createLinearGradient(
                    particle.x - particle.vx * trailLength/particle.speed,
                    particle.y - particle.vy * trailLength/particle.speed,
                    particle.x, particle.y
                );
                gradient.addColorStop(0, 'transparent');
                gradient.addColorStop(1, particle.color);
                
                ctx.strokeStyle = gradient;
                ctx.lineWidth = particle.size;
                ctx.beginPath();
                ctx.moveTo(particle.x - particle.vx * trailLength/particle.speed, 
                          particle.y - particle.vy * trailLength/particle.speed);
                ctx.lineTo(particle.x, particle.y);
                ctx.stroke();
            } else {
                // Regular particles
                ctx.fillRect(particle.x - particle.size/2, particle.y - particle.size/2, 
                           particle.size, particle.size);
            }
            
            ctx.restore();
        }
        
        // Update functions remain similar but with adjusted speeds for mobile
        function updatePlayer() {
            const targetX = inputX;
            const speed = isMobile ? player.speed * 1.5 : player.speed;
            player.x += (targetX - player.x) * 0.15;
            
            player.x = Math.max(player.width/2, Math.min(canvas.width - player.width/2, player.x));
            
            player.fireTimer--;
            if (firePressed && player.fireTimer <= 0) {
                fireBullet();
                const fireRate = player.powerUps.rapidFire > 0 ? 3 : 5;
                player.fireTimer = fireRate;
            }
            
            if (player.powerUps.rapidFire > 0) player.powerUps.rapidFire--;
            if (player.powerUps.shield > 0) player.powerUps.shield--;
            if (player.powerUps.multiShot > 0) player.powerUps.multiShot--;
        }
        
        function fireBullet() {
            playShootSound();
            
            if (player.powerUps.multiShot > 0) {
                for (let i = -1; i <= 1; i++) {
                    bullets.push({
                        x: player.x + i * 15,
                        y: player.y - player.height/2,
                        speed: 12,
                        damage: 1,
                        angle: i * 0.1,
                        color: '#00ffff'
                    });
                }
            } else {
                bullets.push({
                    x: player.x,
                    y: player.y - player.height/2,
                    speed: 10,
                    damage: 1,
                    angle: 0,
                    color: '#00ff00'
                });
            }
        }
        
        function updateBullets() {
            bullets = bullets.filter(bullet => {
                bullet.y -= bullet.speed;
                bullet.x += Math.sin(bullet.angle) * bullet.speed;
                
                for (let i = enemies.length - 1; i >= 0; i--) {
                    const enemy = enemies[i];
                    if (checkCollision(bullet, enemy)) {
                        enemy.health -= bullet.damage;
                        
                        createImpactParticles(bullet.x, bullet.y, enemy.color);
                        
                        if (enemy.health <= 0) {
                            score += enemy.points;
                            enemiesKilled++;
                            createExplosion(enemy.x, enemy.y, enemy.color);
                            playExplosionSound();
                            
                            if (Math.random() < 0.2) {
                                spawnPowerUp(enemy.x, enemy.y);
                            }
                            
                            enemies.splice(i, 1);
                        }
                        
                        return false;
                    }
                }
                
                return bullet.y > -10;
            });
        }
        
        function updateEnemies() {
            enemies = enemies.filter(enemy => {
                enemy.y += enemy.speed;
                
                // Enemy-specific movement patterns
                switch(enemy.pattern) {
                    case 'hexagon':
                        enemy.x += Math.sin(enemy.y * 0.02) * 2;
                        break;
                    case 'worm':
                        enemy.x += Math.sin(gameFrame * 0.05 + enemy.id) * 3;
                        break;
                    case 'spike':
                        enemy.x += Math.cos(gameFrame * 0.03 + enemy.id) * 1.5;
                        break;
                }
                
                // Keep enemies in bounds
                if (enemy.x < enemy.width/2 || enemy.x > GAME_WIDTH - enemy.width/2) {
                    enemy.speed *= -1;
                }
                
                if (!player.powerUps.shield && checkCollision(enemy, player)) {
                    player.health -= 10;
                    createExplosion(enemy.x, enemy.y, '#ff0000');
                    updateUI();
                    return false;
                }
                
                if (enemy.y > GAME_HEIGHT + enemy.height) {
                    player.health -= 5;
                    updateUI();
                    return false;
                }
                
                return true;
            });
        }
        
        function updateParticles() {
            particles = particles.filter(particle => {
                particle.x += particle.vx;
                particle.y += particle.vy;
                particle.vy += particle.gravity || 0.1;
                particle.alpha -= particle.fadeRate || 0.02;
                particle.size *= 0.98;
                
                return particle.alpha > 0 && particle.size > 0.1;
            });
        }
        
        function updatePowerUps() {
            powerUps = powerUps.filter(powerUp => {
                powerUp.y += 2;
                powerUp.x += Math.sin(gameFrame * 0.05) * 0.5;
                
                if (checkCollision(powerUp, player)) {
                    collectPowerUp(powerUp);
                    return false;
                }
                
                return powerUp.y < GAME_HEIGHT + 30;
            });
        }
        
        function checkCollision(obj1, obj2) {
            const dist = Math.sqrt(
                Math.pow(obj1.x - obj2.x, 2) + 
                Math.pow(obj1.y - obj2.y, 2)
            );
            return dist < ((obj1.width || 20)/2 + (obj2.width || 20)/2);
        }
        
        function createImpactParticles(x, y, color) {
            for (let i = 0; i < 8; i++) {
                const angle = (Math.PI * 2 / 8) * i;
                particles.push({
                    x: x,
                    y: y,
                    vx: Math.cos(angle) * 3,
                    vy: Math.sin(angle) * 3,
                    size: 4,
                    color: color,
                    alpha: 1,
                    type: 'impact',
                    gravity: 0
                });
            }
        }
        
        function createExplosion(x, y, color) {
            for (let i = 0; i < 25; i++) {
                const angle = Math.random() * Math.PI * 2;
                const speed = 2 + Math.random() * 8;
                particles.push({
                    x: x,
                    y: y,
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed,
                    size: 3 + Math.random() * 5,
                    color: color,
                    alpha: 1,
                    type: 'explosion',
                    speed: speed,
                    fadeRate: 0.03
                });
            }
        }
        
        function spawnEnemy() {
            const types = Object.keys(enemyTypes);
            const weights = [30, 25, 20, 15, 10]; // Spawn probability weights
            const totalWeight = weights.reduce((a, b) => a + b, 0);
            let random = Math.random() * totalWeight;
            
            let selectedType = types[0];
            for (let i = 0; i < types.length; i++) {
                random -= weights[i] || 10;
                if (random <= 0) {
                    selectedType = types[i];
                    break;
                }
            }
            
            const enemyData = enemyTypes[selectedType];
            
            enemies.push({
                x: Math.random() * (GAME_WIDTH - 60) + 30,
                y: -30,
                ...enemyData,
                type: selectedType,
                maxHealth: enemyData.health,
                id: Math.random()
            });
        }
        
        function spawnPowerUp(x, y) {
            const types = [
                { type: 'shield', symbol: '🛡️', color: '#00ffff', duration: 300 },
                { type: 'rapidFire', symbol: '⚡', color: '#ffff00', duration: 300 },
                { type: 'health', symbol: '💉', color: '#00ff00', value: 30 },
                { type: 'multiShot', symbol: '💥', color: '#ff00ff', duration: 250 }
            ];
            
            const powerUp = types[Math.floor(Math.random() * types.length)];
            powerUps.push({
                x: x,
                y: y,
                width: 40,
                height: 40,
                ...powerUp
            });
        }
        
        function collectPowerUp(powerUp) {
            playPowerUpSound();
            
            const indicator = document.getElementById('powerup-indicator');
            indicator.style.color = powerUp.color;
            
            switch(powerUp.type) {
                case 'shield':
                    player.powerUps.shield = powerUp.duration;
                    showPowerUpText('SCUDO ATTIVO!', powerUp.color);
                    break;
                case 'rapidFire':
                    player.powerUps.rapidFire = powerUp.duration;
                    showPowerUpText('FUOCO RAPIDO!', powerUp.color);
                    break;
                case 'health':
                    player.health = Math.min(player.maxHealth, player.health + powerUp.value);
                    showPowerUpText('+' + powerUp.value + ' SALUTE!', powerUp.color);
                    break;
                case 'multiShot':
                    player.powerUps.multiShot = powerUp.duration;
                    showPowerUpText('TRIPLO COLPO!', powerUp.color);
                    break;
            }
            
            updateUI();
        }
        
        function showPowerUpText(text, color) {
            const indicator = document.getElementById('powerup-indicator');
            indicator.textContent = text;
            indicator.style.color = color || '#00ff00';
            indicator.style.opacity = '1';
            setTimeout(() => {
                indicator.style.opacity = '0';
            }, 2000);
        }
        
        function showWaveIndicator(waveNum) {
            const indicator = document.getElementById('wave-indicator');
            indicator.textContent = `ONDATA ${waveNum}`;
            indicator.style.opacity = '1';
            setTimeout(() => {
                indicator.style.opacity = '0';
            }, 2000);
        }
        
        function updateWave() {
            const spawnRate = Math.max(30 - waveNumber * 2, 8);
            
            if (gameFrame % spawnRate === 0) {
                spawnEnemy();
                
                // Extra enemies on higher waves
                if (waveNumber > 5 && Math.random() < 0.3) {
                    setTimeout(() => spawnEnemy(), 500);
                }
            }
            
            // Wave progression
            if (gameFrame % 600 === 0 && gameFrame > 0) {
                waveNumber++;
                showWaveIndicator(waveNumber);
                
                // Increase difficulty
                Object.keys(enemyTypes).forEach(type => {
                    enemyTypes[type].speed *= 1.1;
                    enemyTypes[type].health = Math.ceil(enemyTypes[type].health * 1.15);
                    enemyTypes[type].points = Math.ceil(enemyTypes[type].points * 1.2);
                });
                
                // Bonus points for surviving wave
                score += 500 * waveNumber;
                showPowerUpText(`+${500 * waveNumber} BONUS ONDATA!`, '#ffff00');
            }
        }
        
        function updateUI() {
            document.getElementById('score').textContent = score.toLocaleString();
            document.getElementById('health').textContent = Math.max(0, player.health);
            document.getElementById('wave').textContent = waveNumber;
            
            // Health color indicator
            const healthSpan = document.getElementById('health');
            if (player.health > 70) {
                healthSpan.style.color = '#00ff00';
            } else if (player.health > 30) {
                healthSpan.style.color = '#ffff00';
            } else {
                healthSpan.style.color = '#ff0000';
            }
            
            if (player.health <= 0) {
                gameOver();
            }
        }
        
        // Enhanced sound system
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        function playShootSound() {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            const filter = audioContext.createBiquadFilter();
            
            oscillator.connect(filter);
            filter.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            filter.type = 'highpass';
            filter.frequency.value = 400;
            
            oscillator.type = 'square';
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.exponentialRampToValueAtTime(400, audioContext.currentTime + 0.1);
            
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        }
        
        function playExplosionSound() {
            const bufferSize = audioContext.sampleRate * 0.2;
            const buffer = audioContext.createBuffer(1, bufferSize, audioContext.sampleRate);
            const data = buffer.getChannelData(0);
            
            for (let i = 0; i < bufferSize; i++) {
                data[i] = (Math.random() - 0.5) * (1 - i / bufferSize);
            }
            
            const noise = audioContext.createBufferSource();
            const gainNode = audioContext.createGain();
            const filter = audioContext.createBiquadFilter();
            
            noise.buffer = buffer;
            noise.connect(filter);
            filter.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            filter.type = 'lowpass';
            filter.frequency.setValueAtTime(3000, audioContext.currentTime);
            filter.frequency.exponentialRampToValueAtTime(400, audioContext.currentTime + 0.2);
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
            
            noise.start(audioContext.currentTime);
        }
        
        function playPowerUpSound() {
            const oscillator1 = audioContext.createOscillator();
            const oscillator2 = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator1.connect(gainNode);
            oscillator2.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator1.type = 'sine';
            oscillator2.type = 'sine';
            
            oscillator1.frequency.setValueAtTime(400, audioContext.currentTime);
            oscillator1.frequency.exponentialRampToValueAtTime(800, audioContext.currentTime + 0.15);
            
            oscillator2.frequency.setValueAtTime(500, audioContext.currentTime);
            oscillator2.frequency.exponentialRampToValueAtTime(1000, audioContext.currentTime + 0.15);
            
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
            
            oscillator1.start(audioContext.currentTime);
            oscillator2.start(audioContext.currentTime);
            oscillator1.stop(audioContext.currentTime + 0.3);
            oscillator2.stop(audioContext.currentTime + 0.3);
        }
        
        // Game loop
        function gameLoop() {
            if (!gameRunning) return;
            
            // Draw everything
            drawBackground();
            
            updatePlayer();
            updateBullets();
            updateEnemies();
            updateParticles();
            updatePowerUps();
            updateWave();
            
            powerUps.forEach(drawPowerUp);
            bullets.forEach(drawBullet);
            enemies.forEach(drawEnemy);
            particles.forEach(drawParticle);
            drawPlayer();
            
            // HUD effects
            if (player.health <= 30) {
                // Danger overlay
                ctx.fillStyle = `rgba(255, 0, 0, ${0.1 * (1 + Math.sin(gameFrame * 0.1))})`;
                ctx.fillRect(0, 0, GAME_WIDTH, GAME_HEIGHT);
            }
            
            gameFrame++;
            requestAnimationFrame(gameLoop);
        }
        
        function startGame() {
            document.getElementById('startScreen').style.display = 'none';
            gameRunning = true;
            score = 0;
            enemiesKilled = 0;
            waveNumber = 1;
            gameFrame = 0;
            
            // Reset player
            player.health = player.maxHealth;
            player.x = GAME_WIDTH / 2;
            player.powerUps = { rapidFire: 0, shield: 0, multiShot: 0 };
            
            // Clear arrays
            bullets = [];
            enemies = [];
            particles = [];
            powerUps = [];
            
            // Reset enemy stats
            const baseStats = {
                trojan: { speed: 1.5, health: 2, points: 100 },
                ransomware: { speed: 1, health: 3, points: 200 },
                botnet: { speed: 2, health: 1, points: 150 },
                worm: { speed: 1.2, health: 4, points: 250 },
                virus: { speed: 2.5, health: 1, points: 175 }
            };
            
            Object.keys(baseStats).forEach(type => {
                enemyTypes[type].speed = baseStats[type].speed;
                enemyTypes[type].health = baseStats[type].health;
                enemyTypes[type].points = baseStats[type].points;
            });
            
            initBackground();
            updateUI();
            gameLoop();
        }
        
        function gameOver() {
            gameRunning = false;
            document.getElementById('finalScore').textContent = score.toLocaleString();
            document.getElementById('enemiesKilled').textContent = enemiesKilled;
            document.getElementById('finalWave').textContent = waveNumber;
            document.getElementById('gameOver').style.display = 'block';
        }
        
        function restartGame() {
            document.getElementById('gameOver').style.display = 'none';
            startGame();
        }
        
        // Initialize
        initBackground();
    </script>
</body>
</html>
