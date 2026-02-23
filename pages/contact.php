<?php
$page_title = 'Contact';
require_once '../includes/config.php';

require_once '../includes/header.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Contact</span>
        </div>
        <h1>Contactez <span style="color:var(--secondary)">Le FAJ Niger</span></h1>
        <p>Notre équipe est disponible pour répondre à toutes vos questions</p>
    </div>
</div>

<section style="padding:90px 0; background:var(--light);">
    <div class="container">
        <div style="display:grid; grid-template-columns:1fr 1.5fr; gap:60px; align-items:start;">
            
            <!-- Infos de contact -->
            <div>
                <h2 class="section-title" style="font-size:28px;" data-aos="fade-right">
                    Nous Sommes <span>Disponibles</span> 24h/7j
                </h2>
                <p style="color:var(--gray); margin-bottom:40px; line-height:1.8;">
                    Vous pouvez nous contacter de toutes les manières qui vous conviennent. 
                    Nous sommes disponibles pour répondre à vos questions sur nos projets, 
                    les dons et toute autre demande.
                </p>
                
                <div style="display:flex; flex-direction:column; gap:24px;">
                    <div style="display:flex; gap:20px; background:white; padding:24px; border-radius:var(--radius-lg); box-shadow:var(--shadow);" data-aos="fade-right" data-aos-delay="100">
                        <div style="width:50px; height:50px; background:rgba(232,135,10,0.1); border-radius:var(--radius); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-map-marker-alt" style="font-size:22px; color:var(--secondary);"></i>
                        </div>
                        <div>
                            <h5 style="font-weight:700; color:var(--primary); margin-bottom:4px;">Adresse</h5>
                            <p style="color:var(--gray); font-size:14px;"><?= getSiteParam('site_adresse', 'Niamey, Niger') ?></p>
                        </div>
                    </div>
                    
                    <div style="display:flex; gap:20px; background:white; padding:24px; border-radius:var(--radius-lg); box-shadow:var(--shadow);" data-aos="fade-right" data-aos-delay="200">
                        <div style="width:50px; height:50px; background:rgba(232,135,10,0.1); border-radius:var(--radius); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-phone" style="font-size:22px; color:var(--secondary);"></i>
                        </div>
                        <div>
                            <h5 style="font-weight:700; color:var(--primary); margin-bottom:4px;">Téléphone</h5>
                            <a href="tel:<?= getSiteParam('site_telephone','') ?>" style="color:var(--gray); font-size:14px;"><?= getSiteParam('site_telephone', '+227 20 XX XX XX') ?></a>
                        </div>
                    </div>
                    
                    <div style="display:flex; gap:20px; background:white; padding:24px; border-radius:var(--radius-lg); box-shadow:var(--shadow);" data-aos="fade-right" data-aos-delay="300">
                        <div style="width:50px; height:50px; background:rgba(232,135,10,0.1); border-radius:var(--radius); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-envelope" style="font-size:22px; color:var(--secondary);"></i>
                        </div>
                        <div>
                            <h5 style="font-weight:700; color:var(--primary); margin-bottom:4px;">Email</h5>
                            <a href="mailto:<?= getSiteParam('site_email','contact@faj.ne') ?>" style="color:var(--gray); font-size:14px;"><?= getSiteParam('site_email', 'contact@faj.ne') ?></a>
                        </div>
                    </div>
                </div>
                
                <!-- Réseaux sociaux -->
                <div style="margin-top:40px;" data-aos="fade-right" data-aos-delay="400">
                    <h5 style="font-weight:700; color:var(--primary); margin-bottom:16px;">Suivez-nous</h5>
                    <div style="display:flex; gap:12px;">
                        <?php
                        $socials = [
                            ['url'=>getSiteParam('site_facebook','#'), 'icon'=>'fab fa-facebook-f', 'label'=>'Facebook', 'color'=>'#1877F2'],
                            ['url'=>getSiteParam('site_twitter','#'), 'icon'=>'fab fa-x-twitter', 'label'=>'Twitter/X', 'color'=>'#000'],
                            ['url'=>getSiteParam('site_linkedin','#'), 'icon'=>'fab fa-linkedin-in', 'label'=>'LinkedIn', 'color'=>'#0A66C2'],
                            ['url'=>getSiteParam('site_youtube','#'), 'icon'=>'fab fa-youtube', 'label'=>'YouTube', 'color'=>'#FF0000'],
                        ];
                        foreach ($socials as $s):
                        ?>
                        <a href="<?= $s['url'] ?>" target="_blank" 
                           style="width:44px; height:44px; background:<?= $s['color'] ?>1a; border-radius:50%; display:flex; align-items:center; justify-content:center; color:<?= $s['color'] ?>; font-size:16px; transition:var(--transition);"
                           onmouseover="this.style.background='<?= $s['color'] ?>'; this.style.color='white';"
                           onmouseout="this.style.background='<?= $s['color'] ?>1a'; this.style.color='<?= $s['color'] ?>';"
                           title="<?= $s['label'] ?>">
                            <i class="<?= $s['icon'] ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Formulaire de contact -->
            <div style="background:white; border-radius:var(--radius-lg); padding:50px; box-shadow:var(--shadow);" data-aos="fade-left">
                <h3 style="font-family:var(--font-display); font-size:24px; color:var(--primary); margin-bottom:8px;">
                    <i class="fas fa-paper-plane" style="color:var(--secondary);"></i> Envoyez-nous un Message
                </h3>
                <p style="color:var(--gray); font-size:14px; margin-bottom:30px;">
                    Remplissez le formulaire et nous vous répondrons sous 24 heures.
                </p>
                
                <form id="contactForm" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom complet <span class="required">*</span></label>
                            <input type="text" id="nom" name="nom" class="form-control" placeholder="Votre nom" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" class="form-control" placeholder="+227 XX XX XX XX">
                    </div>
                    
                    <div class="form-group">
                        <label for="sujet">Sujet <span class="required">*</span></label>
                        <select id="sujet" name="sujet" class="form-control" required>
                            <option value="">Sélectionnez un sujet</option>
                            <option value="Information sur le don">Information sur le don</option>
                            <option value="Question sur un projet">Question sur un projet</option>
                            <option value="Partenariat">Partenariat</option>
                            <option value="Bénévolat">Bénévolat</option>
                            <option value="Problème technique">Problème technique</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message <span class="required">*</span></label>
                        <textarea id="message" name="message" class="form-control" rows="5" 
                                  placeholder="Décrivez votre demande en détail..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; font-size:16px; padding:16px;">
                        <i class="fas fa-paper-plane"></i> Envoyer le Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Carte Google Maps (placeholder) -->
<div style="height:400px; background:var(--primary); position:relative; overflow:hidden;">
    <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; flex-direction:column; gap:20px; color:white; text-align:center; padding:20px;">
        <i class="fas fa-map-marker-alt" style="font-size:60px; color:var(--secondary);"></i>
        <h3 style="font-family:var(--font-display); font-size:28px;">Fonds d'Appui à la Justice</h3>
        <p style="color:rgba(255,255,255,0.75); font-size:16px;"><?= getSiteParam('site_adresse', 'Niamey, Niger') ?></p>
        <a href="https://maps.google.com/?q=Niamey,Niger" target="_blank" class="btn btn-primary">
            <i class="fas fa-directions"></i> Obtenir l'itinéraire
        </a>
    </div>
</div>

<style>
@media (max-width:768px) {
    section > .container > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
