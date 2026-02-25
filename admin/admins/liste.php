<?php
$page_title = 'Administrateurs';
require_once '../../includes/config.php';
requireAdmin();
try { $pdo = getDB(); $admins = $pdo->query("SELECT id,nom,email,role,actif,derniere_connexion FROM admins ORDER BY id ASC")->fetchAll(); }
catch (Exception $e) { $admins = []; }
include '../../admin/includes/layout-header.php';
?>
<div class="page-header">
    <div><h1 class="page-title"><i class="fas fa-user-shield"></i> Administrateurs</h1><p class="page-subtitle"><?= count($admins) ?> compte(s)</p></div>
</div>
<div class="card"><div class="card-body p-0">
<table class="table"><thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Dernière connexion</th><th>Statut</th></tr></thead>
<tbody>
<?php foreach ($admins as $a): ?>
<tr>
    <td><?= $a['id'] ?></td>
    <td><strong><?= sanitize($a['nom']) ?></strong></td>
    <td><?= sanitize($a['email']) ?></td>
    <td><span class="status-badge <?= $a['role']==='super_admin'?'success':'info' ?>"><?= ucfirst(str_replace('_',' ',$a['role'])) ?></span></td>
    <td style="font-size:12px;color:var(--admin-gray);"><?= $a['derniere_connexion'] ? date('d/m/Y H:i', strtotime($a['derniere_connexion'])) : 'Jamais' ?></td>
    <td><?= $a['actif'] ? '<span class="status-badge success">Actif</span>' : '<span class="status-badge danger">Inactif</span>' ?></td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></div>
<?php include '../../admin/includes/layout-footer.php'; ?>
