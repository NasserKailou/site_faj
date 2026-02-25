<?php
$page_title = 'Gestion de l\'Équipe';
require_once '../../includes/config.php';
requireAdmin();

// Traitement des actions POST
$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        $pdo = getDB();
        if ($action === 'ajouter' || $action === 'modifier') {
            $nom    = sanitize($_POST['nom'] ?? '');
            $poste  = sanitize($_POST['poste'] ?? '');
            $bio    = sanitize($_POST['biographie'] ?? '');
            $email  = sanitize($_POST['email'] ?? '');
            $linkedin = sanitize($_POST['linkedin'] ?? '');
            $ordre  = (int)($_POST['ordre'] ?? 0);
            $actif  = isset($_POST['actif']) ? 1 : 0;

            if (empty($nom) || empty($poste)) { $err = 'Nom et poste obligatoires.'; }
            else {
                if ($action === 'ajouter') {
                    $pdo->prepare("INSERT INTO equipe (nom,poste,biographie,email,linkedin,ordre,actif) VALUES (?,?,?,?,?,?,?)")
                        ->execute([$nom,$poste,$bio,$email,$linkedin,$ordre,$actif]);
                    $msg = 'Membre ajouté avec succès.';
                } else {
                    $id = (int)($_POST['id'] ?? 0);
                    $pdo->prepare("UPDATE equipe SET nom=?,poste=?,biographie=?,email=?,linkedin=?,ordre=?,actif=? WHERE id=?")
                        ->execute([$nom,$poste,$bio,$email,$linkedin,$ordre,$actif,$id]);
                    $msg = 'Membre modifié avec succès.';
                }
            }
        } elseif ($action === 'supprimer') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM equipe WHERE id=?")->execute([$id]);
            $msg = 'Membre supprimé.';
        }
    } catch (Exception $e) {
        $err = 'Erreur : ' . ($e->getMessage());
    }
}

try {
    $pdo = getDB();
    $membres = $pdo->query("SELECT * FROM equipe ORDER BY ordre ASC, id ASC")->fetchAll();
} catch (Exception $e) { $membres = []; }

include '../../admin/includes/layout-header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-users"></i> Gestion de l'Équipe</h1>
        <p class="page-subtitle"><?= count($membres) ?> membre(s) enregistré(s)</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('modalAjout').style.display='flex'">
        <i class="fas fa-plus"></i> Ajouter un membre
    </button>
</div>

<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $err ?></div><?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <table class="table">
            <thead><tr><th>#</th><th>Nom</th><th>Poste</th><th>Ordre</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($membres)): ?>
            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--admin-gray);"><i class="fas fa-users" style="font-size:40px;display:block;margin-bottom:12px;opacity:0.3;"></i>Aucun membre</td></tr>
            <?php else: ?>
            <?php foreach ($membres as $m): ?>
            <tr>
                <td><?= $m['id'] ?></td>
                <td><strong><?= sanitize($m['nom']) ?></strong><?php if($m['email']): ?><br><small style="color:var(--admin-gray)"><?= $m['email'] ?></small><?php endif;?></td>
                <td><?= sanitize($m['poste']) ?></td>
                <td><?= $m['ordre'] ?></td>
                <td><?= $m['actif'] ? '<span class="status-badge success">Actif</span>' : '<span class="status-badge danger">Inactif</span>' ?></td>
                <td>
                    <button onclick='editMembre(<?= json_encode($m) ?>)' class="btn btn-sm btn-outline btn-icon" title="Modifier"><i class="fas fa-edit"></i></button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce membre ?')">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Supprimer"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout/Modification -->
<div id="modalAjout" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:var(--admin-radius);padding:36px;width:520px;max-width:95vw;max-height:90vh;overflow-y:auto;">
        <h3 id="modalTitle" style="margin-bottom:24px;color:var(--admin-primary);font-size:18px;"><i class="fas fa-user-plus"></i> Ajouter un membre</h3>
        <form method="POST">
            <input type="hidden" name="action" id="formAction" value="ajouter">
            <input type="hidden" name="id" id="membreId" value="">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Nom *</label><input type="text" name="nom" id="fNom" class="form-control" required></div>
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Poste *</label><input type="text" name="poste" id="fPoste" class="form-control" required></div>
            </div>
            <div style="margin-bottom:16px;"><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Biographie</label><textarea name="biographie" id="fBio" class="form-control" rows="3"></textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Email</label><input type="email" name="email" id="fEmail" class="form-control"></div>
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">LinkedIn</label><input type="url" name="linkedin" id="fLinkedin" class="form-control"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Ordre d'affichage</label><input type="number" name="ordre" id="fOrdre" class="form-control" value="0"></div>
                <div style="display:flex;align-items:center;gap:8px;padding-top:22px;"><input type="checkbox" name="actif" id="fActif" checked><label for="fActif" style="font-size:14px;font-weight:600;">Membre actif</label></div>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('modalAjout').style.display='none'" class="btn btn-outline">Annuler</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function editMembre(m) {
    document.getElementById('formAction').value = 'modifier';
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier le membre';
    document.getElementById('membreId').value = m.id;
    document.getElementById('fNom').value = m.nom;
    document.getElementById('fPoste').value = m.poste;
    document.getElementById('fBio').value = m.biographie || '';
    document.getElementById('fEmail').value = m.email || '';
    document.getElementById('fLinkedin').value = m.linkedin || '';
    document.getElementById('fOrdre').value = m.ordre;
    document.getElementById('fActif').checked = m.actif == 1;
    document.getElementById('modalAjout').style.display = 'flex';
}
</script>

<?php include '../../admin/includes/layout-footer.php'; ?>
