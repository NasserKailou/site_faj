<?php
$page_title = 'Paramètres du site';
require_once '../includes/config.php';
requireAdmin();

function getSiteParam($cle, $default = '') {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT valeur FROM parametres WHERE cle = ?");
        $stmt->execute([$cle]);
        $result = $stmt->fetch();
        return $result ? $result['valeur'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDB();
        
        $params_a_sauver = [
            'site_nom', 'site_slogan', 'site_description', 'site_email', 
            'site_telephone', 'site_adresse', 'site_facebook', 'site_twitter', 
            'site_linkedin', 'site_youtube', 'hero_titre', 'hero_sous_titre',
            'a_propos_titre', 'a_propos_texte'
        ];
        
        $stmt = $pdo->prepare("INSERT INTO parametres (cle, valeur) VALUES (?, ?) ON DUPLICATE KEY UPDATE valeur = VALUES(valeur)");
        
        foreach ($params_a_sauver as $cle) {
            if (isset($_POST[$cle])) {
                $stmt->execute([$cle, $_POST[$cle]]);
            }
        }
        
        // Upload logo si fourni
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','svg','webp'])) {
                move_uploaded_file($_FILES['logo']['tmp_name'], ASSETS_PATH . '/images/logo-faj.' . $ext);
            }
        }
        
        $success = 'Paramètres enregistrés avec succès !';
    } catch (Exception $e) {
        $error = 'Erreur lors de la sauvegarde: ' . $e->getMessage();
    }
}

include '../admin/includes/layout-header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-cog"></i> Paramètres du Site</h1>
        <p class="page-subtitle">Gérez les informations générales et le contenu du site</p>
    </div>
