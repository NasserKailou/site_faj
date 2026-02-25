<?php
$page_title = 'Politique de Confidentialité';
require_once '../includes/config.php';
require_once '../includes/header.php';
?>
<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Politique de Confidentialité</span>
        </div>
        <h1>Politique de <span style="color:var(--secondary)">Confidentialité</span></h1>
    </div>
</div>
<section style="padding:70px 0; background:var(--light);">
    <div class="container" style="max-width:860px;">
        <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); padding:50px;">
            <h2 style="color:var(--primary); margin-bottom:24px;">Protection de vos données personnelles</h2>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Le <strong>Fonds d'Appui à la Justice (FAJ)</strong> s'engage à protéger vos données personnelles conformément aux lois en vigueur.</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">1. Données collectées</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Nous collectons uniquement les données nécessaires au traitement de vos dons : nom, adresse email, téléphone, montant du don, méthode de paiement.</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">2. Utilisation des données</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Vos données sont utilisées pour : traiter votre don, vous envoyer un reçu, vous tenir informé de l'avancement des projets (si vous y consentez).</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">3. Sécurité</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Vos données sont stockées de façon sécurisée. Les données bancaires ne transitent jamais sur nos serveurs (paiement sécurisé via des partenaires certifiés PCI-DSS).</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">4. Vos droits</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Vous disposez d'un droit d'accès, de rectification et de suppression de vos données. Pour l'exercer, contactez-nous à <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>.</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">5. Cookies</h3>
            <p style="color:var(--gray); line-height:1.9;">Ce site utilise uniquement des cookies de session nécessaires au fonctionnement. Aucun cookie tiers à des fins publicitaires n'est utilisé.</p>
            <div style="margin-top:40px; padding-top:24px; border-top:1px solid var(--light-gray);">
                <a href="<?= SITE_URL ?>/" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
            </div>
        </div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>
