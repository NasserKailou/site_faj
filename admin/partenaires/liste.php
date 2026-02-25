<?php
$page_title = 'Partenaires';
require_once '../../includes/config.php';
requireAdmin();

$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        $pdo = getDB();
        if ($action === 'ajouter' || $action === 'modifier') {
            $nom     = sanitize($_POST['nom'] ?? '');
            $type    = sanitize($_POST['type'] ?? 'national');
            $site    = sanitize($_POST['site_web'] ?? '');
            $ordre   = (int)($_POST['ordre'] ?? 0);
            $actif   = isset($_POST['actif']) ? 1 : 0;

            // Upload logo
            $logo = $_POST['logo_actuel'] ?? null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp','svg'])) {
                    $filename = 'part_' . time() . '.' . $ext;
                    if (!is_dir(UPLOADS_PATH . '/partenaires')) mkdir(UPLOADS_PATH . '/partenaires', 0755, true);
                    move_uploaded_file($_FILES['logo']['tmp_name'], UPLOADS_PATH . '/partenaires/' . $filename);
                    $logo = $filename;
                }
            }

            if (empty($nom)) { $err = 'Le nom est obligatoire.'; }
            else {
                if ($action === 'ajouter') {
                    $pdo->prepare("INSERT INTO partenaires (nom,logo,site_web,type,ordre,actif) VALUES (?,?,?,?,?,?)")
                        ->execute([$nom,$logo,$site,$type,$ordre,$actif]);
                    $msg = 'Partenaire ajouté.';
                } else {
                    $id = (int)($_POST['id'] ?? 0);
                    $pdo->prepare("UPDATE partenaires SET nom=?,logo=?,site_web=?,type=?,ordre=?,actif=? WHERE id=?")
                        ->execute([$nom,$logo,$site,$type,$ordre,$actif,$id]);
                    $msg = 'Partenaire modifié.';
                }
            }
        } elseif ($action === 'supprimer') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM partenaires WHERE id=?")->execute([$id]);
            $msg = 'Partenaire supprimé.';
        }
    } catch (Exception $e) { $err = 'Erreur : ' . $e->getMessage(); }
}

try { $pdo = getDB(); $partenaires = $pdo->query("SELECT * FROM partenaires ORDER BY ordre ASC, id ASC")->fetchAll(); }
catch (Exception $e) { $partenaires = []; }

include '../../admin/includes/layout-header.php';
?>
<div class="page-header">
    <div><h1 class="page-title"><i class="fas fa-handshake"></i> Partenaires</h1><p class="page-subtitle"><?= count($partenaires) ?> partenaire(s)</p></div>
    <button class="btn btn-primary" onclick="document.getElementById('modalPart').style.display='flex'">
        <i class="fas fa-plus"></i> Ajouter
    </button>
</div>

<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $err ?></div><?php endif; ?>

<div class="card"><div class="card-body p-0">
<table class="table">
    <thead><tr><th>#</th><th>Logo</th><th>Nom</th><th>Type</th><th>Site</th><th>Ordre</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php if (empty($partenaires)): ?>
    <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--admin-gray);"><i class="fas fa-handshake" style="font-size:40px;display:block;margin-bottom:12px;opacity:0.3;"></i>Aucun partenaire</td></tr>
    <?php else: ?>
    <?php foreach ($partenaires as $p): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td>
            <?php if ($p['logo']): ?>
            <img src="<?= UPLOADS_URL ?>/partenaires/<?= $p['logo'] ?>" style="height:36px;max-width:80px;object-fit:contain;border-radius:4px;">
            <?php else: ?><span style="color:var(--admin-gray);font-size:12px;">—</span><?php endif; ?>
        </td>
        <td><strong><?= sanitize($p['nom']) ?></strong></td>
        <td><span class="badge orange"><?= ucfirst($p['type'] ?? '') ?></span></td>
        <td><?= $p['site_web'] ? '<a href="'.htmlspecialchars($p['site_web']).'" target="_blank" style="font-size:12px;">Visiter</a>' : '—' ?></td>
        <td><?= $p['ordre'] ?></td>
        <td><?= $p['actif'] ? '<span class="status-badge success">Actif</span>' : '<span class="status-badge danger">Inactif</span>' ?></td>
        <td>
            <button onclick='editPart(<?= json_encode($p) ?>)' class="btn btn-sm btn-outline btn-icon" title="Modifier"><i class="fas fa-edit"></i></button>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ?')">
                <input type="hidden" name="action" value="supprimer">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger btn-icon"><i class="fas fa-trash"></i></button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div></div>

<!-- Modal Ajout/Modification -->
<div id="modalPart" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:var(--admin-radius);padding:36px;width:520px;max-width:95vw;max-height:90vh;overflow-y:auto;">
        <h3 id="partModalTitle" style="margin-bottom:24px;color:var(--admin-primary);"><i class="fas fa-handshake"></i> Ajouter un partenaire</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="partAction" value="ajouter">
            <input type="hidden" name="id" id="partId" value="">
            <input type="hidden" name="logo_actuel" id="partLogoActuel" value="">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Nom *</label><input type="text" name="nom" id="pNom" class="form-control" required></div>
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Type</label>
                    <select name="type" id="pType" class="form-control">
                        <option value="international">International</option>
                        <option value="national" selected>National</option>
                        <option value="institutionnel">Institutionnel</option>
                        <option value="prive">Privé</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:16px;"><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Site web</label><input type="url" name="site_web" id="pSite" class="form-control" placeholder="https://..."></div>
            <div style="margin-bottom:16px;"><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Logo (jpg, png, svg)</label><input type="file" name="logo" class="form-control" accept="image/*"></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Ordre d'affichage</label><input type="number" name="ordre" id="pOrdre" class="form-control" value="0"></div>
                <div style="display:flex;align-items:center;gap:8px;padding-top:22px;"><input type="checkbox" name="actif" id="pActif" checked><label for="pActif" style="font-size:14px;font-weight:600;">Actif</label></div>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button type="button" onclick="closePart()" class="btn btn-outline">Annuler</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function editPart(p) {
    document.getElementById('partAction').value = 'modifier';
    document.getElementById('partModalTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier le partenaire';
    document.getElementById('partId').value = p.id;
    document.getElementById('partLogoActuel').value = p.logo || '';
    document.getElementById('pNom').value = p.nom;
    document.getElementById('pType').value = p.type || 'national';
    document.getElementById('pSite').value = p.site_web || '';
    document.getElementById('pOrdre').value = p.ordre;
    document.getElementById('pActif').checked = p.actif == 1;
    document.getElementById('modalPart').style.display = 'flex';
}
function closePart() {
    document.getElementById('modalPart').style.display = 'none';
    document.getElementById('partAction').value = 'ajouter';
    document.getElementById('partModalTitle').innerHTML = '<i class="fas fa-handshake"></i> Ajouter un partenaire';
    document.getElementById('partId').value = '';
}
</script>

<?php include '../../admin/includes/layout-footer.php'; ?>
