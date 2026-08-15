<?php
$page_title = 'À Propos du FAJ';
require_once '../includes/config.php';
require_once '../includes/faj_data.php';

try {
    $pdo = getDB();
    $equipe = $pdo->query("SELECT * FROM equipe WHERE actif=1 ORDER BY ordre ASC")->fetchAll();
} catch (Exception $e) {
    $equipe = [];
}

require_once '../includes/header.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">À Propos</span>
        </div>
        <h1>À Propos du <span style="color:var(--secondary)">FAJ Niger</span></h1>
        <p>En savoir plus sur notre mission, notre vision et notre équipe</p>
    </div>
</div>

<!-- Mission & Vision -->
<section style="background:white; padding:90px 0;">
    <div class="container">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:80px; align-items:center;">
            <div data-aos="fade-right">
                <span class="section-tag"><i class="fas fa-balance-scale"></i> Notre Mission</span>
                <h2 class="section-title">Pour une Justice <span>Accessible à Tous</span></h2>
                <div style="color:var(--gray); line-height:1.9; font-size:15px;">
                    <?= getSiteParam('a_propos_texte', '
                    <p>Le <strong>Fonds d\'Appui à la Justice (FAJ)</strong> est un mécanisme de financement innovant créé pour soutenir la modernisation et l\'amélioration du système judiciaire du Niger.</p>
                    <p>Notre mission est de mobiliser des ressources financières auprès de la société civile, des entreprises et des partenaires internationaux pour financer des projets structurants dans le domaine de la justice.</p>
                    <p>La Justice est rendue au nom du peuple. Le peuple doit contribuer à sa modernisation. À l\'unisson, nous pouvons relever tous les défis à travers nos contributions !</p>
                    ') ?>
                </div>
                <div style="display:flex; gap:16px; margin-top:30px;">
                    <a href="<?= SITE_URL ?>/don" class="btn btn-primary">
                        <i class="fas fa-heart"></i> Soutenir le FAJ
                    </a>
                    <a href="<?= SITE_URL ?>/projets" class="btn btn-outline-primary">
                        <i class="fas fa-project-diagram"></i> Nos Projets
                    </a>
                </div>
            </div>
            <div data-aos="fade-left">
                <img src="<?= SITE_URL ?>/assets/images/hero-croissance.jpg" alt="À propos FAJ" 
                     style="border-radius:var(--radius-lg); box-shadow:var(--shadow-lg); width:100%;">
            </div>
        </div>
    </div>
</section>

<!-- Vision & Objectifs -->
<section style="background:var(--light); padding:90px 0;">
    <div class="container">
        <div class="section-header centered" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-bullseye"></i> Domaines</span>
            <h2 class="section-title">6 Domaines d'Intervention <span>Prioritaires</span></h2>
        </div>
        
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:30px;">
            <?php
            $axes = fajDomaines();
            foreach ($axes as $i => $axe):
            ?>
            <div style="background:white; border-radius:var(--radius-lg); padding:35px; box-shadow:var(--shadow); transition:var(--transition);" 
                 data-aos="fade-up" data-aos-delay="<?= $i * 80 ?>"
                 onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='var(--shadow-lg)'"
                 onmouseout="this.style.transform=''; this.style.boxShadow='var(--shadow)'">
                <div style="width:64px; height:64px; background:<?= $axe['color'] ?>1a; border-radius:var(--radius); display:flex; align-items:center; justify-content:center; margin-bottom:20px;">
                    <i class="<?= $axe['icon'] ?>" style="font-size:26px; color:<?= $axe['color'] ?>;"></i>
                </div>
                <h4 style="font-size:17px; font-weight:700; color:var(--primary); margin-bottom:12px;"><?= $axe['titre'] ?></h4>
                <p style="font-size:14px; color:var(--gray); line-height:1.8;"><?= $axe['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Vision 2035 -->
<section style="background:linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); padding:90px 0; position:relative; overflow:hidden;">
    <div class="container" style="position:relative; z-index:1;">
        <div style="text-align:center; max-width:800px; margin:0 auto;" data-aos="fade-up">
            <span class="section-tag">
                <i class="fas fa-flag"></i> Vision 2035
            </span>
            <h2 style="font-family:var(--font-display); font-size:clamp(28px,4vw,46px); color:white; margin-bottom:24px;">
                Notre Ambition pour le <span style="color:var(--secondary-light)">Niger de Demain</span>
            </h2>
            <blockquote style="font-size:18px; color:rgba(255,255,255,0.85); line-height:1.9; font-style:italic; border-left:4px solid var(--secondary); padding-left:24px; text-align:left;">
                "En matière de Justice et des droits humains, les actions de la refondation porteront sur l'amélioration de l'efficacité du système judiciaire pour que la Justice soit réellement indépendante, honnête et pleinement au service de la société."
            </blockquote>
            <p style="color:rgba(255,255,255,0.7); margin-top:24px; font-size:15px;">
                Notre ambition c'est qu'à l'horizon 2035, il y'ait un meilleur accès à la Justice et un système carcéral modernisé. Cependant, cet objectif ne peut être atteint qu'avec la participation de tous.
            </p>
        </div>
    </div>
</section>

<!-- Équipe -->
<?php if (!empty($equipe)): ?>
<section style="background:white; padding:90px 0;">
    <div class="container">
        <div class="section-header centered" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-users"></i> Notre Équipe</span>
            <h2 class="section-title">Les <span>Acteurs du Changement</span></h2>
        </div>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:30px;">
            <?php foreach ($equipe as $m): ?>
            <div style="background:var(--light); border-radius:var(--radius-lg); overflow:hidden; text-align:center; padding-bottom:25px;" data-aos="fade-up">
                <div style="height:200px; overflow:hidden; background:var(--primary);">
                    <?php if ($m['photo']): ?>
                    <img src="<?= UPLOADS_URL ?>/team/<?= $m['photo'] ?>" alt="<?= $m['nom'] ?>" style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                        <div style="width:80px; height:80px; border-radius:50%; background:var(--secondary); display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:700; color:white;">
                            <?= strtoupper(substr($m['nom'], 0, 1)) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div style="padding:20px 20px 0;">
                    <h4 style="font-size:16px; font-weight:700; color:var(--primary);"><?= sanitize($m['nom']) ?></h4>
                    <p style="font-size:13px; color:var(--secondary); font-weight:600; margin-bottom:10px;"><?= sanitize($m['poste']) ?></p>
                    <?php if ($m['biographie']): ?>
                    <p style="font-size:13px; color:var(--gray); line-height:1.7;"><?= sanitize($m['biographie']) ?></p>
                    <?php endif; ?>
                    <?php if ($m['linkedin']): ?>
                    <a href="<?= $m['linkedin'] ?>" target="_blank" style="color:var(--secondary); margin-top:12px; display:inline-block;">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php else: ?>
<section style="background:white; padding:90px 0;">
    <div class="container">
        <div class="section-header centered" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-users"></i> Notre Équipe</span>
            <h2 class="section-title">Les <span>Acteurs du Changement</span></h2>
        </div>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px,1fr)); gap:30px;">
            <?php
            // Composition officielle du Conseil d'Administration (5 membres).
            // Noms individuels non communiqués → fonctions institutionnelles.
            // TODO: photos/noms nominatifs à fournir par le FAJ.
            $equipe_defaut = [];
            foreach (fajConseilAdministration() as $membre) {
                $equipe_defaut[] = ['nom' => $membre, 'poste' => 'Membre du Conseil d\'Administration', 'initiale' => 'FAJ'];
            }
            foreach ($equipe_defaut as $m):
            ?>
            <div style="background:var(--light); border-radius:var(--radius-lg); overflow:hidden; text-align:center; padding-bottom:25px;" data-aos="fade-up">
                <div style="height:180px; overflow:hidden; background:var(--primary); display:flex; align-items:center; justify-content:center;">
                    <div style="width:90px; height:90px; border-radius:50%; background:var(--secondary); display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:700; color:white;">
                        <?= $m['initiale'] ?>
                    </div>
                </div>
                <div style="padding:20px 20px 0;">
                    <h4 style="font-size:15px; font-weight:700; color:var(--primary);"><?= $m['nom'] ?></h4>
                    <p style="font-size:13px; color:var(--secondary); font-weight:600;"><?= $m['poste'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-band">
    <div class="container" data-aos="fade-up">
        <h2>Rejoignez notre combat pour la Justice</h2>
        <p>Faites un don aujourd'hui et participez à la construction d'un Niger plus juste.</p>
        <a href="<?= SITE_URL ?>/don" class="btn btn-white btn-lg">
            <i class="fas fa-heart"></i> Faire un Don
        </a>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
