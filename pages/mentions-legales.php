<?php
$page_title = 'Mentions Légales';
require_once '../includes/config.php';
require_once '../includes/header.php';
?>
<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Mentions Légales</span>
        </div>
        <h1>Mentions <span style="color:var(--secondary)">Légales</span></h1>
    </div>
</div>
<section style="padding:70px 0; background:var(--light);">
    <div class="container" style="max-width:860px;">
        <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); padding:50px;">
            <h2 style="color:var(--primary); margin-bottom:24px;">Informations légales</h2>
            <h3 style="color:var(--primary); margin:30px 0 12px;">Éditeur du site</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;"><strong>Fonds d'Appui à la Justice (FAJ) du Niger</strong><br>
            Adresse : <?= SITE_ADDRESS ?><br>
            Email : <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a><br>
            Téléphone : <?= SITE_PHONE ?></p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">Hébergement</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Ce site est hébergé par un prestataire d'hébergement web professionnel.</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">Propriété intellectuelle</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Tous les contenus présents sur ce site (textes, images, logos) sont la propriété exclusive du FAJ Niger. Toute reproduction est interdite sans autorisation préalable.</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">Responsabilité</h3>
            <p style="color:var(--gray); line-height:1.9;">Le FAJ Niger s'efforce d'assurer l'exactitude des informations diffusées sur ce site, mais ne peut garantir leur exhaustivité.</p>
            <div style="margin-top:40px; padding-top:24px; border-top:1px solid var(--light-gray);">
                <a href="<?= SITE_URL ?>/" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
            </div>
        </div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>
