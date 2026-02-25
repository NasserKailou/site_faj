<?php
$page_title = 'Don Annulé';
require_once '../includes/config.php';
require_once '../includes/header.php';
?>
<section style="padding:100px 0; background:var(--light); text-align:center;">
    <div class="container">
        <div style="max-width:560px; margin:0 auto; background:white; border-radius:var(--radius-lg); padding:60px 50px; box-shadow:var(--shadow-lg);">
            <div style="width:100px; height:100px; border-radius:50%; background:rgba(220,53,69,0.1); color:#dc3545; font-size:50px; display:flex; align-items:center; justify-content:center; margin:0 auto 30px;">
                <i class="fas fa-times-circle"></i>
            </div>
            <h1 style="font-family:var(--font-display); font-size:32px; color:var(--primary); margin-bottom:16px;">Don Annulé</h1>
            <p style="font-size:16px; color:var(--gray); line-height:1.8; margin-bottom:36px;">
                Votre transaction a été annulée. Aucun montant n'a été prélevé.<br>
                Vous pouvez réessayer à tout moment.
            </p>
            <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
                <a href="<?= SITE_URL ?>/don" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Réessayer
                </a>
                <a href="<?= SITE_URL ?>/" class="btn btn-outline">
                    <i class="fas fa-home"></i> Accueil
                </a>
            </div>
        </div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>
