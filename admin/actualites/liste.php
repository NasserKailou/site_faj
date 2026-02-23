<?php
$page_title = 'Gestion des Actualités';
require_once '../../includes/config.php';
requireAdmin();

$success = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        $pdo = getDB();
        if ($action === 'delete') {
            $pdo->prepare("DELETE FROM actualites WHERE id=?")->execute([intval($_POST['id'])]);
            $success = 'Actualité supprimée.';
        } elseif (in_array($action, ['add','edit'])) {
            $titre = sanitize($_POST['titre'] ?? '');
            $extrait = sanitize($_POST['extrait'] ?? '');
            $contenu = $_POST['contenu'] ?? '';
            $categorie = sanitize($_POST['categorie'] ?? '');
            $statut = sanitize($_POST['statut'] ?? 'publie');
            $en_vedette = isset($_POST['en_vedette']) ? 1 : 0;
            
            // Slug
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $titre))));
            
            // Image
            $image = $_POST['image_actuelle'] ?? null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                    $filename = 'actu_' . time() . '.' . $ext;
                    if (!is_dir(UPLOADS_PATH . '/actualites')) mkdir(UPLOADS_PATH . '/actualites', 0755, true);
                    move_uploaded_file($_FILES['image']['tmp_name'], UPLOADS_PATH . '/actualites/' . $filename);
                    $image = $filename;
                }
            }
            
            if ($action === 'add') {
                $pdo->prepare("INSERT INTO actualites (titre, slug, extrait, contenu, categorie, image, statut, en_vedette, admin_id) VALUES (?,?,?,?,?,?,?,?,?)")
                    ->execute([$titre, $slug, $extrait, $contenu, $categorie, $image, $statut, $en_vedette, $_SESSION['admin_id']]);
                $success = 'Actualité publiée !';
            } else {
                $id = intval($_POST['id']);
                $pdo->prepare("UPDATE actualites SET titre=?, extrait=?, contenu=?, categorie=?, image=?, statut=?, en_vedette=? WHERE id=?")
                    ->execute([$titre, $extrait, $contenu, $categorie, $image, $statut, $en_vedette, $id]);
                $success = 'Actualité modifiée !';
            }
        }
    } catch (Exception $e) { $error = $e->getMessage(); }
}

try {
    $pdo = getDB();
    $actualites = $pdo->query("SELECT * FROM actualites ORDER BY created_at DESC")->fetchAll();
} catch (Exception $e) { $actualites = []; }

include '../../admin/includes/layout-header.php';
?>
<div class="page-header">
    <div><h1 class="page-title"><i class="fas fa-newspaper"></i> Actualités</h1></div>
    <button class="btn btn-primary" onclick="document.getElementById('actForm').style.display='block'">
        <i class="fas fa-plus"></i> Nouvelle Actualité
    </button>
</div>
<?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

<div id="actForm" class="card" style="margin-bottom:24px; display:none;">
    <div class="card-header"><h3>Nouvelle Actualité</h3></div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="form-group"><label>Titre *</label><input type="text" name="titre" class="form-control" required></div>
            <div class="form-group"><label>Extrait (résumé)</label><textarea name="extrait" class="form-control" rows="2"></textarea></div>
            <div class="form-group"><label>Contenu complet (HTML autorisé)</label><textarea name="contenu" class="form-control" rows="10"></textarea></div>
            <div class="form-grid">
                <div class="form-group"><label>Catégorie</label><input type="text" name="categorie" class="form-control" placeholder="Ex: Événement, Annonce..."></div>
                <div class="form-group"><label>Statut</label><select name="statut" class="form-control"><option value="publie">Publié</option><option value="brouillon">Brouillon</option></select></div>
            </div>
            <div class="form-group"><label>Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
            <div class="form-group"><label class="checkbox-label" style="display:flex;gap:8px;align-items:center;"><input type="checkbox" name="en_vedette"> Mettre en vedette</label></div>
            <div style="display:flex; gap:12px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Publier</button>
                <button type="button" class="btn btn-outline" onclick="this.closest('#actForm').style.display='none'">Annuler</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Titre</th><th>Catégorie</th><th>Vues</th><th>Statut</th><th>Vedette</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($actualites as $a): ?>
                    <tr>
                        <td><strong><?= sanitize($a['titre']) ?></strong></td>
                        <td><span class="badge orange"><?= sanitize($a['categorie'] ?? '-') ?></span></td>
                        <td><?= $a['nb_vues'] ?></td>
                        <td><span class="status-badge <?= $a['statut']==='publie' ? 'success' : 'warning' ?>"><?= $a['statut'] ?></span></td>
                        <td><?= $a['en_vedette'] ? '<i class="fas fa-star" style="color:#ffc107;"></i>' : '-' ?></td>
                        <td style="font-size:12px;"><?= date('d/m/Y', strtotime($a['created_at'])) ?></td>
                        <td>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                <button class="btn btn-sm btn-danger btn-icon"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($actualites)): ?><tr><td colspan="7" style="text-align:center;padding:40px;color:var(--admin-gray);">Aucune actualité</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include '../../admin/includes/layout-footer.php'; ?>
