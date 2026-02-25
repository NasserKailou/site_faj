<?php
$page_title = 'Gestion des Projets';
require_once '../../includes/config.php';
requireAdmin();

$success = '';
$error = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = getDB();
        
        if ($action === 'delete') {
            $id = intval($_POST['id']);
            $pdo->prepare("UPDATE projets SET statut='brouillon' WHERE id=?")->execute([$id]);
            $success = 'Projet archivé avec succès.';
            
        } elseif ($action === 'add' || $action === 'edit') {
            $titre = sanitize($_POST['titre'] ?? '');
            $slug = sanitize(str_replace(' ', '-', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $_POST['titre'] ?? ''))));
            $description_courte = sanitize($_POST['description_courte'] ?? '');
            $description_longue = $_POST['description_longue'] ?? '';
            $categorie = sanitize($_POST['categorie'] ?? 'autre');
            $objectif_montant = floatval($_POST['objectif_montant'] ?? 0);
            $statut = sanitize($_POST['statut'] ?? 'actif');
            $priorite = intval($_POST['priorite'] ?? 0);
            
            // Upload image
            $image = $_POST['image_actuelle'] ?? null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                    $filename = 'projet_' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], UPLOADS_PATH . '/projets/' . $filename);
                    $image = $filename;
                }
            }
            
            if ($action === 'add') {
                // Rendre le slug unique
                $base_slug = $slug;
                $counter = 1;
                while ($pdo->prepare("SELECT id FROM projets WHERE slug=?")->execute([$slug]) && $pdo->query("SELECT COUNT(*) FROM projets WHERE slug='$slug'")->fetchColumn() > 0) {
                    $slug = $base_slug . '-' . $counter++;
                }
                
                $pdo->prepare("INSERT INTO projets (titre, slug, description_courte, description_longue, categorie, objectif_montant, image, statut, priorite) VALUES (?,?,?,?,?,?,?,?,?)")
                    ->execute([$titre, $slug, $description_courte, $description_longue, $categorie, $objectif_montant, $image, $statut, $priorite]);
                $success = 'Projet créé avec succès !';
            } else {
                $id = intval($_POST['id']);
                $pdo->prepare("UPDATE projets SET titre=?, description_courte=?, description_longue=?, categorie=?, objectif_montant=?, image=?, statut=?, priorite=? WHERE id=?")
                    ->execute([$titre, $description_courte, $description_longue, $categorie, $objectif_montant, $image, $statut, $priorite, $id]);
                $success = 'Projet modifié avec succès !';
            }
        }
    } catch (Exception $e) {
        $error = 'Erreur: ' . $e->getMessage();
    }
}

// Mode édition
$edit_projet = null;
if (isset($_GET['edit'])) {
    try {
        $pdo = getDB();
        $edit_projet = $pdo->prepare("SELECT * FROM projets WHERE id=?")->execute([intval($_GET['edit'])]) ? 
                       $pdo->query("SELECT * FROM projets WHERE id=" . intval($_GET['edit']))->fetch() : null;
        $edit_stmt = $pdo->prepare("SELECT * FROM projets WHERE id=?");
        $edit_stmt->execute([intval($_GET['edit'])]);
        $edit_projet = $edit_stmt->fetch();
    } catch (Exception $e) {}
}

try {
    $pdo = getDB();
    $projets = $pdo->query("SELECT * FROM projets ORDER BY priorite, created_at DESC")->fetchAll();
} catch (Exception $e) {
    $projets = [];
}

$categories = [
    'infrastructure' => 'Infrastructures',
    'formation' => 'Formation',
    'humanisation' => 'Humanisation Carcérale',
    'acces_justice' => 'Accès à la Justice',
    'numerisation' => 'Numérisation',
    'autre' => 'Autres',
];

include '../../admin/includes/layout-header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-project-diagram"></i> Gestion des Projets</h1>
        <p class="page-subtitle">Gérez les projets financés par le FAJ</p>
    </div>
    <button class="btn btn-primary" onclick="showForm()">
        <i class="fas fa-plus"></i> Nouveau Projet
    </button>
</div>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

