<?php
$page_title = 'Nos Projets';
require_once '../includes/config.php';

$cat_filter = isset($_GET['cat']) ? sanitize($_GET['cat']) : 'all';

try {
    $pdo = getDB();
    if ($cat_filter !== 'all') {
        $stmt = $pdo->prepare("SELECT * FROM projets WHERE statut='actif' AND categorie=? ORDER BY priorite");
        $stmt->execute([$cat_filter]);
        $projets = $stmt->fetchAll();
    } else {
        $projets = $pdo->query("SELECT * FROM projets WHERE statut='actif' ORDER BY priorite")->fetchAll();
    }
} catch (Exception $e) {
    $projets = [];
}

$categories = [
    'infrastructure' => ['label' => 'Infrastructures', 'icon' => 'fas fa-building-columns'],
    'formation' => ['label' => 'Formation', 'icon' => 'fas fa-graduation-cap'],
    'humanisation' => ['label' => 'Humanisation Carcérale', 'icon' => 'fas fa-heart'],
    'acces_justice' => ['label' => 'Accès à la Justice', 'icon' => 'fas fa-hands-helping'],
    'numerisation' => ['label' => 'Numérisation', 'icon' => 'fas fa-laptop-code'],
    'autre' => ['label' => 'Autres', 'icon' => 'fas fa-folder'],
];

require_once '../includes/header.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Nos Projets</span>
        </div>
        <h1>Nos <span style="color:var(--secondary)">Programmes & Projets</span></h1>
        <p>Découvrez tous les projets que nous finançons pour améliorer le système judiciaire du Niger</p>
    </div>
</div>

