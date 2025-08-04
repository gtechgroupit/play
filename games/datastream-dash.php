<?php
/**
 * Datastream Dash - G Tech Arcade
 * Gioco HTML5 a scorrimento orizzontale avanzato
 * (c) G Tech Group - https://www.gtechgroup.it
 */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Datastream Dash - G Tech Arcade</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: #0a0a0a;
            color: #fff;
            overflow: hidden;
            touch-action: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        #gameCanvas {
            display: block;
            background: #000;
            cursor: crosshair;
            image-rendering: optimizeSpeed;
            image-rendering: -webkit-optimize-contrast;
        }
        
        /* HUD avanzato */
        .game-ui {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 20px;
            background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent);
        }
        
        .score-panel {
            background: rgba(0, 255, 204, 0.1);
            border: 2px solid #00ffcc;
            border-radius: 10px;
            padding: 15px 25px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 30px rgba(0, 255, 204, 0.3);
        }
        
        .score-display {
            font-size: clamp(18px, 4vw, 28px);
            color: #00ffcc;
            text-shadow: 0 0 15px #00ffcc;
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        .highscore-display {
            font-size: clamp(12px, 2.5vw, 16px);
            color: #88e3ff;
            margin-top: 5px;
            opacity: 0.8;
        }
        
        .stats-panel {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .lives-display {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .life-icon {
            width: 30px;
            height: 30px;
            background: #88e3ff;
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
            box-shadow: 0 0 15px #88e3ff;
            transition: all 0.3s;
        }
        
        .life-icon.lost {
            background: #333;
            box-shadow: none;
            transform: scale(0.8);
        }
        
        .power-display {
            background: rgba(255, 238, 0, 0.1);
            border: 2px solid #ffee00;
            border-radius: 25px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(10px);
        }
        
        .power-bar {
            width: 100px;
            height: 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }
        
        .power-fill {
            height: 100%;
            background: linear-gradient(90deg, #ffee00, #ff9900);
            width: 100%;
            transition: width 0.3s;
            box-shadow: 0 0 10px #ffee00;
        }
        
        /* Combo e notifiche */
        .combo-display {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: clamp(30px, 8vw, 60px);
            font-weight: bold;
            text-align: center;
            pointer-events: none;
            opacity: 0;
            transition: none;
        }
        
        .combo-display.show {
            animation: comboPopup 1s ease-out;
        }
        
        @keyframes comboPopup {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.5);
            }
            50% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.5);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(1);
            }
        }
        
        .notification {
            position: fixed;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: clamp(20px, 5vw, 40px);
            font-weight: bold;
            text-align: center;
            pointer-events: none;
            opacity: 0;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        
        .notification.show {
            animation: slideNotification 2s ease-out;
        }
        
        @keyframes slideNotification {
            0% {
                opacity: 0;
                transform: translate(-50%, -30%);
            }
            20% {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
            80% {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -70%);
            }
        }
        
        /* Pulsanti touch migliorati */
        .touch-controls {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: none;
            gap: 15px;
            pointer-events: all;
        }
        
        .control-button {
            width: 70px;
            height: 70px;
            background: rgba(0, 255, 204, 0.2);
            border: 2px solid #00ffcc;
            border-radius: 50%;
            color: #00ffcc;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .control-button:active {
            transform: scale(0.9);
            background: rgba(0, 255, 204, 0.4);
        }
        
        .control-button.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            border-color: #666;
            color: #666;
            box-shadow: none;
        }
        
        .control-button.shield {
            border-color: #88e3ff;
            color: #88e3ff;
            background: rgba(136, 227, 255, 0.2);
            box-shadow: 0 0 20px rgba(136, 227, 255, 0.3);
        }
        
        /* Menu migliorato */
        .game-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 100;
            backdrop-filter: blur(20px);
        }
        
        .game-menu.hidden {
            display: none;
        }
        
        .menu-title {
            font-size: clamp(40px, 10vw, 80px);
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #00ffcc, #88e3ff, #ffee00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 50px rgba(0, 255, 204, 0.5);
        }
        
        .menu-subtitle {
            font-size: clamp(16px, 4vw, 24px);
            color: #88e3ff;
            margin-bottom: 50px;
            opacity: 0.8;
        }
        
        .menu-buttons {
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }
        
        .menu-button {
            padding: 20px 60px;
            font-size: clamp(18px, 4vw, 28px);
            background: linear-gradient(45deg, #00ffcc, #0099ff);
            color: #000;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 2px;
            transition: all 0.3s;
            box-shadow: 0 5px 30px rgba(0, 255, 204, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .menu-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: all 0.6s;
        }
        
        .menu-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 255, 204, 0.6);
        }
        
        .menu-button:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .stats-summary {
            margin-top: 40px;
            text-align: center;
            color: #88e3ff;
            font-size: clamp(14px, 3vw, 20px);
            line-height: 1.8;
        }
        
        /* Tutorial overlay */
        .tutorial-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
            padding: 20px;
        }
        
        .tutorial-content {
            max-width: 600px;
            background: rgba(0, 255, 204, 0.05);
            border: 2px solid #00ffcc;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        .tutorial-content h3 {
            font-size: clamp(24px, 5vw, 36px);
            color: #00ffcc;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        
        .tutorial-content p {
            font-size: clamp(14px, 3vw, 18px);
            line-height: 1.8;
            margin-bottom: 20px;
            color: #88e3ff;
        }
        
        .packet-legend {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        
        .packet-example {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .packet-icon {
            width: 30px;
            height: 30px;
            border-radius: 5px;
            box-shadow: 0 0 15px;
        }
        
        /* Responsive */
        @media (hover: none) and (pointer: coarse) {
            .touch-controls {
                display: flex;
            }
            
            #gameCanvas {
                cursor: none;
            }
        }
        
        /* Animazioni globali */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px currentColor; }
            50% { box-shadow: 0 0 40px currentColor, 0 0 60px currentColor; }
        }
    </style>
