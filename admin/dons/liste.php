<?php
$page_title = 'Liste des Dons';
require_once '../../includes/config.php';
requireAdmin();

// Filtres
$statut_filter = sanitize($_GET['statut'] ?? '');
$search = sanitize($_GET['q'] ?? '');
$page = max(1, intval($_GET['p'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

try {
    $pdo = getDB();
    
    $where = ['1=1'];
    $params = [];
    
    if ($statut_filter) { $where[] = 'statut = ?'; $params[] = $statut_filter; }
    if ($search) {
        $where[] = '(donateur_nom LIKE ? OR donateur_email LIKE ? OR reference LIKE ?)';
        $s = '%' . $search . '%';
        $params = array_merge($params, [$s, $s, $s]);
    }
    
    $whereStr = implode(' AND ', $where);
    
    $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM dons WHERE $whereStr");
    $total_stmt->execute($params);
    $total = $total_stmt->fetchColumn();
    $total_pages = ceil($total / $per_page);
    
    $params[] = $per_page;
    $params[] = $offset;
    
    $dons_stmt = $pdo->prepare("SELECT d.*, p.titre as projet_titre FROM dons d LEFT JOIN projets p ON d.projet_id = p.id WHERE $whereStr ORDER BY d.created_at DESC LIMIT ? OFFSET ?");
    $dons_stmt->execute($params);
    $dons = $dons_stmt->fetchAll();
    
    // Totaux
    $total_collecte = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE statut='complete'")->fetchColumn();
    
} catch (Exception $e) {
    $dons = []; $total = 0; $total_pages = 0; $total_collecte = 0;
}

include '../../admin/includes/layout-header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-hand-holding-heart"></i> Gestion des Dons</h1>
        <p class="page-subtitle">Total collecté : <strong style="color:var(--admin-secondary);"><?= number_format($total_collecte,0,',',' ') ?> FCFA</strong></p>
    </div>
    <a href="<?= SITE_URL ?>/admin/dons/export" class="btn btn-outline">
        <i class="fas fa-file-excel"></i> Exporter CSV
    </a>
</div>

<!-- Filtres -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <div style="flex:1; min-width:200px;">
                <label style="font-size:12px; font-weight:600; color:var(--admin-gray); display:block; margin-bottom:6px;">Rechercher</label>
                <input type="text" name="q" value="<?= $search ?>" class="form-control" placeholder="Nom, email, référence...">
            </div>
            <div>
                <label style="font-size:12px; font-weight:600; color:var(--admin-gray); display:block; margin-bottom:6px;">Statut</label>
                <select name="statut" class="form-control">
                    <option value="">Tous</option>
                    <option value="complete" <?= $statut_filter === 'complete' ? 'selected' : '' ?>>Complétés</option>
                    <option value="en_attente" <?= $statut_filter === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="echoue" <?= $statut_filter === 'echoue' ? 'selected' : '' ?>>Échoués</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrer</button>
            <a href="<?= SITE_URL ?>/admin/dons" class="btn btn-outline">Réinitialiser</a>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> <?= $total ?> don(s) trouvé(s)</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Donateur</th>
                        <th>Montant</th>
                        <th>Méthode</th>
                        <th>Projet</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dons)): ?>
                    <tr><td colspan="8" style="text-align:center; padding:50px; color:var(--admin-gray);">
                        <i class="fas fa-inbox" style="font-size:40px; display:block; margin-bottom:12px; opacity:0.3;"></i>
                        Aucun don trouvé
                    </td></tr>
                    <?php else: ?>
                    <?php foreach ($dons as $don):
                        $statuts = [
                            'complete'=>'<span class="status-badge success">Complété</span>',
                            'en_attente'=>'<span class="status-badge warning">En attente</span>',
                            'echoue'=>'<span class="status-badge danger">Échoué</span>',
                            'rembourse'=>'<span class="status-badge info">Remboursé</span>',
                        ];
                        $methodes = [
                            'orange_money'=>'<span class="badge orange"><i class="fas fa-mobile-alt"></i> Orange Money</span>',
                            'moov_money'=>'<span class="badge blue"><i class="fas fa-mobile-alt"></i> Moov Money</span>',
                            'carte_visa'=>'<span class="badge navy"><i class="fab fa-cc-visa"></i> Visa</span>',
                            'carte_mastercard'=>'<span class="badge red"><i class="fab fa-cc-mastercard"></i> Mastercard</span>',
                        ];
                    ?>
                    <tr>
                        <td><code style="font-size:12px;"><?= $don['reference'] ?></code></td>
                        <td>
                            <?php if ($don['anonyme']): ?>
                            <em style="color:var(--admin-gray);">Anonyme</em>
                            <?php else: ?>
                            <strong><?= sanitize($don['donateur_nom']) ?></strong>
                            <small style="display:block; color:var(--admin-gray); font-size:11px;"><?= $don['donateur_email'] ?></small>
                            <?php if ($don['donateur_pays']): ?>
                            <small style="color:var(--admin-gray); font-size:11px;"><?= $don['donateur_pays'] ?></small>
                            <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td><strong style="color:var(--admin-secondary); font-size:15px;"><?= number_format($don['montant'],0,',',' ') ?> FCFA</strong></td>
                        <td><?= $methodes[$don['methode_paiement']] ?? $don['methode_paiement'] ?></td>
                        <td style="font-size:13px;"><?= $don['projet_titre'] ? sanitize($don['projet_titre']) : '<em style="color:var(--admin-gray);">Don général</em>' ?></td>
                        <td><?= $statuts[$don['statut']] ?? $don['statut'] ?></td>
                        <td style="font-size:12px; color:var(--admin-gray);"><?= date('d/m/Y H:i', strtotime($don['created_at'])) ?></td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="<?= SITE_URL ?>/admin/dons/detail?id=<?= $don['id'] ?>" class="btn btn-sm btn-outline btn-icon" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($don['statut'] === 'en_attente'): ?>
                                <button onclick="confirmerDon(<?= $don['id'] ?>)" class="btn btn-sm btn-success btn-icon" title="Confirmer">
                                    <i class="fas fa-check"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div style="padding:16px 24px; border-top:1px solid var(--admin-border); display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:13px; color:var(--admin-gray);">Page <?= $page ?> / <?= $total_pages ?></span>
        <div style="display:flex; gap:6px;">
            <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
            <a href="?p=<?= $i ?>&statut=<?= $statut_filter ?>&q=<?= $search ?>" 
               class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-outline' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function confirmerDon(id) {
    if (!confirm('Confirmer ce don comme complété ?')) return;
    fetch('<?= SITE_URL ?>/api/admin-actions.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'confirm_don', id: id})
    }).then(r => r.json()).then(d => {
        if (d.success) location.reload();
        else alert(d.message);
    });
}
</script>

<?php include '../../admin/includes/layout-footer.php'; ?>