</div>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    
    <!-- Tabs -->
    <div style="display:flex; gap:4px; margin-bottom:24px; border-bottom:2px solid var(--admin-border);">
        <?php
        $tabs = ['general'=>'Général', 'contact'=>'Contact & Réseaux', 'accueil'=>'Page d\'Accueil', 'a_propos'=>'À Propos', 'paiement'=>'Paiements'];
        foreach ($tabs as $id => $label):
        ?>
        <button type="button" class="tab-btn <?= $id === 'general' ? 'active' : '' ?>" 
                onclick="switchTab('<?= $id ?>')" data-tab="<?= $id ?>"
                style="padding:10px 20px; background:none; border:none; border-bottom:2px solid transparent; margin-bottom:-2px; font-weight:600; font-size:14px; cursor:pointer; color:var(--admin-gray); transition:all 0.3s;">
            <?= $label ?>
        </button>
        <?php endforeach; ?>
    </div>
    
    <!-- Onglet Général -->
    <div id="tab-general" class="tab-panel">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-globe"></i> Informations Générales</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom du site <span style="color:red">*</span></label>
                        <input type="text" name="site_nom" class="form-control" value="<?= getSiteParam('site_nom', 'Fonds d\'Appui à la Justice') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Slogan</label>
                        <input type="text" name="site_slogan" class="form-control" value="<?= getSiteParam('site_slogan', 'Ensemble pour une Justice accessible à tous') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Description du site (SEO)</label>
                    <textarea name="site_description" class="form-control" rows="3"><?= getSiteParam('site_description') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Logo du site</label>
                    <div style="display:flex; align-items:center; gap:20px;">
                        <img src="<?= SITE_URL ?>/assets/images/logo-faj.png" alt="Logo" style="width:60px; height:60px; object-fit:contain; border:2px solid var(--admin-border); border-radius:8px; padding:5px;">
                        <div class="image-upload-zone" style="flex:1; padding:20px;" onclick="document.getElementById('logoInput').click()">
                            <i class="fas fa-upload" style="font-size:24px; margin-bottom:8px; display:block;"></i>
                            <span>Cliquez pour changer le logo (PNG, JPG, SVG)</span>
                        </div>
                        <input type="file" id="logoInput" name="logo" accept="image/*" style="display:none;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Onglet Contact -->
    <div id="tab-contact" class="tab-panel" style="display:none;">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-address-book"></i> Coordonnées</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Email de contact</label>
                        <input type="email" name="site_email" class="form-control" value="<?= getSiteParam('site_email', 'contact@faj.ne') ?>">
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="text" name="site_telephone" class="form-control" value="<?= getSiteParam('site_telephone', '+227 20 XX XX XX') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Adresse physique</label>
                    <input type="text" name="site_adresse" class="form-control" value="<?= getSiteParam('site_adresse', 'Niamey, Niger') ?>">
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:20px;">
            <div class="card-header"><h3><i class="fas fa-share-alt"></i> Réseaux Sociaux</h3></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fab fa-facebook" style="color:#1877F2;"></i> Facebook</label>
                        <input type="url" name="site_facebook" class="form-control" value="<?= getSiteParam('site_facebook') ?>" placeholder="https://facebook.com/...">
                    </div>
                    <div class="form-group">
                        <label><i class="fab fa-x-twitter"></i> Twitter / X</label>
                        <input type="url" name="site_twitter" class="form-control" value="<?= getSiteParam('site_twitter') ?>" placeholder="https://twitter.com/...">
                    </div>
                    <div class="form-group">
                        <label><i class="fab fa-linkedin" style="color:#0A66C2;"></i> LinkedIn</label>
                        <input type="url" name="site_linkedin" class="form-control" value="<?= getSiteParam('site_linkedin') ?>" placeholder="https://linkedin.com/...">
                    </div>
                    <div class="form-group">
                        <label><i class="fab fa-youtube" style="color:#FF0000;"></i> YouTube</label>
                        <input type="url" name="site_youtube" class="form-control" value="<?= getSiteParam('site_youtube') ?>" placeholder="https://youtube.com/...">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Onglet Accueil -->
    <div id="tab-accueil" class="tab-panel" style="display:none;">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-home"></i> Section Hero (Bannière principale)</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Titre principal</label>
                    <input type="text" name="hero_titre" class="form-control" value="<?= getSiteParam('hero_titre', 'Votre donation peut changer des vies.') ?>">
                    <small style="color:var(--admin-gray); font-size:12px;">Utilisez &lt;span&gt;texte en orange&lt;/span&gt; pour mettre en évidence</small>
                </div>
                <div class="form-group">
                    <label>Sous-titre / Description</label>
                    <textarea name="hero_sous_titre" class="form-control" rows="3"><?= getSiteParam('hero_sous_titre') ?></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Onglet À Propos -->
    <div id="tab-a_propos" class="tab-panel" style="display:none;">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-info-circle"></i> Page À Propos</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label>Titre section À Propos</label>
                    <input type="text" name="a_propos_titre" class="form-control" value="<?= getSiteParam('a_propos_titre', 'À Propos du FAJ') ?>">
                </div>
                <div class="form-group">
                    <label>Texte de présentation (HTML autorisé)</label>
                    <div class="editor-toolbar">
                        <button type="button" onclick="formatText('bold')"><i class="fas fa-bold"></i></button>
                        <button type="button" onclick="formatText('italic')"><i class="fas fa-italic"></i></button>
                        <button type="button" onclick="formatText('underline')"><i class="fas fa-underline"></i></button>
                        <button type="button" onclick="insertTag('p')">§</button>
                        <button type="button" onclick="insertTag('strong')"><b>B</b></button>
                    </div>
                    <textarea name="a_propos_texte" id="editorAPropos" class="form-control form-control-lg" rows="10" style="border-top:none; border-radius:0 0 8px 8px;"><?= getSiteParam('a_propos_texte') ?></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Onglet Paiements -->
    <div id="tab-paiement" class="tab-panel" style="display:none;">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Ces informations sont sensibles. Modifiez le fichier <code>includes/config.php</code> directement sur le serveur pour configurer les clés API de paiement.</span>
        </div>
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-credit-card"></i> Passerelles de Paiement</h3></div>
            <div class="card-body">
                <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:20px;">
                    <?php
                    $gateways = [
                        ['icon'=>'fas fa-mobile-alt', 'name'=>'CinetPay', 'desc'=>'Orange Money, Moov Money Niger', 'color'=>'#FF6600', 'key'=>'CINETPAY_APIKEY'],
                        ['icon'=>'fab fa-cc-stripe', 'name'=>'Stripe', 'desc'=>'Visa, Mastercard (International)', 'color'=>'#635BFF', 'key'=>'STRIPE_PUBLIC_KEY'],
                        ['icon'=>'fas fa-university', 'name'=>'PayDunya', 'desc'=>'Solution africaine (Afrique de l\'Ouest)', 'color'=>'#ff4d00', 'key'=>'PAYDUNYA_PUBLIC_KEY'],
                        ['icon'=>'fas fa-money-bill-transfer', 'name'=>'Virement Bancaire', 'desc'=>'Pour les dons importants', 'color'=>'#28a745', 'key'=>null],
                    ];
                    foreach ($gateways as $gw):
                    $is_configured = ($gw['key'] && defined($gw['key']) && strpos(constant($gw['key']), 'VOTRE_') === false);
                    ?>
                    <div style="border:2px solid var(--admin-border); border-radius:var(--admin-radius); padding:20px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <i class="<?= $gw['icon'] ?>" style="font-size:26px; color:<?= $gw['color'] ?>;"></i>
                                <div>
                                    <strong style="font-size:15px;"><?= $gw['name'] ?></strong>
                                    <p style="font-size:12px; color:var(--admin-gray);"><?= $gw['desc'] ?></p>
                                </div>
                            </div>
                            <span class="status-badge <?= $is_configured ? 'success' : 'warning' ?>">
                                <?= $is_configured ? '✓ Configuré' : '⚠ À configurer' ?>
                            </span>
                        </div>
                        <?php if (!$gw['key']): ?>
                        <p style="font-size:13px; color:var(--admin-gray);">Disponible nativement via coordonnées bancaires</p>
                        <?php else: ?>
                        <p style="font-size:12px; color:var(--admin-gray);">
                            Configurez la clé <code><?= $gw['key'] ?></code> dans <code>includes/config.php</code>
                        </p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Submit -->
    <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px; padding-top:20px; border-top:1px solid var(--admin-border);">
        <a href="<?= SITE_URL ?>/" target="_blank" class="btn btn-outline">
            <i class="fas fa-eye"></i> Voir le site
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Enregistrer les paramètres
        </button>
    </div>
</form>

<style>
.tab-btn.active {
    color: var(--admin-secondary) !important;
    border-bottom-color: var(--admin-secondary) !important;
}
</style>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tabId).style.display = 'block';
    document.querySelector('[data-tab="' + tabId + '"]').classList.add('active');
}

function formatText(cmd) {
    document.execCommand(cmd, false, null);
}

function insertTag(tag) {
    const ta = document.getElementById('editorAPropos');
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const selected = ta.value.substring(start, end);
    const replacement = '<' + tag + '>' + selected + '</' + tag + '>';
    ta.value = ta.value.substring(0, start) + replacement + ta.value.substring(end);
}
</script>

<?php include '../admin/includes/layout-footer.php'; ?>