</head>
<body>
    <!-- Canvas del gioco -->
    <canvas id="gameCanvas"></canvas>
    
    <!-- UI del gioco -->
    <div class="game-ui">
        <div class="top-bar">
            <div class="score-panel">
                <div class="score-display">SCORE: <span id="score">0</span></div>
                <div class="highscore-display">HIGH: <span id="highscore">0</span></div>
            </div>
            
            <div class="stats-panel">
                <div class="lives-display" id="livesDisplay">
                    <div class="life-icon"></div>
                    <div class="life-icon"></div>
                    <div class="life-icon"></div>
                </div>
                
                <div class="power-display">
                    <span style="font-size: 12px;">POWER</span>
                    <div class="power-bar">
                        <div class="power-fill" id="powerFill"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Combo display -->
    <div class="combo-display" id="comboDisplay">
        <div style="color: #ffee00; text-shadow: 0 0 30px #ffee00;">COMBO</div>
        <div style="font-size: 150%;">x<span id="comboCount">0</span></div>
    </div>
    
    <!-- Notifiche -->
    <div class="notification" id="notification"></div>
    
    <!-- Controlli touch -->
    <div class="touch-controls">
        <button class="control-button shield" id="shieldButton">SHIELD</button>
        <button class="control-button" id="boostButton">BOOST</button>
    </div>
    
    <!-- Menu principale -->
    <div class="game-menu" id="gameMenu">
        <h1 class="menu-title">DATASTREAM DASH</h1>
        <p class="menu-subtitle">Navigate the Digital Highway</p>
        
        <div class="menu-buttons">
            <button class="menu-button" onclick="startGame()">START GAME</button>
            <button class="menu-button" onclick="showTutorial()">HOW TO PLAY</button>
        </div>
        
        <div class="stats-summary">
            <div>Best Score: <span id="menuHighscore">0</span></div>
            <div>Total Games: <span id="totalGames">0</span></div>
        </div>
    </div>
    
    <!-- Tutorial -->
    <div class="tutorial-overlay" id="tutorialOverlay">
        <div class="tutorial-content">
            <h3>How to Play</h3>
            <p>Pilot your digital ship through the datastream!</p>
            
            <div class="packet-legend">
                <div class="packet-example">
                    <div class="packet-icon" style="background: #00ffcc;"></div>
                    <span>Data Packets (+10)</span>
                </div>
                <div class="packet-example">
                    <div class="packet-icon" style="background: #88e3ff;"></div>
                    <span>Boost Power (+20)</span>
                </div>
                <div class="packet-example">
                    <div class="packet-icon" style="background: #ffee00;"></div>
                    <span>Shield (+30)</span>
                </div>
                <div class="packet-example">
                    <div class="packet-icon" style="background: #ff0033;"></div>
                    <span>Virus (Damage)</span>
                </div>
            </div>
            
            <p><strong>Controls:</strong><br>
            ↑↓ or Touch: Move ship<br>
            SPACE: Activate Boost<br>
            SHIFT: Deploy Shield</p>
            
            <p>Collect 5 good packets in a row to activate TURBO MODE!</p>
            
            <button class="menu-button" onclick="closeTutorial()">GOT IT!</button>
        </div>
    </div>
    
    <script>
        // ==========================================
        // DATASTREAM DASH - VERSIONE AVANZATA
        // ==========================================
        
        // Canvas e context
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d', { alpha: false });
        
        // Ottimizzazioni performance
        ctx.imageSmoothingEnabled = false;
        
        // DOM references
        const scoreEl = document.getElementById('score');
        const highscoreEl = document.getElementById('highscore');
        const menuHighscoreEl = document.getElementById('menuHighscore');
        const totalGamesEl = document.getElementById('totalGames');
        const livesDisplay = document.getElementById('livesDisplay');
        const powerFill = document.getElementById('powerFill');
        const comboDisplay = document.getElementById('comboDisplay');
        const comboCountEl = document.getElementById('comboCount');
        const notification = document.getElementById('notification');
        const boostBtn = document.getElementById('boostButton');
        const shieldBtn = document.getElementById('shieldButton');
        const gameMenu = document.getElementById('gameMenu');
        const tutorialOverlay = document.getElementById('tutorialOverlay');
        
        // Game state
        let gameState = 'menu'; // menu, playing, paused, gameover
        let score = 0;
        let highscore = parseInt(localStorage.getItem('datastreamHighscore') || '0');
        let totalGames = parseInt(localStorage.getItem('datastreamTotalGames') || '0');
        let lives = 3;
        let combo = 0;
        let maxCombo = 0;
        let distance = 0;
        let difficulty = 1;
        let powerLevel = 100;
        
        // Power-ups
        let boostActive = false;
        let boostTimer = 0;
        let shieldActive = false;
        let shieldTimer = 0;
        let turboMode = false;
        let turboTimer = 0;
        let magnetActive = false;
        let magnetTimer = 0;
        
        // Configurazione dinamica
        const config = {
            baseSpeed: 3,
            shipSpeed: 6,
            shipAcceleration: 0.3,
            maxShipSpeed: 10,
            packetSpawnRate: 60,
            particlePoolSize: 200,
            maxPackets: 20,
            powerDrainRate: 0.1,
            powerRegenRate: 0.05
        };
        
        // Pool di oggetti per performance
        const particlePool = [];
        const packets = [];
        const backgroundLayers = [[], [], []]; // 3 livelli di parallasse
        const stars = [];
        
        // Audio context per effetti sonori
        let audioCtx = null;
        let masterGain = null;
        
        // Input state
        const keys = {};
        const touches = { active: false, startY: 0, currentY: 0 };
        const mouse = { active: false, y: 0 };
        
        // Ship object
        const ship = {
            x: 100,
            y: 0,
            width: 60,
            height: 30,
            velocity: 0,
            targetY: 0,
            trail: [],
            hitbox: { x: 10, y: 5, width: 40, height: 20 }, // Hitbox più piccola
            animation: 0,
            damaged: false,
            damageTimer: 0
        };
        
        // ==========================================
        // AUDIO SYSTEM
        // ==========================================
        
        function initAudio() {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                masterGain = audioCtx.createGain();
                masterGain.gain.value = 0.3;
                masterGain.connect(audioCtx.destination);
            }
        }
        
        function playSound(type) {
            if (!audioCtx) return;
            
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            
            osc.connect(gain);
            gain.connect(masterGain);
            
            const now = audioCtx.currentTime;
            
            switch(type) {
                case 'collect':
                    osc.frequency.setValueAtTime(800, now);
                    osc.frequency.exponentialRampToValueAtTime(1200, now + 0.1);
                    gain.gain.setValueAtTime(0.2, now);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + 0.1);
                    osc.start(now);
                    osc.stop(now + 0.1);
                    break;
                    
                case 'damage':
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(200, now);
                    osc.frequency.exponentialRampToValueAtTime(50, now + 0.2);
                    gain.gain.setValueAtTime(0.3, now);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + 0.2);
                    osc.start(now);
                    osc.stop(now + 0.2);
                    break;
                    
                case 'powerup':
                    osc.frequency.setValueAtTime(400, now);
                    osc.frequency.exponentialRampToValueAtTime(800, now + 0.15);
                    osc.frequency.exponentialRampToValueAtTime(1600, now + 0.3);
                    gain.gain.setValueAtTime(0.2, now);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + 0.3);
                    osc.start(now);
                    osc.stop(now + 0.3);
                    break;
                    
                case 'combo':
                    for (let i = 0; i < 3; i++) {
                        const o = audioCtx.createOscillator();
                        const g = audioCtx.createGain();
                        o.connect(g);
                        g.connect(masterGain);
                        o.frequency.value = 600 + (i * 200);
                        g.gain.setValueAtTime(0.1, now + i * 0.05);
                        g.gain.exponentialRampToValueAtTime(0.01, now + i * 0.05 + 0.1);
                        o.start(now + i * 0.05);
                        o.stop(now + i * 0.05 + 0.1);
                    }
                    break;
            }
        }
        
        // ==========================================
        // PARTICLE SYSTEM AVANZATO
        // ==========================================
        
        class Particle {
            constructor() {
                this.reset();
            }
            
            reset() {
                this.active = false;
                this.x = 0;
                this.y = 0;
                this.vx = 0;
                this.vy = 0;
                this.size = 1;
                this.life = 0;
                this.maxLife = 1;
                this.color = '#fff';
                this.type = 'normal';
                this.gravity = 0;
                this.fade = true;
            }
            
            spawn(x, y, options = {}) {
                this.active = true;
                this.x = x;
                this.y = y;
                this.vx = options.vx || (Math.random() - 0.5) * 5;
                this.vy = options.vy || (Math.random() - 0.5) * 5;
                this.size = options.size || Math.random() * 3 + 1;
                this.life = this.maxLife = options.life || 60;
                this.color = options.color || '#fff';
                this.type = options.type || 'normal';
                this.gravity = options.gravity || 0;
                this.fade = options.fade !== undefined ? options.fade : true;
            }
            
            update() {
                if (!this.active) return;
                
                this.x += this.vx;
                this.y += this.vy;
                this.vy += this.gravity;
                this.life--;
                
                if (this.type === 'trail') {
                    this.vx *= 0.98;
                    this.vy *= 0.98;
                } else if (this.type === 'explosion') {
                    this.vx *= 0.95;
                    this.vy *= 0.95;
                }
                
                if (this.life <= 0) {
                    this.reset();
                }
            }
            
            draw(ctx) {
                if (!this.active) return;
                
                const alpha = this.fade ? this.life / this.maxLife : 1;
                ctx.save();
                ctx.globalAlpha = alpha;
                
                if (this.type === 'star') {
                    // Stella a 4 punte
                    ctx.fillStyle = this.color;
                    ctx.translate(this.x, this.y);
                    ctx.rotate(this.life * 0.1);
                    ctx.beginPath();
                    for (let i = 0; i < 4; i++) {
                        const angle = (i / 4) * Math.PI * 2;
                        const x = Math.cos(angle) * this.size;
                        const y = Math.sin(angle) * this.size;
                        if (i === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.closePath();
                    ctx.fill();
                } else {
                    // Cerchio standard con glow
                    ctx.fillStyle = this.color;
                    ctx.shadowColor = this.color;
                    ctx.shadowBlur = this.size * 2;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fill();
                }
                
                ctx.restore();
            }
        }
        
        // Inizializza pool particelle
        for (let i = 0; i < config.particlePoolSize; i++) {
            particlePool.push(new Particle());
        }
        
        function spawnParticle(x, y, options) {
            const particle = particlePool.find(p => !p.active);
            if (particle) {
                particle.spawn(x, y, options);
            }
        }
        
        function spawnExplosion(x, y, color, count = 15) {
            for (let i = 0; i < count; i++) {
                const angle = (i / count) * Math.PI * 2;
                const speed = Math.random() * 3 + 2;
                spawnParticle(x, y, {
                    vx: Math.cos(angle) * speed,
                    vy: Math.sin(angle) * speed,
                    color: color,
                    type: 'explosion',
                    size: Math.random() * 4 + 2,
                    life: 30 + Math.random() * 30
                });
            }
        }
        
        // ==========================================
        // PACKET SYSTEM AVANZATO
        // ==========================================
        
        class Packet {
            constructor() {
                this.reset();
            }
            
            reset() {
                this.active = false;
                this.x = 0;
                this.y = 0;
                this.width = 40;
                this.height = 40;
                this.type = 'data';
                this.subtype = 'normal';
                this.speed = 0;
                this.value = 10;
                this.color = '#00ffcc';
                this.rotation = 0;
                this.scale = 1;
                this.magnetized = false;
                this.pattern = null;
            }
            
            spawn(y, type = 'data', pattern = null) {
                this.active = true;
                this.x = canvas.width + 50;
                this.y = y;
                this.type = type;
                this.pattern = pattern;
                this.rotation = Math.random() * Math.PI * 2;
                this.scale = 1;
                this.magnetized = false;
                
                // Velocità basata su difficoltà
                this.speed = config.baseSpeed + (difficulty - 1) * 0.5;
                
                // Configurazione per tipo
                switch(type) {
                    case 'data':
                        this.subtype = Math.random() < 0.7 ? 'normal' : (Math.random() < 0.5 ? 'power' : 'shield');
                        if (this.subtype === 'normal') {
                            this.color = '#00ffcc';
                            this.value = 10;
                        } else if (this.subtype === 'power') {
                            this.color = '#88e3ff';
                            this.value = 20;
                            this.width = this.height = 35;
                        } else {
                            this.color = '#ffee00';
                            this.value = 30;
                            this.width = this.height = 45;
                        }
                        break;
                        
                    case 'virus':
                        this.color = '#ff0033';
                        this.value = -1;
                        this.subtype = Math.random() < 0.8 ? 'normal' : 'mega';
                        if (this.subtype === 'mega') {
                            this.width = this.height = 60;
                            this.color = '#9900cc';
                        }
                        break;
                        
                    case 'bonus':
                        this.color = '#00ff88';
                        this.value = 50;
                        this.width = this.height = 50;
                        this.speed *= 1.5;
                        break;
                }
            }
            
            update() {
                if (!this.active) return;
                
                // Movimento base
                this.x -= this.speed + (boostActive ? 2 : 0) + (turboMode ? 1 : 0);
                
                // Pattern di movimento
                if (this.pattern) {
                    switch(this.pattern.type) {
                        case 'sine':
                            this.y += Math.sin(this.x * 0.01) * 2;
                            break;
                        case 'zigzag':
                            this.y += Math.sin(this.x * 0.02) * 4;
                            break;
                    }
                }
                
                // Effetto magnete
                if (magnetActive && this.type === 'data') {
                    const dx = ship.x - this.x;
                    const dy = ship.y - this.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    
                    if (dist < 150) {
                        this.magnetized = true;
                        const force = (150 - dist) / 150 * 5;
                        this.x += dx / dist * force;
                        this.y += dy / dist * force;
                    }
                }
                
                // Animazioni
                this.rotation += 0.02;
                if (this.type === 'bonus') {
                    this.scale = 1 + Math.sin(Date.now() * 0.005) * 0.1;
                }
                
                // Rimuovi se fuori schermo
                if (this.x + this.width < -50) {
                    this.reset();
                }
            }
            
            draw(ctx) {
                if (!this.active) return;
                
                ctx.save();
                ctx.translate(this.x, this.y);
                ctx.rotate(this.rotation);
                ctx.scale(this.scale, this.scale);
                
                // Glow effect
                ctx.shadowColor = this.color;
                ctx.shadowBlur = 20;
                
                // Disegna forma base
                ctx.fillStyle = this.color;
                ctx.strokeStyle = this.color;
                ctx.lineWidth = 2;
                
                if (this.type === 'data') {
                    // Forma cristallo per dati
                    ctx.beginPath();
                    ctx.moveTo(0, -this.height/2);
                    ctx.lineTo(this.width/2, 0);
                    ctx.lineTo(0, this.height/2);
                    ctx.lineTo(-this.width/2, 0);
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();
                    
                    // Simbolo interno
                    ctx.fillStyle = '#000';
                    ctx.font = `bold ${this.height/3}px monospace`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    if (this.subtype === 'power') {
                        ctx.fillText('⚡', 0, 0);
                    } else if (this.subtype === 'shield') {
                        ctx.fillText('🛡', 0, 0);
                    } else {
                        ctx.fillText('◆', 0, 0);
                    }
                } else if (this.type === 'virus') {
                    // Forma irregolare virus
                    const spikes = 8;
                    ctx.beginPath();
                    for (let i = 0; i < spikes; i++) {
                        const angle = (i / spikes) * Math.PI * 2;
                        const radius = i % 2 === 0 ? this.width/2 : this.width/3;
                        const x = Math.cos(angle) * radius;
                        const y = Math.sin(angle) * radius;
                        if (i === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();
                    
                    // Occhio del virus
                    ctx.fillStyle = '#000';
                    ctx.beginPath();
                    ctx.arc(0, 0, this.width/6, 0, Math.PI * 2);
                    ctx.fill();
                } else if (this.type === 'bonus') {
                    // Stella bonus
                    const points = 5;
                    ctx.beginPath();
                    for (let i = 0; i < points * 2; i++) {
                        const angle = (i / (points * 2)) * Math.PI * 2 - Math.PI/2;
                        const radius = i % 2 === 0 ? this.width/2 : this.width/4;
                        const x = Math.cos(angle) * radius;
                        const y = Math.sin(angle) * radius;
                        if (i === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();
                }
                
                // Effetto magnetizzato
                if (this.magnetized) {
                    ctx.strokeStyle = '#fff';
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.arc(0, 0, this.width/2 + 10, 0, Math.PI * 2);
                    ctx.stroke();
                }
                
                ctx.restore();
            }
        }
        
        // ==========================================
        // BACKGROUND SYSTEM
        // ==========================================
        
        function initBackground() {
            // Layer 1: Stelle lontane
            backgroundLayers[0] = [];
            for (let i = 0; i < 50; i++) {
                backgroundLayers[0].push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    size: Math.random() * 1.5 + 0.5,
                    speed: 0.5,
                    brightness: Math.random() * 0.5 + 0.3
                });
            }
            
            // Layer 2: Linee di dati medie
            backgroundLayers[1] = [];
            for (let i = 0; i < 20; i++) {
                backgroundLayers[1].push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    length: Math.random() * 150 + 50,
                    speed: 1.5,
                    opacity: Math.random() * 0.3 + 0.1,
                    color: ['#003366', '#004466', '#005566'][Math.floor(Math.random() * 3)]
                });
            }
            
            // Layer 3: Elementi vicini
            backgroundLayers[2] = [];
            for (let i = 0; i < 10; i++) {
                backgroundLayers[2].push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    type: Math.random() < 0.5 ? 'grid' : 'hex',
                    size: Math.random() * 30 + 20,
                    speed: 2.5,
                    opacity: Math.random() * 0.2 + 0.05
                });
            }
        }
        
        function updateBackground() {
            // Update stelle
            backgroundLayers[0].forEach(star => {
                star.x -= star.speed * (1 + boostActive * 0.5);
                if (star.x < -5) {
                    star.x = canvas.width + 5;
                    star.y = Math.random() * canvas.height;
                }
            });
            
            // Update linee
            backgroundLayers[1].forEach(line => {
                line.x -= line.speed * (1 + boostActive * 0.5);
                if (line.x + line.length < 0) {
                    line.x = canvas.width;
                    line.y = Math.random() * canvas.height;
                }
            });
            
            // Update elementi
            backgroundLayers[2].forEach(elem => {
                elem.x -= elem.speed * (1 + boostActive * 0.5);
                if (elem.x + elem.size < 0) {
                    elem.x = canvas.width + elem.size;
                    elem.y = Math.random() * canvas.height;
                }
            });
        }
        
        function drawBackground() {
            // Gradiente di sfondo
            const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0, '#000814');
            gradient.addColorStop(0.5, '#001d3d');
            gradient.addColorStop(1, '#000814');
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Layer 1: Stelle
            backgroundLayers[0].forEach(star => {
                ctx.save();
                ctx.globalAlpha = star.brightness;
                ctx.fillStyle = '#ffffff';
                ctx.shadowColor = '#ffffff';
                ctx.shadowBlur = star.size * 2;
                ctx.beginPath();
                ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            });
            
            // Layer 2: Linee dati
            ctx.save();
            backgroundLayers[1].forEach(line => {
                ctx.globalAlpha = line.opacity;
                ctx.strokeStyle = line.color;
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(line.x, line.y);
                ctx.lineTo(line.x + line.length, line.y);
                ctx.stroke();
                
                // Puntini alle estremità
                ctx.fillStyle = line.color;
                ctx.beginPath();
                ctx.arc(line.x + line.length, line.y, 3, 0, Math.PI * 2);
                ctx.fill();
            });
            ctx.restore();
            
            // Layer 3: Elementi geometrici
            ctx.save();
            backgroundLayers[2].forEach(elem => {
                ctx.globalAlpha = elem.opacity;
                ctx.strokeStyle = '#00ffcc';
                ctx.lineWidth = 1;
                
                if (elem.type === 'grid') {
                    // Griglia
                    const gridSize = 10;
                    for (let i = 0; i < 3; i++) {
                        for (let j = 0; j < 3; j++) {
                            ctx.strokeRect(
                                elem.x + i * gridSize,
                                elem.y + j * gridSize,
                                gridSize,
                                gridSize
                            );
                        }
                    }
                } else {
                    // Esagono
                    ctx.beginPath();
                    for (let i = 0; i < 6; i++) {
                        const angle = (i / 6) * Math.PI * 2;
                        const x = elem.x + Math.cos(angle) * elem.size/2;
                        const y = elem.y + Math.sin(angle) * elem.size/2;
                        if (i === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.closePath();
                    ctx.stroke();
                }
            });
            ctx.restore();
        }
        
        // ==========================================
        // SHIP RENDERING AVANZATO
        // ==========================================
        
        function updateShip() {
            // Fisica migliorata
            const targetVel = (ship.targetY - ship.y) * config.shipAcceleration;
            ship.velocity += (targetVel - ship.velocity) * 0.2;
            ship.velocity = Math.max(-config.maxShipSpeed, Math.min(config.maxShipSpeed, ship.velocity));
            
            ship.y += ship.velocity;
            ship.y = Math.max(30, Math.min(canvas.height - 30, ship.y));
            
            // Trail effect
            if (frameCount % 2 === 0) {
                ship.trail.push({
                    x: ship.x - 20,
                    y: ship.y,
                    life: 20,
                    size: 15
                });
            }
            
            // Update trail
            ship.trail = ship.trail.filter(t => {
                t.x -= 3;
                t.life--;
                t.size *= 0.95;
                return t.life > 0;
            });
            
            // Animazione
            ship.animation += 0.1;
            
            // Damage animation
            if (ship.damaged) {
                ship.damageTimer--;
                if (ship.damageTimer <= 0) {
                    ship.damaged = false;
                }
            }
            
            // Particelle propulsore
            if (frameCount % 3 === 0) {
                const particleCount = boostActive ? 3 : 1;
                for (let i = 0; i < particleCount; i++) {
                    spawnParticle(
                        ship.x - 20 + Math.random() * 10,
                        ship.y + (Math.random() - 0.5) * 20,
                        {
                            vx: -Math.random() * 4 - 2,
                            vy: (Math.random() - 0.5) * 2,
                            color: turboMode ? '#ffee00' : (boostActive ? '#00ffcc' : '#0099ff'),
                            type: 'trail',
                            size: Math.random() * 3 + 1,
                            life: 20
                        }
                    );
                }
            }
        }
        
        function drawShip() {
            ctx.save();
            
            // Draw trail
            ship.trail.forEach((t, i) => {
                ctx.globalAlpha = t.life / 20 * 0.5;
                const gradient = ctx.createLinearGradient(t.x, t.y - t.size, t.x, t.y + t.size);
                gradient.addColorStop(0, 'transparent');
                gradient.addColorStop(0.5, turboMode ? '#ffee00' : '#00ffcc');
                gradient.addColorStop(1, 'transparent');
                ctx.fillStyle = gradient;
                ctx.fillRect(t.x, t.y - t.size, 30, t.size * 2);
            });
            
            ctx.globalAlpha = 1;
            ctx.translate(ship.x + ship.width/2, ship.y);
            
            // Damage flash
            if (ship.damaged && ship.damageTimer % 4 < 2) {
                ctx.globalAlpha = 0.5;
            }
            
            // Shield effect
            if (shieldActive) {
                ctx.strokeStyle = '#88e3ff';
                ctx.lineWidth = 2;
                ctx.shadowColor = '#88e3ff';
                ctx.shadowBlur = 20;
                ctx.beginPath();
                ctx.arc(0, 0, ship.width * 0.8, 0, Math.PI * 2);
                ctx.stroke();
                
                // Shield rotation
                ctx.rotate(ship.animation * 0.5);
                ctx.beginPath();
                for (let i = 0; i < 6; i++) {
                    const angle = (i / 6) * Math.PI * 2;
                    const x = Math.cos(angle) * ship.width * 0.7;
                    const y = Math.sin(angle) * ship.width * 0.7;
                    if (i === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                }
                ctx.closePath();
                ctx.stroke();
                ctx.rotate(-ship.animation * 0.5);
            }
            
            // Ship body
            const shipGradient = ctx.createLinearGradient(-ship.width/2, 0, ship.width/2, 0);
            shipGradient.addColorStop(0, '#003d82');
            shipGradient.addColorStop(0.5, '#0066cc');
            shipGradient.addColorStop(1, '#0099ff');
            
            ctx.fillStyle = shipGradient;
            ctx.strokeStyle = turboMode ? '#ffee00' : '#00ffcc';
            ctx.lineWidth = 2;
            ctx.shadowColor = turboMode ? '#ffee00' : '#00ffcc';
            ctx.shadowBlur = 15;
            
            // Main hull
            ctx.beginPath();
            ctx.moveTo(-ship.width/2, 0);
            ctx.quadraticCurveTo(-ship.width/4, -ship.height/2, ship.width/3, -ship.height/3);
            ctx.lineTo(ship.width/2, 0);
            ctx.lineTo(ship.width/3, ship.height/3);
            ctx.quadraticCurveTo(-ship.width/4, ship.height/2, -ship.width/2, 0);
            ctx.closePath();
            ctx.fill();
            ctx.stroke();
            
            // Cockpit
            ctx.fillStyle = '#00ffcc';
            ctx.globalAlpha = 0.8;
            ctx.beginPath();
            ctx.ellipse(5, 0, 12, 8, 0, 0, Math.PI * 2);
            ctx.fill();
            
            // Engine glow
            if (boostActive || turboMode) {
                const glowSize = 10 + Math.sin(ship.animation * 3) * 5;
                const engineGradient = ctx.createRadialGradient(
                    -ship.width/2, 0, 0,
                    -ship.width/2, 0, glowSize
                );
                engineGradient.addColorStop(0, turboMode ? '#ffee00' : '#00ffcc');
                engineGradient.addColorStop(1, 'transparent');
                ctx.fillStyle = engineGradient;
                ctx.globalAlpha = 0.8;
                ctx.fillRect(-ship.width/2 - glowSize, -glowSize, glowSize * 2, glowSize * 2);
            }
            
            ctx.restore();
        }
        
        // ==========================================
        // GAME LOGIC
        // ==========================================
        
        function spawnPackets() {
            if (packets.filter(p => p.active).length >= config.maxPackets) return;
            
            // Spawn rate aumenta con difficoltà
            const spawnChance = 0.02 * difficulty;
            if (Math.random() < spawnChance) {
                const packet = packets.find(p => !p.active) || new Packet();
                if (!packets.includes(packet)) packets.push(packet);
                
                // Posizione Y con margini
                const margin = 60;
                const y = margin + Math.random() * (canvas.height - margin * 2);
                
                // Tipo di pacchetto basato su difficoltà
                let type = 'data';
                const virusChance = 0.2 + (difficulty - 1) * 0.05;
                const bonusChance = 0.05;
                
                if (Math.random() < virusChance) {
                    type = 'virus';
                } else if (Math.random() < bonusChance) {
                    type = 'bonus';
                }
                
                // Pattern di movimento per difficoltà alta
                let pattern = null;
                if (difficulty > 3 && Math.random() < 0.3) {
                    pattern = {
                        type: ['sine', 'zigzag'][Math.floor(Math.random() * 2)]
                    };
                }
                
                packet.spawn(y, type, pattern);
            }
            
            // Wave spawning per difficoltà alta
            if (difficulty > 5 && frameCount % 600 === 0) {
                // Spawn wave di virus
                const waveSize = Math.min(5, Math.floor(difficulty / 2));
                for (let i = 0; i < waveSize; i++) {
                    setTimeout(() => {
                        const packet = packets.find(p => !p.active) || new Packet();
                        if (!packets.includes(packet)) packets.push(packet);
                        const y = (canvas.height / (waveSize + 1)) * (i + 1);
                        packet.spawn(y, 'virus');
                    }, i * 200);
                }
                showNotification('VIRUS WAVE INCOMING!', '#ff0033');
            }
        }
        
        function checkCollisions() {
            const shipHitbox = {
                x: ship.x + ship.hitbox.x,
                y: ship.y - ship.hitbox.height/2,
                width: ship.hitbox.width,
                height: ship.hitbox.height
            };
            
            packets.forEach(packet => {
                if (!packet.active) return;
                
                // Collision detection
                if (shipHitbox.x < packet.x + packet.width &&
                    shipHitbox.x + shipHitbox.width > packet.x &&
                    shipHitbox.y < packet.y + packet.height/2 &&
                    shipHitbox.y + shipHitbox.height > packet.y - packet.height/2) {
                    
                    handlePacketCollision(packet);
                }
            });
        }
        
        function handlePacketCollision(packet) {
            packet.reset();
            
            if (packet.type === 'data') {
                // Raccolta pacchetto buono
                score += packet.value * (turboMode ? 2 : 1) * difficulty;
                combo++;
                
                // Power-up effects
                if (packet.subtype === 'power') {
                    powerLevel = Math.min(100, powerLevel + 20);
                    showNotification('POWER BOOST!', '#88e3ff');
                } else if (packet.subtype === 'shield') {
                    activateShield();
                }
                
                // Effetti
                playSound('collect');
                spawnExplosion(packet.x, packet.y, packet.color, 10);
                
                // Combo check
                if (combo === 5) {
                    activateTurboMode();
                } else if (combo === 10) {
                    activateMagnet();
                } else if (combo > 0 && combo % 15 === 0) {
                    spawnBonusWave();
                }
                
                if (combo > maxCombo) maxCombo = combo;
                if (combo > 1) showCombo();
                
            } else if (packet.type === 'virus') {
                // Danno
                if (!shieldActive) {
                    lives--;
                    ship.damaged = true;
                    ship.damageTimer = 30;
                    combo = 0;
                    powerLevel = Math.max(0, powerLevel - 20);
                    
                    playSound('damage');
                    spawnExplosion(ship.x + ship.width/2, ship.y, '#ff0033', 20);
                    screenShake();
                    
                    updateLivesDisplay();
                    
                    if (lives <= 0) {
                        endGame();
                    }
                } else {
                    // Shield absorbe danno
                    shieldTimer = 0;
                    playSound('powerup');
                    spawnExplosion(packet.x, packet.y, '#88e3ff', 15);
                    showNotification('SHIELD BLOCK!', '#88e3ff');
                }
                
            } else if (packet.type === 'bonus') {
                // Bonus speciale
                score += packet.value * difficulty;
                powerLevel = 100;
                lives = Math.min(lives + 1, 5);
                
                playSound('powerup');
                spawnExplosion(packet.x, packet.y, '#00ff88', 25);
                showNotification('MEGA BONUS!', '#00ff88');
                updateLivesDisplay();
            }
            
            updateUI();
        }
        
        function activateTurboMode() {
            turboMode = true;
            turboTimer = 300; // 5 secondi a 60fps
            showNotification('TURBO MODE!', '#ffee00');
            playSound('combo');
        }
        
        function activateMagnet() {
            magnetActive = true;
            magnetTimer = 180; // 3 secondi
            showNotification('MAGNET ACTIVE!', '#ff00ff');
        }
        
        function activateShield() {
            shieldActive = true;
            shieldTimer = 240; // 4 secondi
            shieldBtn.classList.add('disabled');
            showNotification('SHIELD UP!', '#88e3ff');
            playSound('powerup');
        }
        
        function spawnBonusWave() {
            showNotification('BONUS WAVE!', '#00ff88');
            for (let i = 0; i < 3; i++) {
                setTimeout(() => {
                    const packet = packets.find(p => !p.active) || new Packet();
                    if (!packets.includes(packet)) packets.push(packet);
                    const y = 100 + i * 100;
                    packet.spawn(y, 'bonus');
                }, i * 100);
            }
        }
        
        // ==========================================
        // UI FUNCTIONS
        // ==========================================
        
        function updateUI() {
            scoreEl.textContent = Math.floor(score);
            highscoreEl.textContent = highscore;
            powerFill.style.width = powerLevel + '%';
            
            // Power bar color
            if (powerLevel > 66) {
                powerFill.style.background = 'linear-gradient(90deg, #00ff88, #00ffcc)';
            } else if (powerLevel > 33) {
                powerFill.style.background = 'linear-gradient(90deg, #ffee00, #ff9900)';
            } else {
                powerFill.style.background = 'linear-gradient(90deg, #ff0033, #ff6600)';
            }
        }
        
        function updateLivesDisplay() {
            const lifeIcons = livesDisplay.querySelectorAll('.life-icon');
            lifeIcons.forEach((icon, i) => {
                if (i >= lives) {
                    icon.classList.add('lost');
                } else {
                    icon.classList.remove('lost');
                }
            });
        }
        
        function showCombo() {
            comboCountEl.textContent = combo;
            comboDisplay.classList.remove('show');
            void comboDisplay.offsetWidth; // Force reflow
            comboDisplay.classList.add('show');
        }
        
        function showNotification(text, color = '#00ffcc') {
            notification.textContent = text;
            notification.style.color = color;
            notification.style.textShadow = `0 0 30px ${color}`;
            notification.classList.remove('show');
            void notification.offsetWidth;
            notification.classList.add('show');
        }
        
        function screenShake() {
            canvas.style.transform = 'translate(0, 0)';
            const shakeFrames = [
                'translate(5px, 5px)',
                'translate(-5px, -5px)',
                'translate(5px, -5px)',
                'translate(-5px, 5px)',
                'translate(0, 0)'
            ];
            
            shakeFrames.forEach((transform, i) => {
                setTimeout(() => {
                    canvas.style.transform = transform;
                }, i * 50);
            });
        }
        
        // ==========================================
        // INPUT HANDLING
        // ==========================================
        
        function setupInput() {
            // Keyboard
            window.addEventListener('keydown', (e) => {
                keys[e.key] = true;
                
                if (gameState === 'playing') {
                    if (e.key === ' ' && powerLevel >= 50) {
                        activateBoost();
                    } else if ((e.key === 'Shift' || e.key === 's') && powerLevel >= 30) {
                        activateShield();
                    }
                }
            });
            
            window.addEventListener('keyup', (e) => {
                keys[e.key] = false;
            });
            
            // Touch
            canvas.addEventListener('touchstart', handleTouchStart, { passive: false });
            canvas.addEventListener('touchmove', handleTouchMove, { passive: false });
            canvas.addEventListener('touchend', handleTouchEnd, { passive: false });
            
            // Mouse
            canvas.addEventListener('mousedown', (e) => {
                mouse.active = true;
                mouse.y = e.clientY;
            });
            
            canvas.addEventListener('mousemove', (e) => {
                if (mouse.active) {
                    mouse.y = e.clientY;
                }
            });
            
            canvas.addEventListener('mouseup', () => {
                mouse.active = false;
            });
            
            // Touch buttons
            boostBtn.addEventListener('click', () => {
                if (gameState === 'playing' && powerLevel >= 50) {
                    activateBoost();
                }
            });
            
            shieldBtn.addEventListener('click', () => {
                if (gameState === 'playing' && powerLevel >= 30 && !shieldActive) {
                    activateShield();
                }
            });
        }
        
        function handleTouchStart(e) {
            e.preventDefault();
            touches.active = true;
            touches.startY = e.touches[0].clientY;
            touches.currentY = touches.startY;
        }
        
        function handleTouchMove(e) {
            e.preventDefault();
            if (touches.active) {
                touches.currentY = e.touches[0].clientY;
            }
        }
        
        function handleTouchEnd(e) {
            e.preventDefault();
            touches.active = false;
        }
        
        function handleInput() {
            if (gameState !== 'playing') return;
            
            // Calculate target Y
            if (keys['ArrowUp']) {
                ship.targetY -= config.shipSpeed;
            } else if (keys['ArrowDown']) {
                ship.targetY += config.shipSpeed;
            } else if (touches.active) {
                const diff = touches.currentY - touches.startY;
                ship.targetY = ship.y + diff * 0.5;
            } else if (mouse.active) {
                ship.targetY = mouse.y;
            } else {
                // Gradual return to current position
                ship.targetY = ship.y;
            }
            
            ship.targetY = Math.max(30, Math.min(canvas.height - 30, ship.targetY));
        }
        
        function activateBoost() {
            if (boostActive || powerLevel < 50) return;
            
            boostActive = true;
            boostTimer = 120; // 2 secondi
            powerLevel -= 50;
            
            playSound('powerup');
            spawnExplosion(ship.x + ship.width/2, ship.y, '#00ffcc', 20);
            showNotification('BOOST!', '#00ffcc');
            
            boostBtn.classList.add('disabled');
            updateUI();
        }
        
        // ==========================================
        // GAME FLOW
        // ==========================================
        
        function startGame() {
            initAudio();
            gameState = 'playing';
            gameMenu.classList.add('hidden');
            
            // Reset tutto
            score = 0;
            lives = 3;
            combo = 0;
            maxCombo = 0;
            distance = 0;
            difficulty = 1;
            powerLevel = 100;
            frameCount = 0;
            
            // Reset power-ups
            boostActive = false;
            shieldActive = false;
            turboMode = false;
            magnetActive = false;
            
            // Reset ship
            ship.y = canvas.height / 2;
            ship.targetY = ship.y;
            ship.velocity = 0;
            ship.trail = [];
            ship.damaged = false;
            
            // Clear arrays
            packets.forEach(p => p.reset());
            particlePool.forEach(p => p.reset());
            
            // Init
            initBackground();
            updateLivesDisplay();
            updateUI();
            
            totalGames++;
            localStorage.setItem('datastreamTotalGames', totalGames.toString());
            totalGamesEl.textContent = totalGames;
        }
        
        function endGame() {
            gameState = 'gameover';
            
            // Update highscore
            if (score > highscore) {
                highscore = Math.floor(score);
                localStorage.setItem('datastreamHighscore', highscore.toString());
                showNotification('NEW HIGH SCORE!', '#ffee00');
            }
            
            // Show stats
            setTimeout(() => {
                gameMenu.classList.remove('hidden');
                menuHighscoreEl.textContent = highscore;
                
                // Potresti aggiungere più statistiche qui
            }, 1000);
        }
        
        function showTutorial() {
            tutorialOverlay.style.display = 'flex';
        }
        
        function closeTutorial() {
            tutorialOverlay.style.display = 'none';
        }
        
        // ==========================================
        // MAIN GAME LOOP
        // ==========================================
        
        let frameCount = 0;
        let lastTime = 0;
        let fps = 0;
        
        function update(deltaTime) {
            if (gameState !== 'playing') return;
            
            frameCount++;
            distance += config.baseSpeed;
            
            // Difficulty progression
            if (frameCount % 600 === 0) { // Ogni 10 secondi
                difficulty += 0.2;
                config.packetSpawnRate = Math.max(30, config.packetSpawnRate - 2);
                showNotification(`LEVEL ${Math.floor(difficulty)}`, '#00ffcc');
            }
            
            // Handle input
            handleInput();
            
            // Update ship
            updateShip();
            
            // Update timers
            if (boostActive) {
                boostTimer--;
                if (boostTimer <= 0) {
                    boostActive = false;
                    boostBtn.classList.remove('disabled');
                }
            }
            
            if (shieldActive) {
                shieldTimer--;
                if (shieldTimer <= 0) {
                    shieldActive = false;
                    shieldBtn.classList.remove('disabled');
                }
            }
            
            if (turboMode) {
                turboTimer--;
                if (turboTimer <= 0) {
                    turboMode = false;
                }
            }
            
            if (magnetActive) {
                magnetTimer--;
                if (magnetTimer <= 0) {
                    magnetActive = false;
                }
            }
            
            // Power management
            if (!boostActive) {
                powerLevel = Math.min(100, powerLevel + config.powerRegenRate);
            } else {
                powerLevel = Math.max(0, powerLevel - config.powerDrainRate);
            }
            
            // Spawn e update pacchetti
            spawnPackets();
            packets.forEach(packet => packet.update());
            
            // Collisioni
            checkCollisions();
            
            // Update particles
            particlePool.forEach(particle => particle.update());
            
            // Update background
            updateBackground();
            
            // Update UI
            updateUI();
        }
        
        function draw() {
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Draw layers
            drawBackground();
            
            // Draw packets
            packets.forEach(packet => packet.draw(ctx));
            
            // Draw ship
            drawShip();
            
            // Draw particles
            particlePool.forEach(particle => particle.draw(ctx));
            
            // Post-processing effects
            if (turboMode) {
                ctx.save();
                ctx.globalAlpha = 0.1;
                ctx.fillStyle = '#ffee00';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.restore();
            }
            
            // FPS counter (debug)
            if (frameCount % 60 === 0) {
                fps = Math.round(1000 / (performance.now() - lastTime) * 60);
            }
        }
        
        function gameLoop(currentTime) {
            const deltaTime = currentTime - lastTime;
            lastTime = currentTime;
            
            update(deltaTime);
            draw();
            
            requestAnimationFrame(gameLoop);
        }
        
        // ==========================================
        // INITIALIZATION
        // ==========================================
        
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            // Reinit background on resize
            if (backgroundLayers[0].length > 0) {
                initBackground();
            }
        }
        
        function init() {
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
            
            setupInput();
            
            // Load saved data
            highscoreEl.textContent = highscore;
            menuHighscoreEl.textContent = highscore;
            totalGamesEl.textContent = totalGames;
            
            // Initialize particle pool
            for (let i = particlePool.length; i < config.particlePoolSize; i++) {
                particlePool.push(new Particle());
            }
            
            // Start
            requestAnimationFrame(gameLoop);
        }
        
        // Avvia il gioco
        init();
    </script>
</body>
</html>
