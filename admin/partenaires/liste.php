<?php
$page_title = 'Partenaires';
require_once '../../includes/config.php';
requireAdmin();
try { $pdo = getDB(); $partenaires = $pdo->query("SELECT * FROM partenaires ORDER BY ordre ASC")->fetchAll(); }
catch (Exception $e) { $partenaires = []; }
include '../../admin/includes/layout-header.php';
?>
<div class="page-header">
    <div><h1 class="page-title"><i class="fas fa-handshake"></i> Partenaires</h1><p class="page-subtitle"><?= count($partenaires) ?> partenaire(s)</p></div>
</div>
<div class="card"><div class="card-body p-0">
<table class="table"><thead><tr><th>#</th><th>Nom</th><th>Type</th><th>Ordre</th><th>Statut</th></tr></thead>
<tbody>
<?php if (empty($partenaires)): ?>
<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--admin-gray);"><i class="fas fa-handshake" style="font-size:40px;display:block;margin-bottom:12px;opacity:0.3;"></i>Aucun partenaire</td></tr>
<?php else: ?>
<?php foreach ($partenaires as $p): ?>
<tr><td><?= $p['id'] ?></td><td><strong><?= sanitize($p['nom']) ?></strong></td><td><?= $p['type'] ?></td><td><?= $p['ordre'] ?></td><td><?= $p['actif'] ? '<span class="status-badge success">Actif</span>' : '<span class="status-badge danger">Inactif</span>' ?></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table>
</div></div>
<?php include '../../admin/includes/layout-footer.php'; ?>
