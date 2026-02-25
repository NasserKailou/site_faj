<?php require_once __DIR__ . '/config.php'; ?>

<!-- Footer -->
<footer class="footer">
    <!-- Footer Top -->
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">
                <!-- Logo & Description -->
                <div class="footer-col footer-about">
                    <a href="<?= SITE_URL ?>/" class="footer-brand">
                        <img src="<?= SITE_URL ?>/assets/images/logo-faj.png" alt="FAJ Niger" class="footer-logo">
                        <div>
                            <span class="footer-brand-name">F.A.J Niger</span>
                            <span class="footer-brand-tagline">Fonds d'Appui à la Justice</span>
                        </div>
                    </a>
                    <p class="footer-desc"><?= getSiteParam('site_description', 'Le FAJ collecte des fonds pour moderniser et améliorer le système judiciaire du Niger, pour que la Justice soit accessible à tous.') ?></p>
                    <div class="footer-social">
                        <a href="<?= getSiteParam('site_facebook', '#') ?>" target="_blank" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?= getSiteParam('site_twitter', '#') ?>" target="_blank" class="social-link"><i class="fab fa-x-twitter"></i></a>
                        <a href="<?= getSiteParam('site_linkedin', '#') ?>" target="_blank" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="<?= getSiteParam('site_youtube', '#') ?>" target="_blank" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <!-- Liens rapides -->
                <div class="footer-col">
                    <h4 class="footer-title">Liens Rapides</h4>
                    <ul class="footer-links">
                        <li><a href="<?= SITE_URL ?>/"><i class="fas fa-chevron-right"></i> Accueil</a></li>
                        <li><a href="<?= SITE_URL ?>/a-propos"><i class="fas fa-chevron-right"></i> À Propos</a></li>
                        <li><a href="<?= SITE_URL ?>/projets"><i class="fas fa-chevron-right"></i> Nos Projets</a></li>
                        <li><a href="<?= SITE_URL ?>/actualites"><i class="fas fa-chevron-right"></i> Actualités</a></li>
                        <li><a href="<?= SITE_URL ?>/equipe"><i class="fas fa-chevron-right"></i> Équipe</a></li>
                        <li><a href="<?= SITE_URL ?>/contact"><i class="fas fa-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>
                
                <!-- Projets -->
                <div class="footer-col">
                    <h4 class="footer-title">Nos Programmes</h4>
                    <ul class="footer-links">
                        <li><a href="<?= SITE_URL ?>/projets?cat=infrastructure"><i class="fas fa-chevron-right"></i> Infrastructures Judiciaires</a></li>
                        <li><a href="<?= SITE_URL ?>/projets?cat=formation"><i class="fas fa-chevron-right"></i> Formation &amp; Renforcement</a></li>
                        <li><a href="<?= SITE_URL ?>/projets?cat=humanisation"><i class="fas fa-chevron-right"></i> Humanisation Carcérale</a></li>
                        <li><a href="<?= SITE_URL ?>/projets?cat=acces_justice"><i class="fas fa-chevron-right"></i> Accès à la Justice</a></li>
                        <li><a href="<?= SITE_URL ?>/projets?cat=numerisation"><i class="fas fa-chevron-right"></i> Numérisation</a></li>
                        <li><a href="<?= SITE_URL ?>/don"><i class="fas fa-chevron-right"></i> Faire un Don</a></li>
                    </ul>
                </div>
                
                <!-- Contact & Newsletter -->
                <div class="footer-col">
                    <h4 class="footer-title">Nous Contacter</h4>
                    <div class="footer-contact-info">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= getSiteParam('site_adresse', 'Niamey, Niger') ?></span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?= getSiteParam('site_telephone', '') ?>"><?= getSiteParam('site_telephone', '+227 20 XX XX XX') ?></a>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?= getSiteParam('site_email', 'contact@faj.ne') ?>"><?= getSiteParam('site_email', 'contact@faj.ne') ?></a>
                        </div>
                    </div>
                    
                    <!-- Newsletter -->
                    <div class="footer-newsletter">
                        <h5>S'inscrire à la newsletter</h5>
                        <form class="newsletter-form" id="newsletterForm" method="POST" action="<?= SITE_URL ?>/api/newsletter.php">
                            <input type="email" name="email" placeholder="Votre email..." required>
                            <button type="submit"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-inner">
                <p>&copy; <?= date('Y') ?> FAJ - Fonds d'Appui à la Justice du Niger. Tous droits réservés.</p>
                <div class="footer-bottom-links">
                    <a href="<?= SITE_URL ?>/mentions-legales">Mentions légales</a>
                    <a href="<?= SITE_URL ?>/politique-confidentialite">Confidentialité</a>
                    <a href="<?= SITE_URL ?>/conditions-generales">CGU</a>
                </div>
                <div class="footer-payments">
                    <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons@v9/icons/visa.svg" alt="Visa" class="payment-icon">
                    <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons@v9/icons/mastercard.svg" alt="Mastercard" class="payment-icon">
                    <span class="payment-text">Orange Money</span>
                    <span class="payment-text">Moov Money</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to top -->
<button class="back-to-top" id="backToTop" aria-label="Retour en haut">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/countup.js@2.8.0/dist/countUp.umd.js"></script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>

<?php if (isset($extra_js)) echo $extra_js; ?>

<script>
// Initialiser AOS
AOS.init({ duration: 800, once: true, offset: 100 });

// Newsletter form
document.getElementById('newsletterForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input[name="email"]').value;
    fetch('<?= SITE_URL ?>/api/newsletter.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({email})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            this.innerHTML = '<p class="success-msg"><i class="fas fa-check-circle"></i> Merci pour votre inscription !</p>';
        }
    });
});
</script>
</body>
</html>
