<?php
$page_title = 'Cadre financier';
$meta_description = 'Cadre financier du Fonds d\'Appui à la Justice (FAJ) : sources de ressources et clé de répartition réglementée (Arrêté n° MF/MJ 00011 du 08 février 2021).';
require_once '../includes/config.php';
require_once '../includes/faj_data.php';
require_once '../includes/header.php';

$sources     = fajSourcesRessources();
$repartition = fajCleRepartition();
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <a href="<?= SITE_URL ?>/a-propos">À Propos</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Cadre financier</span>
        </div>
        <h1>Le <span style="color:var(--secondary)">Cadre Financier</span></h1>
        <p>Des ressources diversifiées et encadrées pour financer durablement la justice</p>
    </div>
</div>

<!-- Sources de ressources -->
<section style="padding:90px 0; background:var(--light);">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-tag">Financement</span>
            <h2 class="section-title">Sources de Ressources</h2>
            <p class="section-subtitle">
                Le FAJ mobilise des ressources publiques et des contributions de partenaires
                pour investir dans la modernisation du système judiciaire.
            </p>
        </div>

        <div class="sources-grid">
            <?php foreach ($sources as $i => $src): ?>
                <div class="source-card" data-aos="fade-up" data-aos-delay="<?= ($i % 4) * 80 ?>">
                    <div class="source-icon"><i class="fas fa-hand-holding-dollar"></i></div>
                    <p><?= htmlspecialchars($src, ENT_QUOTES) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Clé de répartition -->
<section style="padding:90px 0; background:var(--white);">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-tag">Arrêté n° MF/MJ 00011 du 08 février 2021</span>
            <h2 class="section-title">Clé de Répartition Réglementée</h2>
            <p class="section-subtitle">
                Une partie des recettes de justice est reversée au Fonds selon une clé
                de répartition fixée par arrêté conjoint des Ministères des Finances
                et de la Justice.
            </p>
        </div>

        <div class="repartition-grid">
            <?php foreach ($repartition as $i => $r): ?>
                <div class="repartition-card" data-aos="zoom-in" data-aos-delay="<?= $i * 120 ?>">
                    <div class="repartition-ring" style="--pct:<?= (int) $r['pct'] ?>;">
                        <span class="repartition-pct"><?= (int) $r['pct'] ?><small>%</small></span>
                    </div>
                    <h3><?= htmlspecialchars($r['label'], ENT_QUOTES) ?></h3>
                    <p><?= htmlspecialchars($r['detail'], ENT_QUOTES) ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <p class="repartition-note" data-aos="fade-up">
            <i class="fas fa-circle-info"></i>
            Les pourcentages sont prélevés sur les recettes concernées et affectés au
            financement des missions du FAJ.
        </p>
    </div>
</section>

<!-- CTA -->
<section class="engager-cta-band" data-aos="fade-up">
    <div class="container" style="text-align:center;">
        <h2 style="color:var(--white); font-family:var(--font-display); font-size:32px; margin-bottom:14px;">
            Investir dans la justice, c'est investir dans l'État de droit
        </h2>
        <p style="color:rgba(255,255,255,0.85); max-width:640px; margin:0 auto 28px;">
            Partenaires techniques et financiers, entreprises citoyennes : rejoignez le FAJ
            pour bâtir une justice moderne au service de tous.
        </p>
        <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/gouvernance" class="btn btn-secondary">
                <i class="fas fa-sitemap"></i> Notre gouvernance
            </a>
            <a href="<?= SITE_URL ?>/don" class="btn btn-outline-white">
                <i class="fas fa-heart"></i> Faire un don
            </a>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
