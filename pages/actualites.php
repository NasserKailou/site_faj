<?php
$page_title = 'Actualités';
require_once '../includes/config.php';

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

$page = max(1, intval($_GET['p'] ?? 1));
$per_page = 9;
$offset = ($page - 1) * $per_page;
$cat = sanitize($_GET['cat'] ?? '');

try {
    $pdo = getDB();
    
    $where = "statut='publie'";
    $params = [];
    if ($cat) { $where .= " AND categorie=?"; $params[] = $cat; }
    
    $total = $pdo->prepare("SELECT COUNT(*) FROM actualites WHERE $where");
    $total->execute($params);
    $total = $total->fetchColumn();
    $total_pages = ceil($total / $per_page);
    
    $params[] = $per_page;
    $params[] = $offset;
    $stmt = $pdo->prepare("SELECT * FROM actualites WHERE $where ORDER BY en_vedette DESC, created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute($params);
    $actualites = $stmt->fetchAll();
    
    $categories = $pdo->query("SELECT DISTINCT categorie FROM actualites WHERE statut='publie' AND categorie IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    $actualites = []; $total = 0; $total_pages = 0; $categories = [];
}

require_once '../includes/header.php';
?>

<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Actualités</span>
        </div>
        <h1>Nos <span style="color:var(--secondary)">Actualités</span></h1>
        <p>Restez informé des activités et projets du Fonds d'Appui à la Justice</p>
    </div>
</div>

<section style="padding:80px 0; background:var(--light);">
    <div class="container">
        
        <!-- Filtres -->
        <?php if (!empty($categories)): ?>
        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:40px; justify-content:center;">
            <a href="?p=1" class="btn <?= !$cat ? 'btn-primary' : 'btn-outline' ?> btn-sm" style="border-radius:50px;">Toutes</a>
            <?php foreach ($categories as $c): ?>
            <a href="?cat=<?= urlencode($c) ?>&p=1" class="btn <?= $cat === $c ? 'btn-primary' : 'btn-outline' ?> btn-sm" style="border-radius:50px;"><?= sanitize($c) ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (empty($actualites)): ?>
        <div style="text-align:center; padding:80px 0; color:var(--gray);">
            <i class="fas fa-newspaper" style="font-size:60px; opacity:0.2; display:block; margin-bottom:20px;"></i>
            <h3 style="color:var(--primary);">Aucune actualité pour le moment</h3>
            <p>Revenez bientôt pour découvrir nos dernières nouvelles.</p>
        </div>
        <?php else: ?>
        <div class="news-grid">
            <?php foreach ($actualites as $actu): ?>
            <article class="news-card" data-aos="fade-up">
                <div class="news-img">
                    <?php if ($actu['image']): ?>
                    <img src="<?= UPLOADS_URL ?>/actualites/<?= $actu['image'] ?>" alt="<?= sanitize($actu['titre']) ?>">
                    <?php else: ?>
                    <img src="<?= SITE_URL ?>/assets/images/hero-collecte.jpg" alt="Actualité FAJ">
                    <?php endif; ?>
                </div>
                <div class="news-body">
                    <?php if ($actu['categorie']): ?>
                    <span class="news-cat"><?= sanitize($actu['categorie']) ?></span>
                    <?php endif; ?>
                    <div class="news-meta">
                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($actu['created_at'])) ?></span>
                        <span><i class="fas fa-eye"></i> <?= $actu['nb_vues'] ?> vues</span>
                    </div>
                    <h3 class="news-title">
                        <a href="<?= SITE_URL ?>/pages/actualite-detail.php?slug=<?= $actu['slug'] ?>">
                            <?= sanitize($actu['titre']) ?>
                        </a>
                    </h3>
                    <p class="news-excerpt"><?= sanitize($actu['extrait'] ?? substr(strip_tags($actu['contenu'] ?? ''), 0, 150) . '...') ?></p>
                    <a href="<?= SITE_URL ?>/pages/actualite-detail.php?slug=<?= $actu['slug'] ?>" class="read-more">
                        Lire la suite <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div style="display:flex; justify-content:center; gap:8px; margin-top:50px;">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?p=<?= $i ?>&cat=<?= urlencode($cat) ?>" 
               class="btn <?= $i === $page ? 'btn-primary' : 'btn-outline' ?> btn-sm"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
