<?php
$page_title = 'Don Réussi';
require_once '../includes/config.php';

$reference = sanitize($_GET['ref'] ?? '');

try {
    $pdo = getDB();
    if ($reference) {
        $stmt = $pdo->prepare("SELECT * FROM dons WHERE reference = ?");
        $stmt->execute([$reference]);
        $don = $stmt->fetch();
        
        // Marquer comme complété si en attente
        if ($don && $don['statut'] === 'en_attente') {
            $pdo->prepare("UPDATE dons SET statut='complete' WHERE reference=?")->execute([$reference]);
            $don['statut'] = 'complete';
        }
    }
} catch (Exception $e) {
    $don = null;
}

require_once '../includes/header.php';
?>

<section style="padding:100px 0; background:var(--light); text-align:center;">
    <div class="container">
        <div style="max-width:600px; margin:0 auto; background:white; border-radius:var(--radius-lg); padding:60px 50px; box-shadow:var(--shadow-lg);">
            <div style="width:100px; height:100px; border-radius:50%; background:rgba(40,167,69,0.1); color:var(--success); font-size:50px; display:flex; align-items:center; justify-content:center; margin:0 auto 30px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 style="font-family:var(--font-display); font-size:36px; color:var(--primary); margin-bottom:16px;">
                Merci pour votre Don ! 🎉
            </h1>
            <p style="font-size:16px; color:var(--gray); line-height:1.8; margin-bottom:30px;">
                Votre contribution au Fonds d'Appui à la Justice du Niger a bien été reçue.<br>
                Ensemble, nous construisons un Niger plus juste.
            </p>
            
            <?php if ($don): ?>
            <div style="background:var(--light); border-radius:var(--radius); padding:24px; margin-bottom:30px; text-align:left;">
                <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--light-gray);">
                    <span style="color:var(--gray);">Référence :</span>
                    <strong style="font-family:monospace;"><?= $don['reference'] ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--light-gray);">
                    <span style="color:var(--gray);">Montant :</span>
                    <strong style="color:var(--secondary); font-size:18px;"><?= number_format($don['montant'],0,',',' ') ?> FCFA</strong>
                </div>
                <div style="display:flex; justify-content:space-between; padding:10px 0;">
                    <span style="color:var(--gray);">Date :</span>
                    <span><?= date('d/m/Y à H:i', strtotime($don['created_at'])) ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <p style="font-size:14px; color:var(--gray); margin-bottom:30px;">
                <i class="fas fa-envelope" style="color:var(--secondary);"></i>
                Un reçu officiel a été envoyé à votre adresse email.
            </p>
            
            <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
                <a href="<?= SITE_URL ?>/" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Retour à l'Accueil
                </a>
                <a href="<?= SITE_URL ?>/pages/projets.php" class="btn btn-outline-primary">
                    <i class="fas fa-project-diagram"></i> Voir nos Projets
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