<section style="padding:80px 0; background:var(--light);">
    <div class="container">
        
        <!-- Filtres catégories -->
        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:50px; justify-content:center;" data-aos="fade-up">
            <button class="filter-btn <?= $cat_filter === 'all' ? 'active' : '' ?>" 
                    data-filter="all"
                    onclick="window.location='<?= SITE_URL ?>/projets'"
                    style="padding:10px 22px; border-radius:50px; border:2px solid var(--light-gray); background:<?= $cat_filter === 'all' ? 'var(--secondary)' : 'white' ?>; color:<?= $cat_filter === 'all' ? 'white' : 'var(--primary)' ?>; font-weight:600; font-size:14px; cursor:pointer; transition:var(--transition);">
                <i class="fas fa-th-large"></i> Tous les Projets
            </button>
            <?php foreach ($categories as $key => $cat): ?>
            <button class="filter-btn <?= $cat_filter === $key ? 'active' : '' ?>"
                    data-filter="<?= $key ?>"
                    onclick="window.location='<?= SITE_URL ?>/projets?cat=<?= $key ?>'"
                    style="padding:10px 22px; border-radius:50px; border:2px solid var(--light-gray); background:<?= $cat_filter === $key ? 'var(--secondary)' : 'white' ?>; color:<?= $cat_filter === $key ? 'white' : 'var(--primary)' ?>; font-weight:600; font-size:14px; cursor:pointer; transition:var(--transition);">
                <i class="<?= $cat['icon'] ?>"></i> <?= $cat['label'] ?>
            </button>
            <?php endforeach; ?>
        </div>
        
        <!-- Grille de projets -->
        <?php if (empty($projets)): ?>
        <!-- Projets par défaut -->
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:30px;" id="projectsGrid">
            <?php
            $projets_defaut = [
                ['titre'=>'Construction et Équipement de Tribunaux','description_courte'=>'Financement de la construction et de l\'équipement des tribunaux dans les régions du Niger pour améliorer l\'accès à la justice.','categorie'=>'infrastructure','objectif_montant'=>150000000,'montant_collecte'=>45000000,'image'=>'hero-collecte.jpg','slug'=>'construction-equipement-tribunaux'],
                ['titre'=>'Formation des Acteurs Judiciaires','description_courte'=>'Renforcement des capacités des magistrats, avocats, greffiers et auxiliaires de justice à travers des formations continues.','categorie'=>'formation','objectif_montant'=>80000000,'montant_collecte'=>20000000,'image'=>'hero-croissance.jpg','slug'=>'formation-acteurs-judiciaires'],
                ['titre'=>'Humanisation du Milieu Carcéral','description_courte'=>'Amélioration des conditions de détention, accès aux soins et programmes de réinsertion sociale pour les détenus.','categorie'=>'humanisation','objectif_montant'=>100000000,'montant_collecte'=>35000000,'image'=>'hero-billets.jpg','slug'=>'humanisation-milieu-carceral'],
                ['titre'=>'Accès à la Justice pour les Vulnérables','description_courte'=>'Aide juridictionnelle gratuite pour les personnes démunies, femmes victimes de violence et enfants en difficulté.','categorie'=>'acces_justice','objectif_montant'=>60000000,'montant_collecte'=>18000000,'image'=>'hero-collecte.jpg','slug'=>'acces-justice-vulnerables'],
                ['titre'=>'Numérisation du Système Judiciaire','description_courte'=>'Modernisation et digitalisation des archives, procédures judiciaires et développement de plateformes numériques.','categorie'=>'numerisation','objectif_montant'=>120000000,'montant_collecte'=>30000000,'image'=>'hero-croissance.jpg','slug'=>'numerisation-systeme-judiciaire'],
                ['titre'=>'Aide aux Détenus en Attente de Jugement','description_courte'=>'Programme d\'assistance juridique et humanitaire pour les personnes en détention préventive prolongée.','categorie'=>'acces_justice','objectif_montant'=>40000000,'montant_collecte'=>12000000,'image'=>'hero-billets.jpg','slug'=>'aide-detenus-attente-jugement'],
            ];
            foreach ($projets_defaut as $p):
                if ($cat_filter !== 'all' && $p['categorie'] !== $cat_filter) continue;
                $pct = $p['objectif_montant'] > 0 ? min(100, round($p['montant_collecte']/$p['objectif_montant']*100)) : 0;
            ?>
            <div class="project-card project-item" data-cat="<?= $p['categorie'] ?>" data-aos="fade-up">
                <div class="project-img">
                    <img src="<?= SITE_URL ?>/assets/images/<?= $p['image'] ?>" alt="<?= $p['titre'] ?>">
                    <span class="project-cat-badge"><?= $categories[$p['categorie']]['label'] ?? $p['categorie'] ?></span>
                </div>
                <div class="project-body">
                    <h3 class="project-title"><?= $p['titre'] ?></h3>
                    <p class="project-desc"><?= $p['description_courte'] ?></p>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" data-width="<?= $pct ?>%" style="width:<?= $pct ?>%"></div>
                        </div>
                        <div class="progress-info">
                            <span class="progress-percent"><?= $pct ?>% atteint</span>
                            <span class="progress-amount"><?= number_format($p['montant_collecte'],0,',',' ') ?> FCFA</span>
                        </div>
                    </div>
                    <div class="project-footer">
                        <div>
                            <span style="font-size:12px; color:var(--gray); display:block;">Objectif</span>
                            <strong style="font-size:14px; color:var(--primary);"><?= number_format($p['objectif_montant'],0,',',' ') ?> FCFA</strong>
                        </div>
                        <a href="<?= SITE_URL ?>/don" class="btn btn-primary btn-sm">
                            <i class="fas fa-heart"></i> Soutenir
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:30px;" id="projectsGrid">
            <?php foreach ($projets as $projet):
                $pct = $projet['objectif_montant'] > 0 ? min(100, round($projet['montant_collecte']/$projet['objectif_montant']*100)) : 0;
            ?>
            <div class="project-card project-item" data-cat="<?= $projet['categorie'] ?>" data-aos="fade-up">
                <div class="project-img">
                    <?php if ($projet['image']): ?>
                    <img src="<?= UPLOADS_URL ?>/projets/<?= $projet['image'] ?>" alt="<?= $projet['titre'] ?>">
                    <?php else: ?>
                    <img src="<?= SITE_URL ?>/assets/images/hero-collecte.jpg" alt="<?= $projet['titre'] ?>">
                    <?php endif; ?>
                    <span class="project-cat-badge"><?= $categories[$projet['categorie']]['label'] ?? $projet['categorie'] ?></span>
                </div>
                <div class="project-body">
                    <h3 class="project-title"><?= sanitize($projet['titre']) ?></h3>
                    <p class="project-desc"><?= sanitize($projet['description_courte']) ?></p>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" data-width="<?= $pct ?>%" style="width:<?= $pct ?>%"></div>
                        </div>
                        <div class="progress-info">
                            <span class="progress-percent"><?= $pct ?>% atteint</span>
                            <span class="progress-amount"><?= number_format($projet['montant_collecte'],0,',',' ') ?> FCFA</span>
                        </div>
                    </div>
                    <div class="project-footer">
                        <div>
                            <span style="font-size:12px; color:var(--gray); display:block;">Objectif</span>
                            <strong style="font-size:14px;"><?= number_format($projet['objectif_montant'],0,',',' ') ?> FCFA</strong>
                        </div>
                        <a href="<?= SITE_URL ?>/projets/<?= $projet['slug'] ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye"></i> Voir plus
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php endif; ?>
        
        <!-- CTA -->
        <div style="text-align:center; margin-top:60px; padding:50px; background:linear-gradient(135deg,var(--primary),var(--primary-light)); border-radius:var(--radius-lg);" data-aos="fade-up">
            <h3 style="font-family:var(--font-display); font-size:28px; color:white; margin-bottom:12px;">Soutenez nos projets</h3>
            <p style="color:rgba(255,255,255,0.78); font-size:16px; margin-bottom:30px;">
                Chaque don, peu importe son montant, contribue à améliorer la justice au Niger.
            </p>
            <a href="<?= SITE_URL ?>/don" class="btn btn-primary btn-lg" style="background:var(--secondary); box-shadow:0 4px 20px rgba(232,135,10,0.5);">
                <i class="fas fa-heart"></i> Faire un Don Maintenant
            </a>
        </div>
    </div>
</section>

<style>
@media (max-width:1024px) { #projectsGrid { grid-template-columns: repeat(2,1fr); } }
@media (max-width:600px) { #projectsGrid { grid-template-columns: 1fr; } }
</style>

<?php require_once '../includes/footer.php'; ?>
