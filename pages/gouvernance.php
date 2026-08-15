<?php
$page_title = 'Gouvernance';
$meta_description = 'Gouvernance du Fonds d\'Appui à la Justice (FAJ) : Conseil d\'Administration, attributions et cadre institutionnel du Ministère de la Justice et des Droits de l\'Homme du Niger.';
require_once '../includes/config.php';
require_once '../includes/faj_data.php';
require_once '../includes/header.php';

$conseil       = fajConseilAdministration();
$attributions  = fajAttributionsConseil();
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <a href="<?= SITE_URL ?>/a-propos">À Propos</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Gouvernance</span>
        </div>
        <h1>La <span style="color:var(--secondary)">Gouvernance</span> du FAJ</h1>
        <p>Une gouvernance rigoureuse au service d'une justice moderne, transparente et responsable</p>
    </div>
</div>

<!-- Cadre institutionnel -->
<section style="padding:90px 0; background:var(--light);">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-tag">Cadre institutionnel</span>
            <h2 class="section-title">Un Établissement Public sous Tutelle</h2>
            <p class="section-subtitle">
                Le Fonds d'Appui à la Justice est un établissement public à caractère
                administratif, créé par le décret
                <strong>N° 2023-113/PRN/MJ du 26 janvier 2023</strong>, placé sous la
                tutelle du Ministère de la Justice et des Droits de l'Homme.
            </p>
        </div>

        <div class="gouv-pillars">
            <div class="gouv-pillar" data-aos="fade-up" data-aos-delay="0">
                <div class="gouv-pillar-icon"><i class="fas fa-landmark"></i></div>
                <h3>Tutelle</h3>
                <p>Ministère de la Justice et des Droits de l'Homme du Niger.</p>
            </div>
            <div class="gouv-pillar" data-aos="fade-up" data-aos-delay="100">
                <div class="gouv-pillar-icon"><i class="fas fa-users-gear"></i></div>
                <h3>Conseil d'Administration</h3>
                <p>Organe d'orientation et de décision composé de 5 membres institutionnels.</p>
            </div>
            <div class="gouv-pillar" data-aos="fade-up" data-aos-delay="200">
                <div class="gouv-pillar-icon"><i class="fas fa-briefcase"></i></div>
                <h3>Direction Générale</h3>
                <p>Chargée de l'exécution des décisions et de la gestion opérationnelle.</p>
            </div>
        </div>
    </div>
</section>

<!-- Conseil d'Administration -->
<section style="padding:90px 0; background:var(--white);">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-tag">Organe de décision</span>
            <h2 class="section-title">Le Conseil d'Administration</h2>
            <p class="section-subtitle">
                Le Conseil d'Administration réunit les représentants des ministères clés
                afin de garantir une gouvernance concertée et transparente.
            </p>
        </div>

        <div class="conseil-grid">
            <?php foreach ($conseil as $i => $membre): ?>
                <div class="conseil-card" data-aos="fade-up" data-aos-delay="<?= ($i % 3) * 100 ?>">
                    <div class="conseil-num"><?= $i + 1 ?></div>
                    <div class="conseil-icon"><i class="fas fa-user-tie"></i></div>
                    <p><?= htmlspecialchars($membre, ENT_QUOTES) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Attributions -->
<section style="padding:90px 0; background:var(--light);">
    <div class="container">
        <div style="display:grid; grid-template-columns:1fr 1.2fr; gap:60px; align-items:center;" class="gouv-split">
            <div data-aos="fade-right">
                <span class="section-tag">Rôle & responsabilités</span>
                <h2 class="section-title" style="text-align:left;">Attributions du Conseil</h2>
                <p style="color:var(--gray); line-height:1.9; margin-bottom:16px;">
                    Le Conseil d'Administration exerce les pouvoirs d'orientation, de
                    contrôle et de décision nécessaires à la bonne marche du Fonds,
                    dans le respect du cadre légal et réglementaire en vigueur.
                </p>
                <a href="<?= SITE_URL ?>/cadre-financier" class="btn btn-outline-primary">
                    <i class="fas fa-coins"></i> Découvrir le cadre financier
                </a>
            </div>
            <div data-aos="fade-left">
                <ul class="attrib-list">
                    <?php foreach ($attributions as $attr): ?>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><?= htmlspecialchars($attr, ENT_QUOTES) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="engager-cta-band" data-aos="fade-up">
    <div class="container" style="text-align:center;">
        <h2 style="color:var(--white); font-family:var(--font-display); font-size:32px; margin-bottom:14px;">
            Une justice moderne se construit ensemble
        </h2>
        <p style="color:rgba(255,255,255,0.85); max-width:640px; margin:0 auto 28px;">
            Découvrez comment le FAJ mobilise ses ressources et ses partenaires
            pour transformer durablement le système judiciaire nigérien.
        </p>
        <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/cadre-financier" class="btn btn-secondary">
                <i class="fas fa-coins"></i> Cadre financier
            </a>
            <a href="<?= SITE_URL ?>/don" class="btn btn-outline-white">
                <i class="fas fa-heart"></i> Soutenir le FAJ
            </a>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
