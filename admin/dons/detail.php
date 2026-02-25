<?php
$page_title = 'Détail du Don';
require_once '../../includes/config.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) { redirect(SITE_URL . '/admin/dons'); }

$msg = $err = '';

// Traitement actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        $pdo = getDB();
        if ($action === 'confirmer') {
            $pdo->prepare("UPDATE dons SET statut='complete', updated_at=CURRENT_TIMESTAMP WHERE id=?")->execute([$id]);
            $msg = 'Don marqué comme complété.';
        } elseif ($action === 'annuler') {
            $pdo->prepare("UPDATE dons SET statut='echoue', updated_at=CURRENT_TIMESTAMP WHERE id=?")->execute([$id]);
            $msg = 'Don marqué comme échoué.';
        } elseif ($action === 'rembourser') {
            $pdo->prepare("UPDATE dons SET statut='rembourse', updated_at=CURRENT_TIMESTAMP WHERE id=?")->execute([$id]);
            $msg = 'Don marqué comme remboursé.';
        }
    } catch (Exception $e) {
        $err = 'Erreur : ' . $e->getMessage();
    }
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT d.*, p.titre as projet_titre FROM dons d LEFT JOIN projets p ON d.projet_id = p.id WHERE d.id = ?");
    $stmt->execute([$id]);
    $don = $stmt->fetch();
    if (!$don) { redirect(SITE_URL . '/admin/dons'); }
} catch (Exception $e) {
    redirect(SITE_URL . '/admin/dons');
}

$statuts_labels = [
    'complete'   => ['label' => 'Complété',   'class' => 'success'],
    'en_attente' => ['label' => 'En attente', 'class' => 'warning'],
    'echoue'     => ['label' => 'Échoué',     'class' => 'danger'],
    'rembourse'  => ['label' => 'Remboursé',  'class' => 'info'],
];

$methodes_labels = [
    'orange_money'     => 'Orange Money',
    'moov_money'       => 'Moov Money',
    'carte_visa'       => 'Carte Visa',
    'carte_mastercard' => 'Carte Mastercard',
    'virement'         => 'Virement bancaire',
    'autre'            => 'Autre',
];

include '../../admin/includes/layout-header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-hand-holding-heart"></i> Détail du Don</h1>
        <p class="page-subtitle">Référence : <strong><?= $don['reference'] ?></strong></p>
    </div>
    <a href="<?= SITE_URL ?>/admin/dons" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Retour à la liste
    </a>
</div>

<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $err ?></div><?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 340px; gap:24px; align-items:start;">

    <!-- Informations principales -->
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-info-circle"></i> Informations du Don</h3></div>
        <div class="card-body">
            <table style="width:100%; border-collapse:collapse;">
                <?php
                $rows = [
                    ['Référence',       '<code>' . $don['reference'] . '</code>'],
                    ['Montant',         '<strong style="font-size:22px;color:var(--admin-secondary);">' . number_format($don['montant'],0,',',' ') . ' ' . $don['devise'] . '</strong>'],
                    ['Méthode',         $methodes_labels[$don['methode_paiement']] ?? $don['methode_paiement']],
                    ['Transaction ID',  $don['transaction_id'] ?: '<em style="color:var(--admin-gray)">–</em>'],
                    ['Statut',          '<span class="status-badge ' . ($statuts_labels[$don['statut']]['class'] ?? '') . '">' . ($statuts_labels[$don['statut']]['label'] ?? $don['statut']) . '</span>'],
                    ['Projet',          $don['projet_titre'] ? sanitize($don['projet_titre']) : '<em style="color:var(--admin-gray)">Don général</em>'],
                    ['Date de don',     date('d/m/Y à H:i', strtotime($don['created_at']))],
                    ['Dernière MAJ',    date('d/m/Y à H:i', strtotime($don['updated_at']))],
                ];
                foreach ($rows as [$label, $val]):
                ?>
                <tr style="border-bottom:1px solid var(--admin-border);">
                    <td style="padding:14px 0;font-size:13px;color:var(--admin-gray);font-weight:600;width:180px;"><?= $label ?></td>
                    <td style="padding:14px 0;font-size:14px;"><?= $val ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <?php if ($don['message']): ?>
            <div style="margin-top:20px;padding:16px;background:var(--admin-bg);border-radius:var(--admin-radius);border-left:3px solid var(--admin-secondary);">
                <p style="font-size:12px;font-weight:700;color:var(--admin-gray);margin-bottom:8px;">MESSAGE DU DONATEUR</p>
                <p style="font-size:14px;line-height:1.7;"><?= sanitize($don['message']) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Colonne droite -->
    <div>
        <!-- Donateur -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><h3><i class="fas fa-user"></i> Donateur</h3></div>
            <div class="card-body">
                <?php if ($don['anonyme']): ?>
                <p style="color:var(--admin-gray); font-style:italic;">Don anonyme</p>
                <?php else: ?>
                <p style="font-weight:700;font-size:16px;color:var(--admin-primary);margin-bottom:8px;"><?= sanitize($don['donateur_nom']) ?></p>
                <p style="font-size:13px;color:var(--admin-gray);margin-bottom:4px;"><i class="fas fa-envelope"></i> <?= $don['donateur_email'] ?></p>
                <?php if ($don['donateur_telephone']): ?>
                <p style="font-size:13px;color:var(--admin-gray);margin-bottom:4px;"><i class="fas fa-phone"></i> <?= $don['donateur_telephone'] ?></p>
                <?php endif; ?>
                <p style="font-size:13px;color:var(--admin-gray);"><i class="fas fa-map-marker-alt"></i> <?= sanitize($don['donateur_pays']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-cogs"></i> Actions</h3></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
                <?php if ($don['statut'] === 'en_attente'): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="confirmer">
                    <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;">
                        <i class="fas fa-check"></i> Confirmer le paiement
                    </button>
                </form>
                <form method="POST" onsubmit="return confirm('Marquer ce don comme échoué ?')">
                    <input type="hidden" name="action" value="annuler">
                    <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">
                        <i class="fas fa-times"></i> Marquer comme échoué
                    </button>
                </form>
                <?php elseif ($don['statut'] === 'complete'): ?>
                <form method="POST" onsubmit="return confirm('Rembourser ce don ?')">
                    <input type="hidden" name="action" value="rembourser">
                    <button type="submit" class="btn btn-outline" style="width:100%;justify-content:center;">
                        <i class="fas fa-undo"></i> Rembourser
                    </button>
                </form>
                <?php else: ?>
                <p style="color:var(--admin-gray);font-size:13px;text-align:center;padding:10px 0;">Aucune action disponible pour ce statut.</p>
                <?php endif; ?>
                <a href="mailto:<?= $don['donateur_email'] ?>" class="btn btn-outline" style="width:100%;justify-content:center;">
                    <i class="fas fa-envelope"></i> Contacter le donateur
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../../admin/includes/layout-footer.php'; ?>
