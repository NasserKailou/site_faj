<?php
$page_title = 'Conditions Générales';
require_once '../includes/config.php';
require_once '../includes/header.php';
?>
<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Conditions Générales</span>
        </div>
        <h1>Conditions <span style="color:var(--secondary)">Générales</span></h1>
    </div>
</div>
<section style="padding:70px 0; background:var(--light);">
    <div class="container" style="max-width:860px;">
        <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); padding:50px;">
            <?php
            try {
                $contenu = getSiteParam('page_conditions','');
            } catch(Exception $e) { $contenu = ''; }
            if ($contenu):
                echo $contenu;
            else: ?>
            <h2 style="color:var(--primary); margin-bottom:24px;">Conditions Générales de Don</h2>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">En effectuant un don au <strong>Fonds d'Appui à la Justice (FAJ) du Niger</strong>, vous acceptez les présentes conditions générales.</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">1. Nature des dons</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Les dons effectués sur cette plateforme sont des actes volontaires et irrévocables destinés à soutenir les programmes du FAJ Niger.</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">2. Utilisation des fonds</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Les fonds collectés sont exclusivement utilisés pour financer les projets de modernisation du système judiciaire nigérien décrits sur ce site.</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">3. Reçu de don</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Un reçu officiel vous est envoyé par email après chaque don confirmé. Ce reçu peut être utilisé à des fins fiscales selon la législation en vigueur.</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">4. Remboursement</h3>
            <p style="color:var(--gray); line-height:1.9; margin-bottom:20px;">Les dons sont en principe définitifs. En cas d'erreur manifeste, contactez-nous dans les 48h à <?= SITE_EMAIL ?>.</p>
            <h3 style="color:var(--primary); margin:30px 0 12px;">5. Contact</h3>
            <p style="color:var(--gray); line-height:1.9;">Pour toute question : <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></p>
            <?php endif; ?>
            <div style="margin-top:40px; padding-top:24px; border-top:1px solid var(--light-gray);">
                <a href="<?= SITE_URL ?>/" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
            </div>
        </div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>
