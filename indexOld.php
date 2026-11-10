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
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Beauté</title>
    <link rel="stylesheet" href="css/style.css">
    
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
                    <img src="img/first.webp" alt="La Beaut&eacute;" width="560" height="320">
                    <img src="img/second.webp" alt="La Beaut&eacute;" width="560" height="320">
                    <img src="img/third.webp" alt="La Beaut&eacute;" width="560" height="320">
                    <img src="img/fourth.webp" alt="La Beaut&eacute;" width="560" height="320">
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
                <img src="img/mani.webp" alt="Trattamenti Mani e Piedi" loading="lazy">
            </div>

            <div class="treatment-card active" data-category="viso">
                <h4 id="myPuntualFont">Trattamento Viso</h4>
                <p id="obliquo">Riportiamo la pelle del viso al suo naturale splendore con i nostri trattamenti specifici in funzione del tipo di pelle</p>
                <img src="img/rf_viso.webp" alt="Trattamento Viso" loading="lazy">
            </div>

            <div class="treatment-card active" data-category="corpo">
                <h4 id="myPuntualFont">Trattamento Corpo</h4>
                <p id="obliquo">Con uso di <b><u>Radiofrequenza Quadripolare Dinamica</u></b> combatte <b><u>Lassità</u></b> cutanea e migliora la <b><u>Texture</u></b> della pelle</p>
                <img src="img/rqd.webp" alt="Trattamento Corpo" loading="lazy">
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
                <img src="img/endosphere.webp" alt="Endosphères Therapy" loading="lazy">
            </div>

            <div class="treatment-card active" data-category="massaggi">
                <h4 id="myPuntualFont">L' Angolo dei Massaggi</h4>
                <img src="img/massaggi.webp" alt="Massaggi" loading="lazy">
            </div>

            <div class="treatment-card active" data-category="epilazione">
                <h4 id="myPuntualFont">Epilazione</h4>
                <ul id="obliquo">
                    <li>Oltre la cura tradizionale usiamo anche il metodo più delicato, igienico ed efficace.</li>
                    <li>Sugarpaste Depilazione, Depilazione Brasiliana e con il filo.</li>
                    <li>Fotoepilazione Permanente con luce Pulsata.</li>
                    <li>Elettrocoagulazione.</li>
                </ul><br>
                <img src="img/epilazione.webp" alt="Epilazione" loading="lazy">
            </div>
        </section>

        <!-- Dove Siamo -->
        <section id="services" class="container">
            <h1 id="myPuntualFont">Dove Siamo</h1>
            <div class="map-wrap">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2971.737853876107!2d12.484667315232361!3d41.855468974809504!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x13258a63b2d902f3%3A0x461e725642c371af!2sLa+Beaut%C3%A9!5e0!3m2!1sit!2sit!4v1500503116292" width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen></iframe>
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
    </script>
</body>
</html>