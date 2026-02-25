<?php
http_response_code(500);
$bare = !defined('SITE_URL');
if ($bare) require_once __DIR__ . '/../includes/config.php';
if ($bare) require_once __DIR__ . '/../includes/header.php';
?>
<section style="padding:120px 0; background:var(--light); text-align:center;">
    <div class="container">
        <div style="max-width:520px; margin:0 auto;">
            <div style="font-size:120px; font-weight:900; color:var(--light-gray); line-height:1; font-family:var(--font-display);">500</div>
            <h1 style="font-family:var(--font-display); font-size:28px; color:var(--primary); margin-bottom:16px;">Erreur serveur</h1>
            <p style="color:var(--gray); font-size:16px; line-height:1.8; margin-bottom:36px;">Une erreur interne s'est produite. Réessayez dans quelques instants.</p>
            <a href="<?= SITE_URL ?>/" class="btn btn-primary"><i class="fas fa-home"></i> Retour à l'accueil</a>
        </div>
    </div>
</section>
<?php if ($bare) require_once __DIR__ . '/../includes/footer.php'; ?>
