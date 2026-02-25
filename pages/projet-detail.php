<?php
$page_title = 'Projet';
require_once '../includes/config.php';

$slug = sanitize($_GET['slug'] ?? '');
if (empty($slug)) { header('Location: ' . SITE_URL . '/projets'); exit; }

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM projets WHERE slug=? AND statut='actif'");
    $stmt->execute([$slug]);
    $projet = $stmt->fetch();

    if (!$projet) { http_response_code(404); include __DIR__ . '/404.php'; exit; }

    $page_title = $projet['titre'];

    // Projets connexes
    $stmt2 = $pdo->prepare("SELECT id,titre,slug,image,description_courte,objectif_montant,montant_collecte FROM projets WHERE statut='actif' AND id != ? LIMIT 3");
    $stmt2->execute([$projet['id']]);
    $connexes = $stmt2->fetchAll();

} catch (Exception $e) {
    $projet = null;
}

require_once '../includes/header.php';

$pct = (!empty($projet) && $projet['objectif_montant'] > 0) ? min(100, round($projet['montant_collecte'] / $projet['objectif_montant'] * 100)) : 0;
$categories_labels = [
    'infrastructure' => 'Infrastructures Judiciaires',
    'formation'      => 'Formation & Renforcement',
    'humanisation'   => 'Humanisation Carcérale',
    'acces_justice'  => 'Accès à la Justice',
    'numerisation'   => 'Numérisation',
    'autre'          => 'Autres',
];
?>

<div class="page-hero" style="padding:60px 0;">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <a href="<?= SITE_URL ?>/projets">Projets</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current"><?= sanitize($projet['titre'] ?? '') ?></span>
        </div>
    </div>
</div>

<section style="padding:60px 0; background:var(--light);">
    <div class="container">
        <div style="display:grid; grid-template-columns:1fr 340px; gap:40px; align-items:start;">

            <!-- Projet principal -->
            <div>
                <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); overflow:hidden; margin-bottom:30px;">
                    <?php if ($projet['image']): ?>
                    <img src="<?= UPLOADS_URL ?>/projets/<?= $projet['image'] ?>" alt="<?= sanitize($projet['titre']) ?>" style="width:100%; height:360px; object-fit:cover;">
                    <?php else: ?>
                    <div style="height:260px; background:linear-gradient(135deg,var(--primary),var(--primary-light)); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-project-diagram" style="font-size:80px; color:rgba(255,255,255,0.2);"></i>
                    </div>
                    <?php endif; ?>
                    <div style="padding:40px;">
                        <span style="background:var(--secondary); color:white; font-size:12px; font-weight:700; padding:4px 12px; border-radius:20px;">
                            <?= $categories_labels[$projet['categorie']] ?? $projet['categorie'] ?>
                        </span>
                        <h1 style="font-family:var(--font-display); font-size:30px; color:var(--primary); margin:16px 0 24px; line-height:1.3;"><?= sanitize($projet['titre']) ?></h1>
                        <div style="color:var(--dark); line-height:1.9; font-size:16px;">
                            <?= $projet['description_longue'] ?? '<p>' . sanitize($projet['description_courte']) . '</p>' ?>
                        </div>
                        <div style="margin-top:40px; padding-top:24px; border-top:1px solid var(--light-gray);">
                            <a href="<?= SITE_URL ?>/projets" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Retour aux projets
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <aside>
                <!-- Progression du projet -->
                <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); padding:28px; margin-bottom:24px;">
                    <h4 style="font-size:16px; font-weight:700; color:var(--primary); margin-bottom:20px;">Avancement du projet</h4>
                    <div style="margin-bottom:16px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="font-size:13px; color:var(--gray);">Collecté</span>
                            <strong style="color:var(--secondary);"><?= formatMontant($projet['montant_collecte']) ?></strong>
                        </div>
                        <div style="height:10px; background:var(--light-gray); border-radius:5px; overflow:hidden;">
                            <div style="height:100%; width:<?= $pct ?>%; background:linear-gradient(90deg,var(--secondary),#f5a623); border-radius:5px; transition:width 1s ease;"></div>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-top:8px;">
                            <span style="font-size:13px; font-weight:700; color:var(--secondary);"><?= $pct ?>%</span>
                            <span style="font-size:13px; color:var(--gray);">Objectif : <?= formatMontant($projet['objectif_montant']) ?></span>
                        </div>
                    </div>
                    <a href="<?= SITE_URL ?>/don?projet=<?= urlencode($projet['slug']) ?>" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:8px;">
                        <i class="fas fa-heart"></i> Soutenir ce projet
                    </a>
                </div>

                <!-- Projets connexes -->
                <?php if (!empty($connexes)): ?>
                <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); padding:24px;">
                    <h4 style="font-size:16px; font-weight:700; color:var(--primary); margin-bottom:20px; padding-bottom:12px; border-bottom:2px solid var(--secondary);">Autres projets</h4>
                    <?php foreach ($connexes as $c):
                        $pc = $c['objectif_montant'] > 0 ? min(100, round($c['montant_collecte']/$c['objectif_montant']*100)) : 0;
                    ?>
                    <a href="<?= SITE_URL ?>/projets/<?= $c['slug'] ?>" style="display:flex; gap:12px; align-items:center; padding:12px 0; border-bottom:1px solid var(--light-gray); text-decoration:none; color:inherit;">
                        <div style="width:60px; height:60px; border-radius:var(--radius); overflow:hidden; flex-shrink:0; background:var(--light);">
                            <?php if ($c['image']): ?>
                            <img src="<?= UPLOADS_URL ?>/projets/<?= $c['image'] ?>" style="width:100%;height:100%;object-fit:cover;" alt="">
                            <?php else: ?>
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--primary);color:white;"><i class="fas fa-project-diagram" style="font-size:20px;"></i></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p style="font-size:13px; font-weight:600; color:var(--primary); line-height:1.4; margin-bottom:4px;"><?= sanitize($c['titre']) ?></p>
                            <span style="font-size:11px; color:var(--secondary); font-weight:700;"><?= $pc ?>% atteint</span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
