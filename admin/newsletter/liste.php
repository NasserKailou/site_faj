<?php
$page_title = 'Newsletter';
require_once '../../includes/config.php';
requireAdmin();
try { $pdo = getDB(); $abonnes = $pdo->query("SELECT * FROM newsletter ORDER BY created_at DESC")->fetchAll(); }
catch (Exception $e) { $abonnes = []; }
include '../../admin/includes/layout-header.php';
?>
<div class="page-header">
    <div><h1 class="page-title"><i class="fas fa-at"></i> Newsletter</h1><p class="page-subtitle"><?= count($abonnes) ?> abonné(s)</p></div>
</div>
<div class="card"><div class="card-body p-0">
<table class="table"><thead><tr><th>#</th><th>Email</th><th>Nom</th><th>Date</th><th>Statut</th></tr></thead>
<tbody>
<?php if (empty($abonnes)): ?>
<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--admin-gray);"><i class="fas fa-at" style="font-size:40px;display:block;margin-bottom:12px;opacity:0.3;"></i>Aucun abonné</td></tr>
<?php else: ?>
<?php foreach ($abonnes as $a): ?>
<tr><td><?= $a['id'] ?></td><td><?= sanitize($a['email']) ?></td><td><?= sanitize($a['nom'] ?? '') ?></td><td style="font-size:12px;"><?= date('d/m/Y', strtotime($a['created_at'])) ?></td><td><?= $a['actif'] ? '<span class="status-badge success">Actif</span>' : '<span class="status-badge danger">Désabonné</span>' ?></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table>
</div></div>
<?php include '../../admin/includes/layout-footer.php'; ?>
