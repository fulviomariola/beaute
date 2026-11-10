<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_contact'])) {
    header('Content-Type: application/json');
    $name    = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $email   = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $human   = filter_input(INPUT_POST, 'human', FILTER_VALIDATE_INT);

    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message) || $human !== 5) {
        echo json_encode(['success' => false, 'message' => 'Compila tutti i campi correttamente (e risolvi l\'anti-spam).']);
        exit;
    }

    $to      = 'nataliatym@libero.it';
    $subject = 'La Beauté - Nuovo contatto';
    $body    = "Nome: {$name}\nE-Mail: {$email}\n\nMessaggio:\n{$message}";
    $headers = "From: noreply@" . $_SERVER['SERVER_NAME'] . "\r\nReply-To: {$email}\r\nX-Mailer: PHP/" . phpversion();

    if (@mail($to, $subject, $body, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Messaggio inviato con successo! Ti risponderemo a breve.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'invio. Riprova più tardi.']);
    }
    exit;
}
?>

<?php
// Dimensioni reali delle immagini: permettono al browser di riservare lo spazio
// giusto PRIMA del caricamento (migliora il CLS, una delle metriche di PageSpeed).
function lb_img_dim($path) {
    $info = @getimagesize(__DIR__ . '/' . $path);
    return $info ? [$info[0], $info[1]] : [800, 600];
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Beauté</title>
    <meta name="description" content="La Beauté, centro estetico a Roma: trattamenti viso, corpo, mani e piedi, Endosphères, massaggi ed epilazione. Chiama e fissa un appuntamento: 06-83901083.">
    <link rel="stylesheet" href="css/style.css">
    <?php if (file_exists(__DIR__ . '/img/favicon.png')) echo '<link rel="icon" type="image/png" href="img/favicon.png">'; ?>
    <!-- Precarica subito l'immagine principale del carosello (è il candidato LCP) -->
    <link rel="preload" as="image" href="img/first.webp" fetchpriority="high">

    <style>
        /* ====== COOKIE BANNER: stili aggiuntivi (puoi spostarli in style.css) ====== */
        #cookieBanner {
            position: fixed; left: 0; right: 0; bottom: 0; z-index: 9999;
            background: #ffffff; color: #333333;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, .25);
            padding: 16px 20px; font-size: 14px; line-height: 1.55;
            display: none; /* appare solo se l'utente non ha ancora fatto una scelta */
        }
        #cookieBanner.show { display: block; }
        #cookieBanner .cb-inner { max-width: 960px; margin: 0 auto; padding-right: 30px; }
        #cookieBanner .cb-actions { margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap; }
        #cookieBanner button {
            border: 1px solid #999999; background: #ffffff; color: #333333;
            padding: 8px 18px; border-radius: 4px; cursor: pointer; font-size: 14px;
        }
        #cookieBanner button.cb-accept { background: #333333; color: #ffffff; border-color: #333333; }
        #cookieBanner button:hover { opacity: .85; }
        #cookieBanner .cb-close {
            position: absolute; top: 6px; right: 10px;
            background: none; border: none; font-size: 22px; line-height: 1; padding: 4px;
        }
        #cookieBanner a { color: inherit; }

        /* Pulsantino fisso per riaprire le preferenze in qualsiasi momento */
        #cookiePrefs {
            position: fixed; left: 10px; bottom: 10px; z-index: 9998;
            background: rgba(0, 0, 0, .55); color: #ffffff; border: none; border-radius: 16px;
            padding: 5px 14px; font-size: 12px; cursor: pointer;
        }

        /* Placeholder della mappa prima del consenso (click-to-load) */
        .map-wrap { position: relative; }
        .map-placeholder {
            position: absolute; top: 0; right: 0; bottom: 0; left: 0;
            background: #f2f2f2; color: #555555;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center; gap: 12px; padding: 20px; font-size: 15px;
        }
        .map-placeholder button {
            border: 1px solid #999999; background: #ffffff; color: #333333;
            padding: 8px 18px; border-radius: 4px; cursor: pointer; font-size: 14px;
        }
    </style>
    
