<?php
$page_title = 'Messages de Contact';
require_once '../../includes/config.php';
requireAdmin();

// Marquer comme lu
if (isset($_GET['lu']) && isset($_GET['id'])) {
    try {
        getDB()->prepare("UPDATE contacts SET lu=1 WHERE id=?")->execute([intval($_GET['id'])]);
    } catch(Exception $e) {}
}

// Supprimer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        getDB()->prepare("DELETE FROM contacts WHERE id=?")->execute([intval($_POST['delete_id'])]);
    } catch(Exception $e) {}
}

try {
    $pdo = getDB();
    $contacts = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
} catch (Exception $e) {
    $contacts = [];
}

include '../../admin/includes/layout-header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-envelope"></i> Messages de Contact</h1>
        <p class="page-subtitle"><?= count(array_filter($contacts, fn($c) => !$c['lu'])) ?> message(s) non lu(s)</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Expéditeur</th><th>Sujet</th><th>Message</th><th>Date</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $c): ?>
                    <tr style="<?= !$c['lu'] ? 'background:rgba(232,135,10,0.03); font-weight:600;' : '' ?>">
                        <td>
                            <strong><?= sanitize($c['nom']) ?></strong><br>
                            <small style="color:var(--admin-gray);"><?= $c['email'] ?></small>
                            <?php if ($c['telephone']): ?><br><small><?= $c['telephone'] ?></small><?php endif; ?>
                        </td>
                        <td><?= sanitize($c['sujet']) ?></td>
                        <td style="max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= sanitize($c['message']) ?></td>
                        <td style="font-size:12px; color:var(--admin-gray);"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                        <td>
                            <?php if (!$c['lu']): ?>
                            <span class="status-badge warning">Non lu</span>
                            <?php else: ?>
                            <span class="status-badge success">Lu</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="?id=<?= $c['id'] ?>&lu=1" class="btn btn-sm btn-outline btn-icon" title="Marquer lu">
                                    <i class="fas fa-check"></i>
                                </a>
                                <a href="mailto:<?= $c['email'] ?>?subject=RE: <?= urlencode($c['sujet']) ?>" class="btn btn-sm btn-primary btn-icon" title="Répondre">
                                    <i class="fas fa-reply"></i>
                                </a>
                                <form method="POST" onsubmit="return confirm('Supprimer ?')" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?= $c['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger btn-icon"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($contacts)): ?>
                    <tr><td colspan="6" style="text-align:center; padding:50px; color:var(--admin-gray);">Aucun message</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../admin/includes/layout-footer.php'; ?>
