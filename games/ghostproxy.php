<?php
// Ghost Proxy - G Tech Arcade
// Gioco stealth in HTML5 Canvas
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ghost Proxy - G Tech Arcade</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #0b0b1e;
            overflow: hidden;
            font-family: 'Courier New', monospace;
            touch-action: none;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        #gameCanvas {
            display: block;
            width: 100vw;
            height: 100vh;
            cursor: crosshair;
        }
        
        /* Controlli mobile - nascosti di default */
        .mobile-controls {
            position: fixed;
            bottom: 20px;
            left: 20px;
            display: none;
            z-index: 1000;
        }
        
        .joystick-container {
            width: 120px;
            height: 120px;
            background: rgba(0, 255, 187, 0.1);
            border: 2px solid #00ffbb;
            border-radius: 50%;
            position: relative;
            touch-action: none;
        }
        
        .joystick-knob {
            width: 40px;
            height: 40px;
            background: #00ffbb;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            box-shadow: 0 0 20px #00ffbb;
        }
        
        .stealth-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 80px;
            height: 80px;
            background: rgba(255, 0, 119, 0.1);
            border: 2px solid #ff0077;
            border-radius: 50%;
            color: #ff0077;
            font-size: 12px;
            font-weight: bold;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            touch-action: none;
            z-index: 1000;
        }
        
        .stealth-button:active {
            background: rgba(255, 0, 119, 0.3);
            transform: scale(0.95);
        }
        
        /* Adattamento per dispositivi mobili */
        @media (pointer: coarse) {
            .mobile-controls,
            .stealth-button {
                display: flex !important;
            }
        }
        
        /* Ottimizzazione per Samsung Fold */
        @media screen and (min-aspect-ratio: 1/1) and (max-aspect-ratio: 1.5/1) {
            .mobile-controls {
                bottom: 10px;
                left: 10px;
            }
            .stealth-button {
                bottom: 10px;
                right: 10px;
            }
        }
    </style>