</head>
<body id="page-top">

    <nav>
        <a class="myfont logo" href="#page-top">La Beaut&eacute;</a>

                <!-- Tasto Hamburger per Mobile -->
                <button class="nav-toggle" id="navToggle" aria-label="Menu di navigazione">
                        <span></span>
                        <span></span>
                        <span></span>
                </button>

        <ul class="nav-links" id="navLinks">
            <li><a class="myfont" href="#about">I Nostri Trattamenti</a></li>
            <li><a class="myfont" href="#services">Dove Siamo</a></li>
            <li><a class="myfont" href="#contact">Contatti</a></li>
        </ul>
    </nav>

    <main>
        <!-- Intro -->
        <section id="intro" class="container">
            <div style="text-align: left; margin-bottom: 15px;">
                <p id="myPuntualFontMin">
                    Chiama e fissa un appuntamento:<br>
                    <a href="tel:+390683901083" style="color: inherit;">06-83901083</a>&nbsp;&nbsp;oppure&nbsp;&nbsp;
                    <a href="tel:+393490748309" style="color: inherit;">349-0748309</a>
                </p>
            </div>

            <h1 id="myPuntualFont">La Beauté</h1>

            <!-- Carosello Vanilla JS -->
            <div class="carousel">
                <div class="carousel-slide" id="carouselSlide">
                    <img src="img/first.webp" alt="La Beaut&eacute;" width="560" height="320" fetchpriority="high">
                    <img src="img/second.webp" alt="La Beaut&eacute;" width="560" height="320" loading="lazy">
                    <img src="img/third.webp" alt="La Beaut&eacute;" width="560" height="320" loading="lazy">
                    <img src="img/fourth.webp" alt="La Beaut&eacute;" width="560" height="320" loading="lazy">
                </div>
            </div>
        </section>

        <!-- Trattamenti Dinamici -->
        <section id="about" class="container ">
            <h1 id="myPuntualFont">I Nostri Trattamenti</h1>
            
            <!-- Schede Filtri -->
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('all')">Tutti</button>
                <button class="tab-btn" onclick="showTab('mani')">Mani e Piedi</button>
                <button class="tab-btn" onclick="showTab('viso')">Viso</button>
                <button class="tab-btn" onclick="showTab('corpo')">Corpo</button>
                <button class="tab-btn" onclick="showTab('endosphere')">Endosphères</button>
                <button class="tab-btn" onclick="showTab('massaggi')">Massaggi</button>
                <button class="tab-btn" onclick="showTab('epilazione')">Epilazione</button>
            </div>

            <!-- Card Trattamenti -->
            <div class="treatment-card active" data-category="mani">
                <h4 id="myPuntualFont">Trattamenti Mani e Piedi</h4>
                <p id="obliquo">Per chi vive la bellezza a 360°</p>
                <?php [$iw, $ih] = lb_img_dim('img/mani.webp'); ?>
                <img src="img/mani.webp" alt="Trattamenti Mani e Piedi" loading="lazy" decoding="async" width="<?= $iw ?>" height="<?= $ih ?>">
            </div>

            <div class="treatment-card active" data-category="viso">
                <h4 id="myPuntualFont">Trattamento Viso</h4>
                <p id="obliquo">Riportiamo la pelle del viso al suo naturale splendore con i nostri trattamenti specifici in funzione del tipo di pelle</p>
                <?php [$iw, $ih] = lb_img_dim('img/rf_viso.webp'); ?>
                <img src="img/rf_viso.webp" alt="Trattamento Viso" loading="lazy" decoding="async" width="<?= $iw ?>" height="<?= $ih ?>">
            </div>

            <div class="treatment-card active" data-category="corpo">
                <h4 id="myPuntualFont">Trattamento Corpo</h4>
                <p id="obliquo">Con uso di <b><u>Radiofrequenza Quadripolare Dinamica</u></b> combatte <b><u>Lassità</u></b> cutanea e migliora la <b><u>Texture</u></b> della pelle</p>
                <?php [$iw, $ih] = lb_img_dim('img/rqd.webp'); ?>
                <img src="img/rqd.webp" alt="Trattamento Corpo" loading="lazy" decoding="async" width="<?= $iw ?>" height="<?= $ih ?>">
            </div>

            <div class="treatment-card active" data-category="endosphere">
                <h4 id="myPuntualFont">Endosphères Therapy</h4>
                <p id="obliquo">Ultima frontiera del Body Shaping con Microvibrazione Compressiva che produce:</p>
                <ul id="obliquo">
                    <li>Ridurre drasticamente le Culotte de Cheval</li>
                    <li>Per combattere la cellulite</li>
                    <li>Ridurre la pelle a "Buccia d'Arancia"</li>
                    <li>Elimina i liquidi in eccesso, donando leggerezza alle gambe</li>
                </ul><br>
                <?php [$iw, $ih] = lb_img_dim('img/endosphere.webp'); ?>
                <img src="img/endosphere.webp" alt="Endosphères Therapy" loading="lazy" decoding="async" width="<?= $iw ?>" height="<?= $ih ?>">
            </div>

            <div class="treatment-card active" data-category="massaggi">
                <h4 id="myPuntualFont">L' Angolo dei Massaggi</h4>
                <?php [$iw, $ih] = lb_img_dim('img/massaggi.webp'); ?>
                <img src="img/massaggi.webp" alt="Massaggi" loading="lazy" decoding="async" width="<?= $iw ?>" height="<?= $ih ?>">
            </div>

            <div class="treatment-card active" data-category="epilazione">
                <h4 id="myPuntualFont">Epilazione</h4>
                <ul id="obliquo">
                    <li>Oltre la cura tradizionale usiamo anche il metodo più delicato, igienico ed efficace.</li>
                    <li>Sugarpaste Depilazione, Depilazione Brasiliana e con il filo.</li>
                    <li>Fotoepilazione Permanente con luce Pulsata.</li>
                    <li>Elettrocoagulazione.</li>
                </ul><br>
                <?php [$iw, $ih] = lb_img_dim('img/epilazione.webp'); ?>
                <img src="img/epilazione.webp" alt="Epilazione" loading="lazy" decoding="async" width="<?= $iw ?>" height="<?= $ih ?>">
            </div>
        </section>

        <!-- Dove Siamo -->
        <section id="services" class="container">
            <h1 id="myPuntualFont">Dove Siamo</h1>
            <div class="map-wrap">
                <!-- Cookie: la mappa NON viene caricata finché l'utente non dà il consenso -->
                <iframe id="gmapIframe" data-src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2971.737853876107!2d12.484667315232361!3d41.855468974809504!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x13258a63b2d902f3%3A0x461e725642c371af!2sLa+Beaut%C3%A9!5e0!3m2!1sit!2sit!4v1500503116292" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen title="Mappa: Dove siamo"></iframe>
                <div id="mapPlaceholder" class="map-placeholder">
                    <p>La mappa è fornita da <b>Google Maps</b> e per visualizzarla<br>vengono installati cookie di terze parti di Google.</p>
                    <button type="button" id="loadMapBtn">Carica la mappa (accetto i cookie di Google Maps)</button>
                </div>
            </div>
        </section>

        <!-- Contatti -->
        <section id="contact" class="container">
            <h1 id="myPuntualFont">Sezione Contatti</h1>
            <form id="contactForm">
                <input type="hidden" name="ajax_contact" value="1">
                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text" id="name" name="name" placeholder="Nome & Cognome" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="esempio@dominio.it" required>
                </div>
                <div class="form-group">
                    <label for="message">Messaggio</label>
                    <textarea id="message" name="message" rows="4" placeholder="Scrivi il tuo messaggio..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="human">2 + 3 = ?</label>
                    <input type="number" id="human" name="human" placeholder="Numerico" required>
                </div>
                <button type="submit" class="btn-submit" id="submitBtn">Invia</button>
                <div id="form-alert"></div>
            </form>
        </section>
    </main>

    <!-- ====== COOKIE BANNER (obbligatorio: il sito incorpora Google Maps) ====== -->
    <div id="cookieBanner" role="dialog" aria-live="polite" aria-label="Informativa cookie">
        <button type="button" class="cb-close" id="cbClose" aria-label="Chiudi e prosegui senza accettare">&times;</button>
        <div class="cb-inner">
            <p>
                <b>Informativa breve:</b> questo sito usa solo memorie tecniche necessarie al suo funzionamento
                (ad esempio per ricordare le tue scelte) e, soltanto con il tuo consenso, cookie di terze parti di
                <b>Google Maps</b>, che servono a mostrarti la mappa della sede. Nessun cookie viene installato
                prima della tua scelta. <a href="cookie-policy.html">Informativa completa sui cookie</a>
            </p>
            <div class="cb-actions">
                <button type="button" class="cb-accept" id="cbAccept">Accetta i cookie</button>
                <button type="button" id="cbRefuse">Rifiuta</button>
            </div>
        </div>
    </div>
    <button type="button" id="cookiePrefs" title="Gestisci le preferenze sui cookie">Cookie</button>

    <script>
        // Carosello Automatico Vanilla
        const slide = document.getElementById('carouselSlide');
        let index = 0;
        const total = slide.children.length;
                const navToggle = document.getElementById('navToggle');
                const navLinks = document.getElementById('navLinks');

                // Apertura / Chiusura al click sul pulsante
                navToggle.addEventListener('click', () => {
                        navLinks.classList.toggle('active');
                });

                // Chiusura automatica quando si tocca uno dei link
                navLinks.querySelectorAll('a').forEach(link => {
                        link.addEventListener('click', () => {
                                navLinks.classList.remove('active');
                        });
                });

        setInterval(() => {
            index = (index + 1) % total;
            slide.style.transform = `translateX(-${index * 100}%)`;
        }, 3500);

        // Filtro Schede Trattamenti
        function showTab(category) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            const cards = document.querySelectorAll('.treatment-card');
            cards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.classList.add('active');
                } else {
                    card.classList.remove('active');
                }
            });
        }

        // Form Asincrono Fetch API (Zero ricaricamento)
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const alertBox = document.getElementById('form-alert');
            const formData = new FormData(this);

            btn.disabled = true;
            btn.textContent = 'Invio in corso...';
            alertBox.style.display = 'none';

            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alertBox.style.display = 'block';
                alertBox.textContent = data.message;
                if (data.success) {
                    alertBox.style.backgroundColor = '#d4edda';
                    alertBox.style.color = '#155724';
                    document.getElementById('contactForm').reset();
                } else {
                    alertBox.style.backgroundColor = '#f8d7da';
                    alertBox.style.color = '#721c24';
                }
            })
            .catch(() => {
                alertBox.style.display = 'block';
                alertBox.style.backgroundColor = '#f8d7da';
                alertBox.style.color = '#721c24';
                alertBox.textContent = 'Si è verificato un errore di rete.';
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Invia';
            });
        });

        // ====== GESTIONE CONSENSO COOKIE (aggiunta) ======
        const CB_KEY = 'lb-cookie-consent';
        const cbBanner   = document.getElementById('cookieBanner');
        const gmap       = document.getElementById('gmapIframe');
        const mapPh      = document.getElementById('mapPlaceholder');
        const loadMapBtn = document.getElementById('loadMapBtn');

        function getConsent() { try { return localStorage.getItem(CB_KEY); } catch (e) { return null; } }
        function setConsent(v) { try { localStorage.setItem(CB_KEY, v); } catch (e) {} }

        // La mappa parte SOLO qui: prima di questo momento Google non riceve nessuna richiesta
        function loadMap() {
            if (gmap.getAttribute('src') !== gmap.dataset.src) {
                gmap.setAttribute('src', gmap.dataset.src);
            }
            mapPh.style.display = 'none';
        }
        function unloadMap() {
            gmap.removeAttribute('src');
            mapPh.style.display = 'flex';
        }

        document.getElementById('cbAccept').addEventListener('click', function () {
            setConsent('accepted'); cbBanner.classList.remove('show'); loadMap();
        });
        document.getElementById('cbRefuse').addEventListener('click', function () {
            setConsent('refused'); cbBanner.classList.remove('show'); unloadMap();
        });
        // Chiudere con la X equivale a proseguire senza accettare
        document.getElementById('cbClose').addEventListener('click', function () {
            setConsent('refused'); cbBanner.classList.remove('show'); unloadMap();
        });
        // Consenso "granulare": l'utente carica la mappa solo se la vuole
        loadMapBtn.addEventListener('click', function () {
            setConsent('accepted'); cbBanner.classList.remove('show'); loadMap();
        });
        // Riapertura del pannello (cambio idea / revoca del consenso)
        document.getElementById('cookiePrefs').addEventListener('click', function () {
            cbBanner.classList.add('show');
        });

        // Stato iniziale
        if (getConsent() === 'accepted') {
            loadMap();
        } else if (getConsent() === null) {
            cbBanner.classList.add('show'); // prima visita: nessun cookie di Google ancora installato
        }
    </script>
</body>
</html>