<!-- Formulaire ajout/édition -->
<div id="projetForm" class="card" style="margin-bottom:24px; <?= $edit_projet ? '' : 'display:none;' ?>">
    <div class="card-header">
        <h3><i class="fas fa-<?= $edit_projet ? 'edit' : 'plus' ?>"></i> <?= $edit_projet ? 'Modifier le Projet' : 'Nouveau Projet' ?></h3>
        <button type="button" onclick="hideForm()" class="btn btn-sm btn-outline"><i class="fas fa-times"></i></button>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?= $edit_projet ? 'edit' : 'add' ?>">
            <?php if ($edit_projet): ?>
            <input type="hidden" name="id" value="<?= $edit_projet['id'] ?>">
            <input type="hidden" name="image_actuelle" value="<?= $edit_projet['image'] ?>">
            <?php endif; ?>
            
            <div class="form-grid">
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Titre du projet <span style="color:red">*</span></label>
                    <input type="text" name="titre" class="form-control" value="<?= $edit_projet ? sanitize($edit_projet['titre']) : '' ?>" required placeholder="Ex: Construction de Tribunaux dans les régions">
                </div>
                
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="categorie" class="form-control">
                        <?php foreach ($categories as $key => $label): ?>
                        <option value="<?= $key ?>" <?= ($edit_projet && $edit_projet['categorie'] === $key) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Objectif financier (FCFA)</label>
                    <input type="number" name="objectif_montant" class="form-control" value="<?= $edit_projet ? $edit_projet['objectif_montant'] : '' ?>" placeholder="Ex: 150000000" min="0">
                </div>
                
                <div class="form-group">
                    <label>Statut</label>
                    <select name="statut" class="form-control">
                        <option value="actif" <?= (!$edit_projet || $edit_projet['statut']==='actif') ? 'selected' : '' ?>>Actif</option>
                        <option value="en_pause" <?= ($edit_projet && $edit_projet['statut']==='en_pause') ? 'selected' : '' ?>>En pause</option>
                        <option value="termine" <?= ($edit_projet && $edit_projet['statut']==='termine') ? 'selected' : '' ?>>Terminé</option>
                        <option value="brouillon" <?= ($edit_projet && $edit_projet['statut']==='brouillon') ? 'selected' : '' ?>>Brouillon</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Ordre d'affichage</label>
                    <input type="number" name="priorite" class="form-control" value="<?= $edit_projet ? $edit_projet['priorite'] : '0' ?>" min="0">
                </div>
                
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Description courte</label>
                    <textarea name="description_courte" class="form-control" rows="2" placeholder="Une phrase de résumé du projet..."><?= $edit_projet ? sanitize($edit_projet['description_courte']) : '' ?></textarea>
                </div>
                
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Description complète (HTML autorisé)</label>
                    <textarea name="description_longue" class="form-control" rows="8"><?= $edit_projet ? $edit_projet['description_longue'] : '' ?></textarea>
                </div>
                
                <div class="form-group" style="grid-column:1/-1;">
                    <label>Image du projet</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <?php if ($edit_projet && $edit_projet['image']): ?>
                    <div style="margin-top:10px;">
                        <img src="<?= UPLOADS_URL ?>/projets/<?= $edit_projet['image'] ?>" style="height:80px; border-radius:8px; object-fit:cover;">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="hideForm()" class="btn btn-outline">Annuler</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $edit_projet ? 'Modifier' : 'Créer le Projet' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Liste des projets -->
<div class="card">
    <div class="card-header"><h3><i class="fas fa-list"></i> <?= count($projets) ?> projet(s)</h3></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Ordre</th>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Objectif</th>
                        <th>Collecté</th>
                        <th>Progression</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projets as $p):
                        $pct = $p['objectif_montant'] > 0 ? min(100, round($p['montant_collecte']/$p['objectif_montant']*100)) : 0;
                        $statuts_labels = ['actif'=>'success','brouillon'=>'warning','termine'=>'info','en_pause'=>'danger'];
                    ?>
                    <tr>
                        <td><input type="number" value="<?= $p['priorite'] ?>" style="width:60px; padding:4px 8px; border:1px solid var(--admin-border); border-radius:4px;"></td>
                        <td>
                            <strong><?= sanitize($p['titre']) ?></strong>
                            <?php if ($p['image']): ?>
                            <img src="<?= UPLOADS_URL ?>/projets/<?= $p['image'] ?>" style="height:35px; width:50px; object-fit:cover; border-radius:4px; margin-left:8px; vertical-align:middle;">
                            <?php endif; ?>
                        </td>
                        <td><span class="badge orange"><?= $categories[$p['categorie']] ?? $p['categorie'] ?></span></td>
                        <td><?= number_format($p['objectif_montant'],0,',',' ') ?> FCFA</td>
                        <td><?= number_format($p['montant_collecte'],0,',',' ') ?> FCFA</td>
                        <td>
                            <div style="background:var(--admin-border); height:6px; border-radius:3px; width:100px;">
                                <div style="background:var(--admin-secondary); height:100%; border-radius:3px; width:<?= $pct ?>%;"></div>
                            </div>
                            <small style="font-size:11px; color:var(--admin-gray);"><?= $pct ?>%</small>
                        </td>
                        <td><span class="status-badge <?= $statuts_labels[$p['statut']] ?? 'warning' ?>"><?= ucfirst($p['statut']) ?></span></td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline btn-icon" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= SITE_URL ?>/projets" target="_blank" class="btn btn-sm btn-outline btn-icon" title="Voir sur le site">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Archiver ce projet ?')">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Archiver">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($projets)): ?>
                    <tr><td colspan="8" style="text-align:center; padding:50px; color:var(--admin-gray);">
                        Aucun projet. Créez votre premier projet !
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showForm() {
    document.getElementById('projetForm').style.display = 'block';
    document.getElementById('projetForm').scrollIntoView({behavior:'smooth'});
}
function hideForm() {
    document.getElementById('projetForm').style.display = 'none';
}
</script>

<?php include '../../admin/includes/layout-footer.php'; ?>
