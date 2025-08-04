# G Tech Arcade (play.gtechgroup.it)

Benvenuti nel progetto **G Tech Arcade**, una sala giochi HTML5 professionale e responsive ideata per dimostrare le competenze tecniche, creative e di sicurezza informatica del team G Tech Group. Il progetto, ospitato su `play.gtechgroup.it`, raccoglie 9 minigiochi originali che funzionano completamente in una singola pagina PHP con JavaScript vanilla, senza librerie esterne, perfettamente compatibili con desktop e mobile.

## 🎯 Obiettivi del progetto

* Dimostrare abilità avanzate in HTML5, JavaScript e Canvas API
* Produrre un'esperienza utente ludica e intuitiva, senza dipendenze
* Comunicare la visione di G Tech su tecnologia, sicurezza e innovazione
* Offrire un punto dimostrativo utilizzabile in fiere, eventi, sito o formazione

---

## 🌍 URL di produzione

* [https://play.gtechgroup.it](https://play.gtechgroup.it)

---

## 🗂️ Architettura del progetto

```
/play.gtechgroup.it/
│
├── index.php                   # Sala giochi principale (homepage)
├── /assets/                   # Logo, immagini, icone e sprite
│   ├── logo.png
│   ├── bg.jpg
│   └── icons/
│
├── /games/                    # Pagine PHP per ogni gioco
│   ├── firewall-defender.php
│   ├── eraldin.php
│   ├── hedgehog-rush.php
│   ├── cyberscape.php
│   ├── packet-runner.php
│   ├── ghostproxy.php
│   ├── datastream-dash.php
│   ├── code-jumper.php
│   └── nano-blaster.php
│
├── /css/                      # Fogli di stile globali
├── /js/                       # Script JS comuni o di supporto
└── .htaccess                  # Per URL rewriting opzionale
```

---

## 🎮 Giochi inclusi (totale: 9)

| Titolo                | Descrizione migliorata                                                                                                                                                                                                                                                                                                                                            |
| --------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Firewall Defender** | Sparatutto verticale in stile retrò dove il giocatore controlla un firewall animato che protegge il data center G Tech. Ondate di malware digitali (trojan, ransomware, botnet) cercano di attaccare il core, ma possono essere neutralizzati con aggiornamenti, patch e moduli antivirus. Presenta una colonna sonora chip-style ed effetti sonori di scansione. |
| **Eraldin**           | Action RPG isometrico fantasy/cyberpunk. Il protagonista Eraldin affronta creature digitali, demoni di rete e bug viventi in un regno codificato, in missione per ripristinare l'integrità di un server caduto. Sistema di inventario, XP e dialoghi basato su IA narrativa.                                                                                      |
| **Hedgehog Rush**     | Platform 2D ultrafluido ispirato a Sonic. Il giocatore controlla un riccio robotico che attraversa ambienti digitali pieni di anelli di dati, trappole magnetiche e salti acrobatici. Ogni livello aumenta in velocità e richiede riflessi rapidi. Include power-up di velocità, invincibilità e slow-motion.                                                     |
| **Cyberscape**        | Runner 3D ispirato a Tron, con visuale in prima persona o 2.5D. L’avatar corre in un tunnel di dati, schivando firewall mobili, decryptor laser e stringhe corrotte. Gioco endless con moltiplicatori di punteggio e checkpoint visivi.                                                                                                                           |
| **Packet Runner**     | Puzzle arcade in cui si impersona un pacchetto di rete che deve attraversare un’infrastruttura congestionata. Il giocatore deve pianificare i salti, evitare loop DNS, delay e packet loss. Include variabilità nei livelli con elementi randomici di latenza.                                                                                                    |
| **Ghost Proxy**       | Stealth game top-down. Il giocatore è un proxy “invisibile” che deve attraversare reti di sorveglianza senza essere tracciato da DPI, ISP e firewall AI. Meccaniche di camouflage, instradamento dinamico e gestione dei nodi. Ambientazione visual hacker-style in dark mode.                                                                                    |
| **Datastream Dash**   | Scorrimento orizzontale veloce con una navicella che raccoglie pacchetti sicuri e devia virus digitali. Ogni colore ha un significato semantico (media, executable, dati sensibili). Obiettivo: mantenere l’integrità del flusso dati. Include boost, turbo e sistema combo.                                                                                      |
| **Code Jumper**       | Puzzle-platform concettuale ambientato in un editor di codice. Ogni livello rappresenta un linguaggio: salta tra cicli `for`, blocchi `try/catch`, `functions`, evitando bug, errori di compilazione e memory leaks. È richiesto pensiero logico e tempismo.                                                                                                      |
| **Nano Blaster**      | Arena shooter ambientato all’interno di un microchip. Il giocatore difende le celle di memoria da nano-virus autoreplicanti. I nemici aumentano progressivamente, fino al boss finale: un worm crittografico in grado di mutare. Bonus stage, effetti visivi laser e comandi CLI avanzati.                                                                        |

---

## 🛠 Requisiti

* PHP >= 7.4 (testato fino alla 8.4)
* Hosting Linux (Apache/NGINX) — ideale con Plesk
* Browser moderno (supporto completo a HTML5, Canvas e JS)
* Nessuna libreria o plugin esterno necessario

---

## 🔄 Installazione locale

```bash
git clone https://github.com/gtechgroup/gtech-arcade.git
```

* Caricare tutti i file su hosting o localhost
* Accedere a `http://localhost/play.gtechgroup.it` oppure al dominio assegnato

---

## 🎨 Personalizzazione

* Logo: `assets/logo.png`
* Sfondo: `assets/bg.jpg`
* CSS globale: `css/style.css`
* Ogni gioco è completamente modificabile nel rispettivo file `.php`
* Effetti sonori: integrabili da HTML5 Audio o file `.mp3/.ogg`

---

## ✨ Funzionalità avanzate (modulari)

* Punteggio salvabile in `localStorage` o via PHP
* Classifiche globali sincronizzate (opzionale, via DB)
* Modalità "evento live" con tracciamento sessioni
* Branding dinamico in base al cliente (white-label)

---

## 🏠 index.php (homepage giochi)

* Layout 3x3 responsive (Bootstrap-free, CSS Grid o Flexbox)
* Ogni gioco mostra: anteprima, nome, descrizione breve, pulsante “Gioca ora”
* Effetti hover e badge “Nuovo” per giochi appena pubblicati

---

## 💡 Espansioni future suggerite

* Multiplayer asincrono con punteggio incrociato
* Integrazione WebXR per visori VR/WebVR
* Editor visuale per creare giochi custom
* API REST per login, punteggio, cronologia sessioni

---

## 🚀 Crediti

**Ideazione e sviluppo:** [Gianluca Gentile](https://www.gtechgroup.it) · G Tech Group © 2025

Tutti i giochi, grafica, codice e contenuti sono originali e protetti. Ogni modulo dimostra un'applicazione tecnica diversa: logica, UI, accessibilità, sicurezza, gameplay, backend-ready.

---

## 📅 Roadmap

* [ ] Miglioramento UI per dispositivi mobili
* [ ] Aggiunta sezione "About"
* [ ] Integrazione classifica via MySQL + backend
* [ ] Modalità torneo per eventi in fiera

---

## ⚠️ Licenza

Licenza: **G Tech Proprietary Internal Demo License**

* Uso dimostrativo interno o eventi approvati
* Vietata la distribuzione pubblica senza consenso

Per partnership o licenze commerciali: [info@gtechgroup.it](mailto:info@gtechgroup.it)
