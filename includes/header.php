<?php
require_once __DIR__ . '/config.php';

// Récupérer les paramètres du site
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

$site_nom = getSiteParam('site_nom', 'Fonds d\'Appui à la Justice');
$site_slogan = getSiteParam('site_slogan', 'Ensemble pour une Justice accessible à tous');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= getSiteParam('site_description', 'Fonds d\'Appui à la Justice du Niger') ?>">
    <meta property="og:title" content="<?= isset($page_title) ? $page_title . ' | ' : '' ?>FAJ Niger">
    <meta property="og:description" content="<?= getSiteParam('site_description', 'Fonds d\'Appui à la Justice du Niger') ?>">
    <meta property="og:image" content="<?= SITE_URL ?>/assets/images/logo-faj.png">
    <meta property="og:url" content="<?= SITE_URL ?>">
    <title><?= isset($page_title) ? $page_title . ' | ' : '' ?>FAJ - Fonds d'Appui à la Justice du Niger</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/logo-faj.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    
    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body>

<!-- Barre supérieure -->
<div class="topbar">
    <div class="container">
        <div class="topbar-left">
            <span><i class="fas fa-envelope"></i> <?= getSiteParam('site_email', 'contact@faj.ne') ?></span>
            <span><i class="fas fa-phone"></i> <?= getSiteParam('site_telephone', '+227 20 XX XX XX') ?></span>
        </div>
        <div class="topbar-right">
            <a href="<?= getSiteParam('site_facebook', '#') ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="<?= getSiteParam('site_twitter', '#') ?>" target="_blank"><i class="fab fa-x-twitter"></i></a>
            <a href="<?= getSiteParam('site_linkedin', '#') ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            <a href="<?= getSiteParam('site_youtube', '#') ?>" target="_blank"><i class="fab fa-youtube"></i></a>
        </div>
    </div>
</div>

<!-- Navigation principale -->
<header class="header" id="header">
    <div class="container">
        <div class="navbar">
            <!-- Logo -->
            <a href="<?= SITE_URL ?>/" class="navbar-brand">
                <img src="<?= SITE_URL ?>/assets/images/logo-faj.png" alt="FAJ Niger" class="logo">
                <div class="logo-text">
                    <span class="logo-title">F.A.J</span>
                    <span class="logo-subtitle">Fonds d'Appui à la Justice</span>
                </div>
            </a>
            
            <!-- Menu -->
            <nav class="navbar-nav">
                <ul class="nav-list">
                    <li class="nav-item"><a href="<?= SITE_URL ?>/" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Accueil</a></li>
                    <li class="nav-item"><a href="<?= SITE_URL ?>/pages/a-propos.php" class="nav-link">À Propos</a></li>
                    <li class="nav-item dropdown">
                        <a href="<?= SITE_URL ?>/pages/projets.php" class="nav-link">Projets <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= SITE_URL ?>/pages/projets.php?cat=infrastructure">Infrastructures</a></li>
                            <li><a href="<?= SITE_URL ?>/pages/projets.php?cat=formation">Formation</a></li>
                            <li><a href="<?= SITE_URL ?>/pages/projets.php?cat=humanisation">Humanisation</a></li>
                            <li><a href="<?= SITE_URL ?>/pages/projets.php?cat=acces_justice">Accès à la Justice</a></li>
                            <li><a href="<?= SITE_URL ?>/pages/projets.php?cat=numerisation">Numérisation</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a href="<?= SITE_URL ?>/pages/actualites.php" class="nav-link">Actualités</a></li>
                    <li class="nav-item"><a href="<?= SITE_URL ?>/pages/equipe.php" class="nav-link">Équipe</a></li>
                    <li class="nav-item"><a href="<?= SITE_URL ?>/pages/contact.php" class="nav-link">Contact</a></li>
                </ul>
            </nav>
            
            <!-- CTA Button -->
            <div class="navbar-cta">
                <a href="<?= SITE_URL ?>/pages/don.php" class="btn btn-primary btn-donate">
                    <i class="fas fa-heart"></i> Faire un Don
                </a>
            </div>
            
            <!-- Mobile Toggle -->
            <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