</head>
<body>
    <canvas id="gameCanvas"></canvas>
    
    <!-- Controlli mobile -->
    <div class="mobile-controls">
        <div class="joystick-container" id="joystick">
            <div class="joystick-knob" id="joystickKnob"></div>
        </div>
    </div>
    
    <div class="stealth-button" id="stealthButton">
        STEALTH
    </div>
    
    <script>
        // === CONFIGURAZIONE INIZIALE ===
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        
        // Variabili per responsive design
        let width, height;
        let scale = 1;
        
        // Stato del gioco
        const gameState = {
            playing: false,
            paused: false,
            gameOver: false,
            level: 1,
            timeLeft: 60,
            score: 0
        };
        
        // === SETUP CANVAS RESPONSIVE ===
        function resizeCanvas() {
            width = window.innerWidth;
            height = window.innerHeight;
            
            canvas.width = width;
            canvas.height = height;
            
            // Calcola scala per mantenere proporzioni su diversi dispositivi
            scale = Math.min(width / 1920, height / 1080);
            
            // Adattamento speciale per Samsung Fold
            const aspectRatio = width / height;
            if (aspectRatio >= 1 && aspectRatio <= 1.5) {
                // Modalità quadrata/quasi quadrata
                scale *= 0.8;
            }
        }
        
        // === SISTEMA DI INPUT ===
        const input = {
            keys: {},
            touch: {
                active: false,
                x: 0,
                y: 0
            },
            joystick: {
                active: false,
                angle: 0,
                force: 0
            }
        };
        
        // Input tastiera
        window.addEventListener('keydown', (e) => {
            input.keys[e.key.toLowerCase()] = true;
            e.preventDefault();
        });
        
        window.addEventListener('keyup', (e) => {
            input.keys[e.key.toLowerCase()] = false;
            e.preventDefault();
        });
        
        // Setup joystick mobile
        const joystickElement = document.getElementById('joystick');
        const joystickKnob = document.getElementById('joystickKnob');
        const stealthButton = document.getElementById('stealthButton');
        
        if (joystickElement) {
            let joystickCenter = { x: 60, y: 60 };
            
            function handleJoystickMove(clientX, clientY) {
                const rect = joystickElement.getBoundingClientRect();
                const x = clientX - rect.left - joystickCenter.x;
                const y = clientY - rect.top - joystickCenter.y;
                
                const distance = Math.sqrt(x * x + y * y);
                const maxDistance = 50;
                
                if (distance > 0) {
                    input.joystick.angle = Math.atan2(y, x);
                    input.joystick.force = Math.min(distance / maxDistance, 1);
                    
                    const limitedDistance = Math.min(distance, maxDistance);
                    const limitedX = (x / distance) * limitedDistance;
                    const limitedY = (y / distance) * limitedDistance;
                    
                    joystickKnob.style.transform = 
                        `translate(calc(-50% + ${limitedX}px), calc(-50% + ${limitedY}px))`;
                }
            }
            
            joystickElement.addEventListener('touchstart', (e) => {
                input.joystick.active = true;
                const touch = e.touches[0];
                handleJoystickMove(touch.clientX, touch.clientY);
                e.preventDefault();
            });
            
            joystickElement.addEventListener('touchmove', (e) => {
                const touch = e.touches[0];
                handleJoystickMove(touch.clientX, touch.clientY);
                e.preventDefault();
            });
            
            joystickElement.addEventListener('touchend', () => {
                input.joystick.active = false;
                input.joystick.force = 0;
                joystickKnob.style.transform = 'translate(-50%, -50%)';
            });
        }
        
        // === GRIGLIA DI NODI ===
        class NetworkNode {
            constructor(x, y, id) {
                this.x = x;
                this.y = y;
                this.id = id;
                this.connected = []; // ID dei nodi connessi
                this.isExit = false;
                this.isStart = false;
            }
            
            draw() {
                // Disegna connessioni
                ctx.strokeStyle = 'rgba(0, 255, 187, 0.2)';
                ctx.lineWidth = 2 * scale;
                
                this.connected.forEach(nodeId => {
                    const targetNode = nodes.find(n => n.id === nodeId);
                    if (targetNode) {
                        ctx.beginPath();
                        ctx.moveTo(this.x, this.y);
                        ctx.lineTo(targetNode.x, targetNode.y);
                        ctx.stroke();
                    }
                });
                
                // Disegna nodo
                ctx.beginPath();
                ctx.arc(this.x, this.y, 15 * scale, 0, Math.PI * 2);
                
                if (this.isExit) {
                    ctx.fillStyle = '#00ff00';
                    ctx.fill();
                    ctx.strokeStyle = '#00ff00';
                    ctx.stroke();
                    
                    // Animazione pulsante per l'uscita
                    const pulse = Math.sin(Date.now() * 0.003) * 0.3 + 0.7;
                    ctx.globalAlpha = pulse;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, 25 * scale, 0, Math.PI * 2);
                    ctx.strokeStyle = '#00ff00';
                    ctx.stroke();
                    ctx.globalAlpha = 1;
                } else if (this.isStart) {
                    ctx.fillStyle = '#ffff00';
                    ctx.fill();
                    ctx.strokeStyle = '#ffff00';
                    ctx.stroke();
                } else {
                    ctx.fillStyle = 'rgba(0, 255, 187, 0.1)';
                    ctx.fill();
                    ctx.strokeStyle = '#00ffbb';
                    ctx.stroke();
                }
            }
        }
        
        // === PROXY (GIOCATORE) ===
        class GhostProxy {
            constructor(node) {
                this.currentNode = node;
                this.x = node.x;
                this.y = node.y;
                this.targetX = node.x;
                this.targetY = node.y;
                this.moving = false;
                this.speed = 5 * scale;
                this.radius = 10 * scale;
                this.opacity = 0.8;
                this.trail = []; // Scia del movimento
                this.stealthActive = false;
                this.stealthCooldown = 0;
                this.stealthDuration = 3000; // 3 secondi
                this.stealthMaxCooldown = 5000; // 5 secondi
            }
            
            moveTo(targetNode) {
                if (!this.moving && this.currentNode.connected.includes(targetNode.id)) {
                    this.currentNode = targetNode;
                    this.targetX = targetNode.x;
                    this.targetY = targetNode.y;
                    this.moving = true;
                    return true;
                }
                return false;
            }
            
            update(deltaTime) {
                // Aggiorna posizione
                if (this.moving) {
                    const dx = this.targetX - this.x;
                    const dy = this.targetY - this.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    
                    if (distance < this.speed) {
                        this.x = this.targetX;
                        this.y = this.targetY;
                        this.moving = false;
                        
                        // Aggiungi alla scia
                        this.trail.push({ x: this.x, y: this.y, opacity: 1 });
                        if (this.trail.length > 10) {
                            this.trail.shift();
                        }
                    } else {
                        this.x += (dx / distance) * this.speed;
                        this.y += (dy / distance) * this.speed;
                    }
                }
                
                // Aggiorna scia
                this.trail.forEach(point => {
                    point.opacity -= deltaTime * 0.002;
                });
                this.trail = this.trail.filter(point => point.opacity > 0);
                
                // Gestione stealth
                if (this.stealthCooldown > 0) {
                    this.stealthCooldown -= deltaTime;
                }
                
                if (this.stealthActive) {
                    this.stealthDuration -= deltaTime;
                    if (this.stealthDuration <= 0) {
                        this.deactivateStealth();
                    }
                }
            }
            
            activateStealth() {
                if (this.stealthCooldown <= 0 && !this.stealthActive) {
                    this.stealthActive = true;
                    this.stealthDuration = 3000;
                    this.stealthCooldown = this.stealthMaxCooldown;
                }
            }
            
            deactivateStealth() {
                this.stealthActive = false;
                this.stealthDuration = 0;
            }
            
            draw() {
                // Disegna scia
                ctx.strokeStyle = this.stealthActive ? '#ff0077' : '#00ffbb';
                this.trail.forEach((point, index) => {
                    if (index > 0) {
                        ctx.globalAlpha = point.opacity * 0.3;
                        ctx.lineWidth = 3 * scale;
                        ctx.beginPath();
                        ctx.moveTo(this.trail[index - 1].x, this.trail[index - 1].y);
                        ctx.lineTo(point.x, point.y);
                        ctx.stroke();
                    }
                });
                ctx.globalAlpha = 1;
                
                // Disegna proxy
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                
                if (this.stealthActive) {
                    // Modalità stealth - semitrasparente con effetto glitch
                    const glitch = Math.random() > 0.95 ? 0.2 : 0;
                    ctx.fillStyle = `rgba(255, 0, 119, ${0.3 + glitch})`;
                    ctx.strokeStyle = '#ff0077';
                    
                    // Effetto distorsione
                    for (let i = 0; i < 3; i++) {
                        ctx.globalAlpha = 0.2;
                        ctx.beginPath();
                        ctx.arc(
                            this.x + (Math.random() - 0.5) * 10 * scale,
                            this.y + (Math.random() - 0.5) * 10 * scale,
                            this.radius,
                            0,
                            Math.PI * 2
                        );
                        ctx.stroke();
                    }
                    ctx.globalAlpha = 1;
                } else {
                    // Modalità normale
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
                    ctx.strokeStyle = '#ffffff';
                }
                
                ctx.fill();
                ctx.stroke();
                
                // Indicatore cooldown stealth
                if (this.stealthCooldown > 0 && !this.stealthActive) {
                    const cooldownPercent = this.stealthCooldown / this.stealthMaxCooldown;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.radius + 5 * scale, 
                            -Math.PI / 2, 
                            -Math.PI / 2 + (1 - cooldownPercent) * Math.PI * 2);
                    ctx.strokeStyle = 'rgba(255, 0, 119, 0.5)';
                    ctx.lineWidth = 2 * scale;
                    ctx.stroke();
                }
            }
        }
        
        // === NEMICI - SCANNER LASER ===
        class LaserScanner {
            constructor(node) {
                this.node = node;
                this.x = node.x;
                this.y = node.y;
                this.angle = Math.random() * Math.PI * 2;
                this.rotationSpeed = 0.001 + Math.random() * 0.002;
                this.range = 150 * scale;
                this.scanAngle = Math.PI / 4; // 45 gradi di apertura
                this.color = '#ff0077';
                this.detected = false;
            }
            
            update(deltaTime) {
                // Rotazione continua
                this.angle += this.rotationSpeed * deltaTime;
                if (this.angle > Math.PI * 2) {
                    this.angle -= Math.PI * 2;
                }
                
                // Controlla se il proxy è nel cono visivo
                if (proxy && !proxy.stealthActive) {
                    this.detected = this.checkDetection(proxy.x, proxy.y);
                    if (this.detected) {
                        gameState.gameOver = true;
                    }
                }
            }
            
            checkDetection(targetX, targetY) {
                const dx = targetX - this.x;
                const dy = targetY - this.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                // Fuori dal range
                if (distance > this.range) return false;
                
                // Calcola angolo verso il target
                let angleToTarget = Math.atan2(dy, dx);
                
                // Normalizza gli angoli
                let angleDiff = angleToTarget - this.angle;
                while (angleDiff > Math.PI) angleDiff -= Math.PI * 2;
                while (angleDiff < -Math.PI) angleDiff += Math.PI * 2;
                
                // Controlla se è nel cono visivo
                return Math.abs(angleDiff) < this.scanAngle / 2;
            }
            
            draw() {
                // Disegna base scanner
                ctx.beginPath();
                ctx.arc(this.x, this.y, 10 * scale, 0, Math.PI * 2);
                ctx.fillStyle = this.detected ? '#ff0000' : '#ff0077';
                ctx.fill();
                ctx.strokeStyle = this.detected ? '#ff0000' : '#ff0077';
                ctx.lineWidth = 2 * scale;
                ctx.stroke();
                
                // Disegna cono visivo
                ctx.save();
                ctx.translate(this.x, this.y);
                ctx.rotate(this.angle);
                
                // Effetto gradiente per il cono
                const gradient = ctx.createRadialGradient(0, 0, 0, 0, 0, this.range);
                gradient.addColorStop(0, this.detected ? 'rgba(255, 0, 0, 0.4)' : 'rgba(255, 0, 119, 0.4)');
                gradient.addColorStop(1, 'rgba(255, 0, 119, 0)');
                
                ctx.beginPath();
                ctx.moveTo(0, 0);
                ctx.arc(0, 0, this.range, -this.scanAngle / 2, this.scanAngle / 2);
                ctx.closePath();
                ctx.fillStyle = gradient;
                ctx.fill();
                
                // Bordi del cono
                ctx.strokeStyle = this.detected ? '#ff0000' : '#ff0077';
                ctx.lineWidth = 2 * scale;
                ctx.beginPath();
                ctx.moveTo(0, 0);
                ctx.lineTo(
                    Math.cos(-this.scanAngle / 2) * this.range,
                    Math.sin(-this.scanAngle / 2) * this.range
                );
                ctx.moveTo(0, 0);
                ctx.lineTo(
                    Math.cos(this.scanAngle / 2) * this.range,
                    Math.sin(this.scanAngle / 2) * this.range
                );
                ctx.stroke();
                
                // Linee di scansione
                const scanLines = 5;
                ctx.strokeStyle = this.detected ? 'rgba(255, 0, 0, 0.3)' : 'rgba(255, 0, 119, 0.3)';
                ctx.lineWidth = 1 * scale;
                for (let i = 0; i < scanLines; i++) {
                    const lineAngle = -this.scanAngle / 2 + (this.scanAngle / scanLines) * i;
                    const offset = (Date.now() * 0.001 + i) % 1;
                    
                    ctx.beginPath();
                    ctx.moveTo(
                        Math.cos(lineAngle) * this.range * offset,
                        Math.sin(lineAngle) * this.range * offset
                    );
                    ctx.lineTo(
                        Math.cos(lineAngle) * this.range * (offset + 0.1),
                        Math.sin(lineAngle) * this.range * (offset + 0.1)
                    );
                    ctx.stroke();
                }
                
                ctx.restore();
            }
        }
        
        // === NEMICI - BOT DPI ===
        class DPIBot {
            constructor(patrolNodes) {
                this.patrolNodes = patrolNodes;
                this.currentPatrolIndex = 0;
                this.currentNode = patrolNodes[0];
                this.x = this.currentNode.x;
                this.y = this.currentNode.y;
                this.targetX = this.x;
                this.targetY = this.y;
                this.speed = 2 * scale;
                this.detectionRadius = 80 * scale;
                this.state = 'patrol'; // patrol, alert, tracking
                this.alertTimer = 0;
                this.lastKnownPosition = null;
            }
            
            update(deltaTime) {
                // Movimento
                const dx = this.targetX - this.x;
                const dy = this.targetY - this.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance > this.speed) {
                    this.x += (dx / distance) * this.speed;
                    this.y += (dy / distance) * this.speed;
                } else {
                    // Raggiunto il target
                    this.x = this.targetX;
                    this.y = this.targetY;
                    
                    if (this.state === 'patrol') {
                        // Prossimo nodo di pattuglia
                        this.currentPatrolIndex = (this.currentPatrolIndex + 1) % this.patrolNodes.length;
                        this.currentNode = this.patrolNodes[this.currentPatrolIndex];
                        this.targetX = this.currentNode.x;
                        this.targetY = this.currentNode.y;
                    }
                }
                
                // Rilevamento proxy
                if (proxy && !proxy.stealthActive) {
                    const proxyDist = Math.sqrt(
                        Math.pow(proxy.x - this.x, 2) + 
                        Math.pow(proxy.y - this.y, 2)
                    );
                    
                    if (proxyDist < this.detectionRadius) {
                        this.state = 'alert';
                        this.alertTimer = 2000; // 2 secondi di allerta
                        this.lastKnownPosition = { x: proxy.x, y: proxy.y };
                        gameState.gameOver = true;
                    }
                }
                
                // Gestione stati
                if (this.state === 'alert') {
                    this.alertTimer -= deltaTime;
                    if (this.alertTimer <= 0) {
                        this.state = 'patrol';
                        this.targetX = this.currentNode.x;
                        this.targetY = this.currentNode.y;
                    } else if (this.lastKnownPosition) {
                        // Muovi verso l'ultima posizione conosciuta
                        this.targetX = this.lastKnownPosition.x;
                        this.targetY = this.lastKnownPosition.y;
                    }
                }
            }
            
            draw() {
                // Area di rilevamento
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.detectionRadius, 0, Math.PI * 2);
                const gradient = ctx.createRadialGradient(this.x, this.y, 0, this.x, this.y, this.detectionRadius);
                
                if (this.state === 'alert') {
                    gradient.addColorStop(0, 'rgba(255, 0, 0, 0.3)');
                    gradient.addColorStop(1, 'rgba(255, 0, 0, 0)');
                } else {
                    gradient.addColorStop(0, 'rgba(0, 255, 187, 0.2)');
                    gradient.addColorStop(1, 'rgba(0, 255, 187, 0)');
                }
                
                ctx.fillStyle = gradient;
                ctx.fill();
                
                // Corpo del bot (quadrato)
                ctx.save();
                ctx.translate(this.x, this.y);
                
                // Animazione rotazione quando in allerta
                if (this.state === 'alert') {
                    ctx.rotate(Date.now() * 0.005);
                }
                
                const botSize = 15 * scale;
                ctx.fillStyle = this.state === 'alert' ? '#ff0000' : '#00ffbb';
                ctx.fillRect(-botSize/2, -botSize/2, botSize, botSize);
                
                ctx.strokeStyle = this.state === 'alert' ? '#ff0000' : '#00ffbb';
                ctx.lineWidth = 2 * scale;
                ctx.strokeRect(-botSize/2, -botSize/2, botSize, botSize);
                
                // Occhio del bot
                ctx.beginPath();
                ctx.arc(0, 0, 5 * scale, 0, Math.PI * 2);
                ctx.fillStyle = this.state === 'alert' ? '#ffffff' : '#0b0b1e';
                ctx.fill();
                
                ctx.restore();
                
                // Linea di pattuglia
                if (this.state === 'patrol' && this.patrolNodes.length > 1) {
                    ctx.strokeStyle = 'rgba(0, 255, 187, 0.1)';
                    ctx.lineWidth = 1 * scale;
                    ctx.setLineDash([5, 5]);
                    
                    ctx.beginPath();
                    for (let i = 0; i < this.patrolNodes.length; i++) {
                        const node = this.patrolNodes[i];
                        if (i === 0) {
                            ctx.moveTo(node.x, node.y);
                        } else {
                            ctx.lineTo(node.x, node.y);
                        }
                    }
                    ctx.closePath();
                    ctx.stroke();
                    ctx.setLineDash([]);
                }
            }
        }
        
        // === GENERAZIONE LIVELLO (AGGIORNATA) ===
        let nodes = [];
        let proxy = null;
        let enemies = [];
        
        function generateLevel(level) {
            nodes = [];
            enemies = [];
            const gridSize = Math.min(6 + Math.floor(level / 2), 10);
            const nodeSpacing = Math.min(width, height) / (gridSize + 1);
            const offsetX = (width - nodeSpacing * (gridSize - 1)) / 2;
            const offsetY = (height - nodeSpacing * (gridSize - 1)) / 2;
            
            // Crea nodi
            let nodeId = 0;
            for (let y = 0; y < gridSize; y++) {
                for (let x = 0; x < gridSize; x++) {
                    // Alcuni nodi potrebbero mancare per creare un labirinto
                    if (Math.random() > 0.2 || (x === 0 && y === 0) || (x === gridSize - 1 && y === gridSize - 1)) {
                        const node = new NetworkNode(
                            offsetX + x * nodeSpacing,
                            offsetY + y * nodeSpacing,
                            nodeId++
                        );
                        
                        // Imposta start e exit
                        if (x === 0 && y === 0) {
                            node.isStart = true;
                        } else if (x === gridSize - 1 && y === gridSize - 1) {
                            node.isExit = true;
                        }
                        
                        nodes.push(node);
                    }
                }
            }
            
            // Crea connessioni
            nodes.forEach(node => {
                nodes.forEach(otherNode => {
                    if (node.id !== otherNode.id) {
                        const distance = Math.sqrt(
                            Math.pow(node.x - otherNode.x, 2) + 
                            Math.pow(node.y - otherNode.y, 2)
                        );
                        
                        // Connetti nodi vicini
                        if (distance < nodeSpacing * 1.5 && Math.random() > 0.3) {
                            if (!node.connected.includes(otherNode.id)) {
                                node.connected.push(otherNode.id);
                                otherNode.connected.push(node.id);
                            }
                        }
                    }
                });
            });
            
            // Assicura che ci sia sempre un percorso
            ensurePathExists();
            
            // Crea proxy al nodo di start
            const startNode = nodes.find(n => n.isStart);
            proxy = new GhostProxy(startNode);
            
            // Aggiungi nemici basati sul livello
            const numScanners = Math.min(1 + Math.floor(level / 2), 5);
            const numBots = Math.min(Math.floor(level / 3), 3);
            
            // Posiziona scanner laser
            const availableNodes = nodes.filter(n => !n.isStart && !n.isExit);
            for (let i = 0; i < numScanners && i < availableNodes.length; i++) {
                const randomNode = availableNodes.splice(
                    Math.floor(Math.random() * availableNodes.length), 1
                )[0];
                enemies.push(new LaserScanner(randomNode));
            }
            
            // Crea bot DPI con percorsi di pattuglia
            for (let i = 0; i < numBots; i++) {
                const patrolSize = 3 + Math.floor(Math.random() * 3);
                const patrolNodes = [];
                
                // Seleziona nodi casuali per il percorso di pattuglia
                for (let j = 0; j < patrolSize; j++) {
                    const availableForPatrol = nodes.filter(n => 
                        !n.isStart && !n.isExit && 
                        !patrolNodes.includes(n)
                    );
                    
                    if (availableForPatrol.length > 0) {
                        patrolNodes.push(
                            availableForPatrol[Math.floor(Math.random() * availableForPatrol.length)]
                        );
                    }
                }
                
                if (patrolNodes.length >= 2) {
                    enemies.push(new DPIBot(patrolNodes));
                }
            }
        }
        
        function ensurePathExists() {
            // Algoritmo semplice per garantire che esista almeno un percorso
            const startNode = nodes.find(n => n.isStart);
            const exitNode = nodes.find(n => n.isExit);
            
            if (!startNode || !exitNode) return;
            
            // Se non ci sono abbastanza connessioni, aggiungi un percorso diretto
            if (startNode.connected.length === 0) {
                const nearestNode = nodes
                    .filter(n => n.id !== startNode.id)
                    .sort((a, b) => {
                        const distA = Math.sqrt(Math.pow(a.x - startNode.x, 2) + Math.pow(a.y - startNode.y, 2));
                        const distB = Math.sqrt(Math.pow(b.x - startNode.x, 2) + Math.pow(b.y - startNode.y, 2));
                        return distA - distB;
                    })[0];
                
                if (nearestNode) {
                    startNode.connected.push(nearestNode.id);
                    nearestNode.connected.push(startNode.id);
                }
            }
        }
        
        // === GAME LOOP ===
        let lastTime = 0;
        
        function update(deltaTime) {
            if (!gameState.playing || gameState.gameOver) return;
            
            // Aggiorna timer
            gameState.timeLeft -= deltaTime / 1000;
            if (gameState.timeLeft <= 0) {
                gameState.gameOver = true;
            }
            
            // Aggiorna proxy
            if (proxy) {
                proxy.update(deltaTime);
                
                // Controlla vittoria
                const currentNode = nodes.find(n => n.id === proxy.currentNode.id);
                if (currentNode && currentNode.isExit && !proxy.moving) {
                    levelComplete();
                }
            }
            
            // Aggiorna nemici
            enemies.forEach(enemy => enemy.update(deltaTime));
            
            // Gestione input
            handleInput();
        }
        
        function levelComplete() {
            gameState.playing = false;
            gameState.score += Math.floor(gameState.timeLeft * 10);
            
            // Prossimo livello dopo una pausa
            setTimeout(() => {
                gameState.level++;
                gameState.timeLeft = 60;
                generateLevel(gameState.level);
                gameState.playing = true;
                gameState.gameOver = false;
            }, 2000);
        }
        
        function handleInput() {
            if (!proxy || proxy.moving) return;
            
            let targetNode = null;
            const currentNode = proxy.currentNode;
            
            // Input tastiera
            if (input.keys['arrowup'] || input.keys['w']) {
                targetNode = findNodeInDirection(currentNode, 0, -1);
            } else if (input.keys['arrowdown'] || input.keys['s']) {
                targetNode = findNodeInDirection(currentNode, 0, 1);
            } else if (input.keys['arrowleft'] || input.keys['a']) {
                targetNode = findNodeInDirection(currentNode, -1, 0);
            } else if (input.keys['arrowright'] || input.keys['d']) {
                targetNode = findNodeInDirection(currentNode, 1, 0);
            }
            
            // Input joystick
            if (input.joystick.active && input.joystick.force > 0.3) {
                const angle = input.joystick.angle;
                const dx = Math.cos(angle);
                const dy = Math.sin(angle);
                
                if (Math.abs(dx) > Math.abs(dy)) {
                    targetNode = findNodeInDirection(currentNode, dx > 0 ? 1 : -1, 0);
                } else {
                    targetNode = findNodeInDirection(currentNode, 0, dy > 0 ? 1 : -1);
                }
            }
            
            // Attiva stealth
            if (input.keys['shift'] || stealthButton.dataset.pressed === 'true') {
                proxy.activateStealth();
            }
            
            // Muovi verso il nodo target
            if (targetNode) {
                proxy.moveTo(targetNode);
            }
        }
        
        function findNodeInDirection(fromNode, dx, dy) {
            let bestNode = null;
            let bestScore = Infinity;
            
            fromNode.connected.forEach(nodeId => {
                const node = nodes.find(n => n.id === nodeId);
                if (node) {
                    const nodeDx = node.x - fromNode.x;
                    const nodeDy = node.y - fromNode.y;
                    const score = Math.abs(nodeDx - dx * 100) + Math.abs(nodeDy - dy * 100);
                    
                    if (score < bestScore) {
                        bestScore = score;
                        bestNode = node;
                    }
                }
            });
            
            return bestNode;
        }
        
        function render() {
            // Pulisci canvas
            ctx.fillStyle = '#0b0b1e';
            ctx.fillRect(0, 0, width, height);
            
            // Effetto griglia di sfondo
            ctx.strokeStyle = 'rgba(0, 255, 187, 0.05)';
            ctx.lineWidth = 1;
            const gridSize = 50 * scale;
            
            for (let x = 0; x < width; x += gridSize) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, height);
                ctx.stroke();
            }
            
            for (let y = 0; y < height; y += gridSize) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(width, y);
                ctx.stroke();
            }
            
            // Disegna nodi
            nodes.forEach(node => node.draw());
            
            // Disegna nemici (prima del proxy per sovrapposizione corretta)
            enemies.forEach(enemy => enemy.draw());
            
            // Disegna proxy
            if (proxy) {
                proxy.draw();
            }
            
            // UI
            drawUI();
            
            // Schermata di game over
            if (gameState.gameOver) {
                drawGameOver();
            }
            
            // Messaggio livello completato
            if (!gameState.playing && !gameState.gameOver) {
                drawLevelComplete();
            }
        }
        
        function drawUI() {
            // Timer
            ctx.fillStyle = gameState.timeLeft < 10 ? '#ff0077' : '#00ffbb';
            ctx.font = `${20 * scale}px Courier New`;
            ctx.textAlign = 'left';
            ctx.fillText(`TIME: ${Math.ceil(gameState.timeLeft)}s`, 20 * scale, 30 * scale);
            
            // Livello
            ctx.fillStyle = '#00ffbb';
            ctx.textAlign = 'right';
            ctx.fillText(`LEVEL: ${gameState.level}`, width - 20 * scale, 30 * scale);
            
            // Score
            ctx.fillText(`SCORE: ${gameState.score}`, width - 20 * scale, 60 * scale);
            
            // Indicatore stealth
            if (proxy) {
                ctx.textAlign = 'center';
                if (proxy.stealthActive) {
                    ctx.fillStyle = '#ff0077';
                    const stealthTime = Math.ceil(proxy.stealthDuration / 1000);
                    ctx.fillText(`STEALTH: ${stealthTime}s`, width / 2, 30 * scale);
                } else if (proxy.stealthCooldown > 0) {
                    ctx.fillStyle = 'rgba(255, 0, 119, 0.5)';
                    const cooldownSec = Math.ceil(proxy.stealthCooldown / 1000);
                    ctx.fillText(`COOLDOWN: ${cooldownSec}s`, width / 2, 30 * scale);
                }
            }
        }
        
        function drawGameOver() {
            // Overlay scuro
            ctx.fillStyle = 'rgba(11, 11, 30, 0.8)';
            ctx.fillRect(0, 0, width, height);
            
            // Testo game over
            ctx.fillStyle = '#ff0077';
            ctx.font = `${60 * scale}px Courier New`;
            ctx.textAlign = 'center';
            ctx.fillText('TRACCIATO!', width / 2, height / 2 - 50 * scale);
            
            ctx.fillStyle = '#00ffbb';
            ctx.font = `${30 * scale}px Courier New`;
            ctx.fillText('CONNESSIONE TERMINATA', width / 2, height / 2);
            
            ctx.font = `${20 * scale}px Courier New`;
            ctx.fillText(`SCORE: ${gameState.score}`, width / 2, height / 2 + 50 * scale);
            
            ctx.fillStyle = 'rgba(0, 255, 187, 0.8)';
            ctx.fillText('Premi R per riprovare', width / 2, height / 2 + 100 * scale);
        }
        
        function drawLevelComplete() {
            ctx.fillStyle = '#00ff00';
            ctx.font = `${40 * scale}px Courier New`;
            ctx.textAlign = 'center';
            ctx.fillText('NODO SICURO RAGGIUNTO!', width / 2, height / 2);
            
            ctx.font = `${20 * scale}px Courier New`;
            ctx.fillText(`Bonus tempo: +${Math.floor(gameState.timeLeft * 10)} punti`, width / 2, height / 2 + 40 * scale);
        }
        
        function gameLoop(currentTime) {
            const deltaTime = currentTime - lastTime;
            lastTime = currentTime;
            
            update(deltaTime);
            render();
            
            requestAnimationFrame(gameLoop);
        }
        
        // === RESTART GAME ===
        window.addEventListener('keydown', (e) => {
            if (e.key.toLowerCase() === 'r' && gameState.gameOver) {
                gameState.level = 1;
                gameState.score = 0;
                gameState.timeLeft = 60;
                gameState.gameOver = false;
                gameState.playing = true;
                generateLevel(1);
            }
        });
        
        // === GESTIONE BOTTONE STEALTH ===
        stealthButton.addEventListener('touchstart', () => {
            stealthButton.dataset.pressed = 'true';
        });
        
        stealthButton.addEventListener('touchend', () => {
            stealthButton.dataset.pressed = 'false';
        });
        
        // === INIZIALIZZAZIONE ===
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
        
        // Genera primo livello
        generateLevel(1);
        gameState.playing = true;
        
        // Avvia game loop
        requestAnimationFrame(gameLoop);
    </script>
</body>
</html>
