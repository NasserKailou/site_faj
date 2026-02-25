<?php
$page_title = 'Actualité';
require_once '../includes/config.php';

$slug = sanitize($_GET['slug'] ?? '');
if (empty($slug)) { header('Location: ' . SITE_URL . '/actualites'); exit; }

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM actualites WHERE slug=? AND statut='publie'");
    $stmt->execute([$slug]);
    $actu = $stmt->fetch();

    if (!$actu) { http_response_code(404); include __DIR__ . '/404.php'; exit; }

    // Incrémenter les vues
    $pdo->prepare("UPDATE actualites SET nb_vues = nb_vues + 1 WHERE id=?")->execute([$actu['id']]);

    $page_title = $actu['titre'];

    // Actualités connexes
    $stmt2 = $pdo->prepare("SELECT id,titre,slug,image,created_at FROM actualites WHERE statut='publie' AND id != ? ORDER BY created_at DESC LIMIT 3");
    $stmt2->execute([$actu['id']]);
    $connexes = $stmt2->fetchAll();

} catch (Exception $e) {
    $actu = null;
}

require_once '../includes/header.php';
?>

<div class="page-hero" style="padding:60px 0;">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <a href="<?= SITE_URL ?>/actualites">Actualités</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current"><?= sanitize($actu['titre'] ?? '') ?></span>
        </div>
    </div>
</div>

<section style="padding:60px 0; background:var(--light);">
    <div class="container">
        <div style="display:grid; grid-template-columns:1fr 340px; gap:40px; align-items:start;">

            <!-- Article principal -->
            <article style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); overflow:hidden;">
                <?php if ($actu['image']): ?>
                <img src="<?= UPLOADS_URL ?>/actualites/<?= $actu['image'] ?>" alt="<?= sanitize($actu['titre']) ?>" style="width:100%; height:380px; object-fit:cover;">
                <?php else: ?>
                <div style="height:280px; background:linear-gradient(135deg,var(--primary),var(--primary-light)); display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-newspaper" style="font-size:80px; color:rgba(255,255,255,0.2);"></i>
                </div>
                <?php endif; ?>

                <div style="padding:40px;">
                    <?php if ($actu['categorie']): ?>
                    <span style="background:var(--secondary); color:white; font-size:12px; font-weight:700; padding:4px 12px; border-radius:20px; text-transform:uppercase; letter-spacing:0.5px;"><?= sanitize($actu['categorie']) ?></span>
                    <?php endif; ?>

                    <h1 style="font-family:var(--font-display); font-size:32px; color:var(--primary); margin:16px 0 12px; line-height:1.3;"><?= sanitize($actu['titre']) ?></h1>

                    <div style="display:flex; gap:20px; color:var(--gray); font-size:13px; margin-bottom:30px; padding-bottom:24px; border-bottom:1px solid var(--light-gray);">
                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($actu['created_at'])) ?></span>
                        <span><i class="fas fa-eye"></i> <?= (int)$actu['nb_vues'] ?> vues</span>
                    </div>

                    <div style="color:var(--dark); line-height:1.9; font-size:16px;">
                        <?= $actu['contenu'] ?? '<p>' . nl2br(sanitize($actu['extrait'] ?? '')) . '</p>' ?>
                    </div>

                    <div style="margin-top:40px; padding-top:24px; border-top:1px solid var(--light-gray);">
                        <a href="<?= SITE_URL ?>/actualites" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Retour aux actualités
                        </a>
                    </div>
                </div>
            </article>

            <!-- Sidebar -->
            <aside>
                <!-- Don CTA -->
                <div style="background:linear-gradient(135deg,var(--primary),var(--primary-light)); border-radius:var(--radius-lg); padding:30px; color:white; margin-bottom:30px; text-align:center;">
                    <i class="fas fa-heart" style="font-size:36px; margin-bottom:12px; display:block; color:var(--secondary);"></i>
                    <h3 style="font-size:18px; margin-bottom:10px;">Soutenez le FAJ</h3>
                    <p style="font-size:13px; opacity:0.85; margin-bottom:20px; line-height:1.6;">Chaque don contribue à améliorer la justice au Niger.</p>
                    <a href="<?= SITE_URL ?>/don" class="btn btn-primary btn-sm" style="background:var(--secondary); width:100%; justify-content:center;">
                        <i class="fas fa-hand-holding-heart"></i> Faire un Don
                    </a>
                </div>

                <!-- Actualités connexes -->
                <?php if (!empty($connexes)): ?>
                <div style="background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow); padding:24px;">
                    <h4 style="font-size:16px; font-weight:700; color:var(--primary); margin-bottom:20px; padding-bottom:12px; border-bottom:2px solid var(--secondary);">
                        <i class="fas fa-newspaper"></i> Autres actualités
                    </h4>
                    <?php foreach ($connexes as $c): ?>
                    <a href="<?= SITE_URL ?>/actualites/<?= $c['slug'] ?>" style="display:flex; gap:12px; align-items:center; padding:12px 0; border-bottom:1px solid var(--light-gray); text-decoration:none; color:inherit; transition:var(--transition);">
                        <div style="width:60px; height:60px; border-radius:var(--radius); overflow:hidden; flex-shrink:0; background:var(--light);">
                            <?php if ($c['image']): ?>
                            <img src="<?= UPLOADS_URL ?>/actualites/<?= $c['image'] ?>" style="width:100%;height:100%;object-fit:cover;" alt="">
                            <?php else: ?>
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--primary);color:white;"><i class="fas fa-newspaper"></i></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p style="font-size:13px; font-weight:600; color:var(--primary); line-height:1.4; margin-bottom:4px;"><?= sanitize($c['titre']) ?></p>
                            <span style="font-size:11px; color:var(--gray);"><?= date('d/m/Y', strtotime($c['created_at'])) ?></span>
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
