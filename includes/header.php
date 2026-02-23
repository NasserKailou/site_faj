<?php
require_once __DIR__ . '/config.php';

$site_nom    = getSiteParam('site_nom',    'Fonds d\'Appui à la Justice');
$site_slogan = getSiteParam('site_slogan', 'Ensemble pour une Justice accessible à tous');

// Déterminer la page active
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$current_path = strtok($current_path, '?');
function isActive(string $path): string {
    global $current_path;
    return ($current_path === $path || strpos($current_path, $path) === 0) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= getSiteParam('site_description', 'Fonds d\'Appui à la Justice du Niger') ?>">
    <meta property="og:title"       content="<?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?>FAJ Niger">
    <meta property="og:description" content="<?= getSiteParam('site_description', 'Fonds d\'Appui à la Justice du Niger') ?>">
    <meta property="og:image"       content="<?= SITE_URL ?>/assets/images/logo-faj.png">
    <meta property="og:url"         content="<?= SITE_URL ?>">
    <meta property="og:type"        content="website">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' | ' : '' ?>FAJ – Fonds d'Appui à la Justice du Niger</title>

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

    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- CSS principal -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">

    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>
<body>

<!-- Barre supérieure -->
<div class="topbar">
    <div class="container">
        <div class="topbar-left">
            <a href="mailto:<?= getSiteParam('site_email', 'contact@faj.ne') ?>">
                <i class="fas fa-envelope"></i>
                <?= getSiteParam('site_email', 'contact@faj.ne') ?>
            </a>
            <a href="tel:<?= preg_replace('/\s/', '', getSiteParam('site_telephone', '')) ?>">
                <i class="fas fa-phone"></i>
                <?= getSiteParam('site_telephone', '+227 20 XX XX XX') ?>
            </a>
        </div>
        <div class="topbar-right">
            <?php $fb = getSiteParam('site_facebook'); if ($fb): ?>
            <a href="<?= htmlspecialchars($fb) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <i class="fab fa-facebook-f"></i>
            </a>
            <?php endif; ?>
            <?php $tw = getSiteParam('site_twitter'); if ($tw): ?>
            <a href="<?= htmlspecialchars($tw) ?>" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X">
                <i class="fab fa-x-twitter"></i>
            </a>
            <?php endif; ?>
            <?php $li = getSiteParam('site_linkedin'); if ($li): ?>
            <a href="<?= htmlspecialchars($li) ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <?php endif; ?>
            <?php $yt = getSiteParam('site_youtube'); if ($yt): ?>
            <a href="<?= htmlspecialchars($yt) ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                <i class="fab fa-youtube"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Navigation principale -->
<header class="header" id="header">
    <div class="container">
        <div class="navbar">
            <!-- Logo -->
            <a href="<?= SITE_URL ?>/" class="navbar-brand">
                <img src="<?= SITE_URL ?>/assets/images/logo-faj.png" alt="FAJ Niger Logo" class="logo">
                <div class="logo-text">
                    <span class="logo-title">F.A.J</span>
                    <span class="logo-subtitle">Fonds d'Appui à la Justice</span>
                </div>
            </a>

            <!-- Menu de navigation (URLs sans .php) -->
            <nav class="navbar-nav" id="mainNav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="<?= SITE_URL ?>/" class="nav-link <?= isActive('/') === 'active' && $current_path === '/' ? 'active' : '' ?>">
                            Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= SITE_URL ?>/a-propos" class="nav-link <?= isActive('/a-propos') ?>">
                            À Propos
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="<?= SITE_URL ?>/projets" class="nav-link <?= isActive('/projets') ?>">
                            Projets <i class="fas fa-chevron-down fa-xs"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= SITE_URL ?>/projets?cat=infrastructure">
                                <i class="fas fa-building"></i> Infrastructures
                            </a></li>
                            <li><a href="<?= SITE_URL ?>/projets?cat=formation">
                                <i class="fas fa-graduation-cap"></i> Formation
                            </a></li>
                            <li><a href="<?= SITE_URL ?>/projets?cat=humanisation">
                                <i class="fas fa-heart"></i> Humanisation
                            </a></li>
                            <li><a href="<?= SITE_URL ?>/projets?cat=acces_justice">
                                <i class="fas fa-balance-scale"></i> Accès à la Justice
                            </a></li>
                            <li><a href="<?= SITE_URL ?>/projets?cat=numerisation">
                                <i class="fas fa-laptop"></i> Numérisation
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="<?= SITE_URL ?>/actualites" class="nav-link <?= isActive('/actualites') ?>">
                            Actualités
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= SITE_URL ?>/equipe" class="nav-link <?= isActive('/equipe') ?>">
                            Équipe
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= SITE_URL ?>/contact" class="nav-link <?= isActive('/contact') ?>">
                            Contact
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Bouton Don -->
            <div class="navbar-cta">
                <a href="<?= SITE_URL ?>/don" class="btn btn-primary btn-donate">
                    <i class="fas fa-heart"></i>
                    <span>Faire un Don</span>
                </a>
            </div>

            <!-- Toggle mobile -->
            <button class="mobile-toggle" id="mobileToggle" aria-label="Ouvrir le menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
