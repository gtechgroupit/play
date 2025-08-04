<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>G Tech Arcade - Sala Giochi Retro</title>
    <meta name="description" content="Benvenuti nella sala giochi G Tech Arcade! 9 giochi HTML5 originali in stile retro arcade.">
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/arcade-style.css">
    
    <!-- Font Arcade -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Effetto scanlines CRT -->
    <div class="scanlines"></div>
    
    <!-- Header con logo e titolo -->
    <header class="arcade-header">
        <div class="neon-sign">
            <img src="assets/logo.png" alt="G Tech Group" class="logo-glow">
            <h1 class="arcade-title">
                <span class="letter">G</span>
                <span class="letter"> </span>
                <span class="letter">T</span>
                <span class="letter">E</span>
                <span class="letter">C</span>
                <span class="letter">H</span>
                <span class="letter"> </span>
                <span class="letter">A</span>
                <span class="letter">R</span>
                <span class="letter">C</span>
                <span class="letter">A</span>
                <span class="letter">D</span>
                <span class="letter">E</span>
            </h1>
        </div>
        <div class="subtitle-marquee">
            <span>★ INSERISCI MONETA ★ 9 GIOCHI DISPONIBILI ★ HIGH SCORE CHALLENGE ★</span>
        </div>
    </header>

    <!-- Container principale -->
    <main class="arcade-floor">
        <!-- Griglia giochi 3x3 -->
        <div class="games-grid">
            <!-- Firewall Defender -->
            <div class="arcade-cabinet" data-game="firewall-defender">
                <div class="screen-frame">
                    <div class="screen-content">
                        <div class="game-preview"></div>
                        <div class="game-info">
                            <h3>Firewall Defender</h3>
                            <p>Proteggi il server dai malware!</p>
                        </div>
                    </div>
                    <div class="screen-glare"></div>
                </div>
                <div class="cabinet-controls">
                    <button class="play-button" onclick="location.href='games/firewall-defender.php'">
                        <span>▶ GIOCA</span>
                    </button>
                    <div class="coin-slot">INSERT COIN</div>
                </div>
            </div>

            <!-- Eraldin -->
            <div class="arcade-cabinet" data-game="eraldin">
                <div class="screen-frame">
                    <div class="screen-content">
                        <div class="game-preview"></div>
                        <div class="game-info">
                            <h3>Eraldin</h3>
                            <p>RPG cyberpunk fantasy</p>
                        </div>
                    </div>
                    <div class="screen-glare"></div>
                </div>
                <div class="cabinet-controls">
                    <button class="play-button" onclick="location.href='games/eraldin.php'">
                        <span>▶ GIOCA</span>
                    </button>
                    <div class="coin-slot">INSERT COIN</div>
                </div>
            </div>

            <!-- Hedgehog Rush -->
            <div class="arcade-cabinet" data-game="hedgehog-rush">
                <div class="screen-frame">
                    <div class="screen-content">
                        <div class="game-preview"></div>
                        <div class="game-info">
                            <h3>Hedgehog Rush</h3>
                            <p>Platform ad alta velocità</p>
                        </div>
                    </div>
                    <div class="screen-glare"></div>
                </div>
                <div class="cabinet-controls">
                    <button class="play-button" onclick="location.href='games/hedgehog-rush.php'">
                        <span>▶ GIOCA</span>
                    </button>
                    <div class="coin-slot">INSERT COIN</div>
                </div>
            </div>

            <!-- Cyberscape -->
            <div class="arcade-cabinet" data-game="cyberscape">
                <div class="screen-frame">
                    <div class="screen-content">
                        <div class="game-preview"></div>
                        <div class="game-info">
                            <h3>Cyberscape</h3>
                            <p>Runner nel cyberspazio</p>
                        </div>
                    </div>
                    <div class="screen-glare"></div>
                </div>
                <div class="cabinet-controls">
                    <button class="play-button" onclick="location.href='games/cyberscape.php'">
                        <span>▶ GIOCA</span>
                    </button>
                    <div class="coin-slot">INSERT COIN</div>
                </div>
            </div>

            <!-- Packet Runner -->
            <div class="arcade-cabinet" data-game="packet-runner">
                <div class="screen-frame">
                    <div class="screen-content">
                        <div class="game-preview"></div>
                        <div class="game-info">
                            <h3>Packet Runner</h3>
                            <p>Naviga la rete TCP/IP</p>
                        </div>
                    </div>
                    <div class="screen-glare"></div>
                </div>
                <div class="cabinet-controls">
                    <button class="play-button" onclick="location.href='games/packet-runner.php'">
                        <span>▶ GIOCA</span>
                    </button>
                    <div class="coin-slot">INSERT COIN</div>
                </div>
            </div>

            <!-- Ghost Proxy -->
            <div class="arcade-cabinet" data-game="ghost-proxy">
                <div class="screen-frame">
                    <div class="screen-content">
                        <div class="game-preview"></div>
                        <div class="game-info">
                            <h3>Ghost Proxy</h3>
                            <p>Stealth hacking game</p>
                        </div>
                    </div>
                    <div class="screen-glare"></div>
                </div>
                <div class="cabinet-controls">
                    <button class="play-button" onclick="location.href='games/ghostproxy.php'">
                        <span>▶ GIOCA</span>
                    </button>
                    <div class="coin-slot">INSERT COIN</div>
                </div>
            </div>

            <!-- Datastream Dash -->
            <div class="arcade-cabinet" data-game="datastream-dash">
                <div class="screen-frame">
                    <div class="screen-content">
                        <div class="game-preview"></div>
                        <div class="game-info">
                            <h3>Datastream Dash</h3>
                            <p>Gestisci il flusso dati</p>
                        </div>
                    </div>
                    <div class="screen-glare"></div>
                </div>
                <div class="cabinet-controls">
                    <button class="play-button" onclick="location.href='games/datastream-dash.php'">
                        <span>▶ GIOCA</span>
                    </button>
                    <div class="coin-slot">INSERT COIN</div>
                </div>
            </div>

            <!-- Code Jumper -->
            <div class="arcade-cabinet" data-game="code-jumper">
                <div class="screen-frame">
                    <div class="screen-content">
                        <div class="game-preview"></div>
                        <div class="game-info">
                            <h3>Code Jumper</h3>
                            <p>Platform nel codice</p>
                        </div>
                    </div>
                    <div class="screen-glare"></div>
                </div>
                <div class="cabinet-controls">
                    <button class="play-button" onclick="location.href='games/code-jumper.php'">
                        <span>▶ GIOCA</span>
                    </button>
                    <div class="coin-slot">INSERT COIN</div>
                </div>
            </div>

            <!-- Nano Blaster -->
            <div class="arcade-cabinet" data-game="nano-blaster">
                <div class="screen-frame">
                    <div class="screen-content">
                        <div class="game-preview"></div>
                        <div class="game-info">
                            <h3>Nano Blaster</h3>
                            <p>Battaglia nel microchip</p>
                        </div>
                    </div>
                    <div class="screen-glare"></div>
                </div>
                <div class="cabinet-controls">
                    <button class="play-button" onclick="location.href='games/nano-blaster.php'">
                        <span>▶ GIOCA</span>
                    </button>
                    <div class="coin-slot">INSERT COIN</div>
                </div>
            </div>
        </div>

        <!-- Decorazioni sala giochi -->
        <div class="arcade-decorations">
            <div class="neon-strips left"></div>
            <div class="neon-strips right"></div>
            <div class="floor-pattern"></div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="arcade-footer">
        <p>© 2025 G Tech Group - <a href="https://www.gtechgroup.it">www.gtechgroup.it</a></p>
        <div class="high-score-ticker">
            <span>HIGH SCORES: </span>
            <span id="ticker-content"></span>
        </div>
    </footer>

    <!-- Audio di sottofondo (opzionale) -->
    <audio id="arcade-ambience" loop>
        <source src="assets/arcade-ambience.mp3" type="audio/mpeg">
    </audio>

    <!-- JavaScript -->
    <script src="js/arcade-effects.js"></script>
</body>
</html>
