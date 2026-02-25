<?php
http_response_code(404);
$bare = !defined('SITE_URL');
if ($bare) require_once __DIR__ . '/../includes/config.php';
if ($bare) require_once __DIR__ . '/../includes/header.php';
?>
<section style="padding:120px 0; background:var(--light); text-align:center;">
    <div class="container">
        <div style="max-width:520px; margin:0 auto;">
            <div style="font-size:120px; font-weight:900; color:var(--light-gray); line-height:1; font-family:var(--font-display);">404</div>
            <h1 style="font-family:var(--font-display); font-size:28px; color:var(--primary); margin-bottom:16px;">Page introuvable</h1>
            <p style="color:var(--gray); font-size:16px; line-height:1.8; margin-bottom:36px;">La page que vous cherchez n'existe pas ou a été déplacée.</p>
            <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
                <a href="<?= SITE_URL ?>/" class="btn btn-primary"><i class="fas fa-home"></i> Accueil</a>
                <a href="<?= SITE_URL ?>/contact" class="btn btn-outline"><i class="fas fa-envelope"></i> Contact</a>
            </div>
        </div>
    </div>
</section>
<?php if ($bare) require_once __DIR__ . '/../includes/footer.php'; ?>
