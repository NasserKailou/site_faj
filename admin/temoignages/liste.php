<?php
$page_title = 'Témoignages';
require_once '../../includes/config.php';
requireAdmin();

$msg = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        $pdo = getDB();
        if ($action === 'ajouter' || $action === 'modifier') {
            $nom    = sanitize($_POST['nom'] ?? '');
            $poste  = sanitize($_POST['poste'] ?? '');
            $contenu= sanitize($_POST['contenu'] ?? '');
            $note   = max(1, min(5, (int)($_POST['note'] ?? 5)));
            $actif  = isset($_POST['actif']) ? 1 : 0;
            if (empty($nom) || empty($contenu)) { $err = 'Nom et témoignage obligatoires.'; }
            else {
                if ($action === 'ajouter') {
                    $pdo->prepare("INSERT INTO temoignages (nom,poste,contenu,note,actif) VALUES (?,?,?,?,?)")
                        ->execute([$nom,$poste,$contenu,$note,$actif]);
                    $msg = 'Témoignage ajouté.';
                } else {
                    $id = (int)($_POST['id'] ?? 0);
                    $pdo->prepare("UPDATE temoignages SET nom=?,poste=?,contenu=?,note=?,actif=? WHERE id=?")
                        ->execute([$nom,$poste,$contenu,$note,$actif,$id]);
                    $msg = 'Témoignage modifié.';
                }
            }
        } elseif ($action === 'supprimer') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare("DELETE FROM temoignages WHERE id=?")->execute([$id]);
            $msg = 'Témoignage supprimé.';
        }
    } catch (Exception $e) { $err = 'Erreur : ' . $e->getMessage(); }
}

try {
    $pdo = getDB();
    $temoignages = $pdo->query("SELECT * FROM temoignages ORDER BY id DESC")->fetchAll();
} catch (Exception $e) { $temoignages = []; }

include '../../admin/includes/layout-header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-quote-left"></i> Témoignages</h1>
        <p class="page-subtitle"><?= count($temoignages) ?> témoignage(s)</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('modalTem').style.display='flex'">
        <i class="fas fa-plus"></i> Ajouter
    </button>
</div>

<?php if ($msg): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $err ?></div><?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <table class="table">
            <thead><tr><th>#</th><th>Nom</th><th>Poste</th><th>Note</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($temoignages)): ?>
            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--admin-gray);"><i class="fas fa-quote-left" style="font-size:40px;display:block;margin-bottom:12px;opacity:0.3;"></i>Aucun témoignage</td></tr>
            <?php else: ?>
            <?php foreach ($temoignages as $t): ?>
            <tr>
                <td><?= $t['id'] ?></td>
                <td><strong><?= sanitize($t['nom']) ?></strong></td>
                <td><?= sanitize($t['poste'] ?? '') ?></td>
                <td><?= str_repeat('★', $t['note']) ?></td>
                <td><?= $t['actif'] ? '<span class="status-badge success">Actif</span>' : '<span class="status-badge danger">Inactif</span>' ?></td>
                <td>
                    <button onclick='editTem(<?= json_encode($t) ?>)' class="btn btn-sm btn-outline btn-icon"><i class="fas fa-edit"></i></button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ?')">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id" value="<?= $t['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger btn-icon"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="modalTem" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:var(--admin-radius);padding:36px;width:500px;max-width:95vw;">
        <h3 id="temModalTitle" style="margin-bottom:24px;color:var(--admin-primary);"><i class="fas fa-quote-left"></i> Ajouter un témoignage</h3>
        <form method="POST">
            <input type="hidden" name="action" id="temAction" value="ajouter">
            <input type="hidden" name="id" id="temId" value="">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Nom *</label><input type="text" name="nom" id="tNom" class="form-control" required></div>
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Poste / Ville</label><input type="text" name="poste" id="tPoste" class="form-control"></div>
            </div>
            <div style="margin-bottom:16px;"><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Témoignage *</label><textarea name="contenu" id="tContenu" class="form-control" rows="4" required></textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
                <div><label style="font-size:12px;font-weight:600;display:block;margin-bottom:6px;">Note (1-5)</label><input type="number" name="note" id="tNote" class="form-control" min="1" max="5" value="5"></div>
                <div style="display:flex;align-items:center;gap:8px;padding-top:22px;"><input type="checkbox" name="actif" id="tActif" checked><label for="tActif" style="font-size:14px;font-weight:600;">Actif</label></div>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('modalTem').style.display='none'" class="btn btn-outline">Annuler</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<script>
function editTem(t) {
    document.getElementById('temAction').value = 'modifier';
    document.getElementById('temModalTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier le témoignage';
    document.getElementById('temId').value = t.id;
    document.getElementById('tNom').value = t.nom;
    document.getElementById('tPoste').value = t.poste || '';
    document.getElementById('tContenu').value = t.contenu;
    document.getElementById('tNote').value = t.note;
    document.getElementById('tActif').checked = t.actif == 1;
    document.getElementById('modalTem').style.display = 'flex';
}
</script>
<?php include '../../admin/includes/layout-footer.php'; ?>
