<?php require_once dirname(__DIR__, 2) . '/includes/config.php'; requireAdmin(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' | ' : '' ?>Admin FAJ Niger</title>
    <link rel="icon" type="image/png" href="<?= SITE_URL ?>/assets/images/logo-faj.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/admin/assets/css/admin.css">
</head>
<body class="admin-body">

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <img src="<?= SITE_URL ?>/assets/images/logo-faj.png" alt="FAJ" class="sidebar-logo">
        <div>
            <span class="sidebar-brand">F.A.J</span>
            <span class="sidebar-tagline">Administration</span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <span class="nav-section-label">Principal</span>
            <a href="<?= SITE_URL ?>/admin/dashboard" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'dashboard') !== false) ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> <span>Tableau de Bord</span>
            </a>
        </div>
        
        <div class="nav-section">
            <span class="nav-section-label">Collecte de Fonds</span>
            <a href="<?= SITE_URL ?>/admin/dons" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'/dons/') !== false) ? 'active' : '' ?>">
                <i class="fas fa-hand-holding-heart"></i> <span>Dons</span>
                <?php
                try {
                    $nb_attente = getDB()->query("SELECT COUNT(*) FROM dons WHERE statut='en_attente'")->fetchColumn();
                    if ($nb_attente > 0) echo '<span class="nav-badge warning">' . $nb_attente . '</span>';
                } catch(Exception $e) {}
                ?>
            </a>
        </div>
        
        <div class="nav-section">
            <span class="nav-section-label">Contenu du site</span>
            <a href="<?= SITE_URL ?>/admin/projets" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'/projets/') !== false) ? 'active' : '' ?>">
                <i class="fas fa-project-diagram"></i> <span>Projets</span>
            </a>
            <a href="<?= SITE_URL ?>/admin/actualites" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'/actualites/') !== false) ? 'active' : '' ?>">
                <i class="fas fa-newspaper"></i> <span>Actualités</span>
            </a>
            <a href="<?= SITE_URL ?>/admin/equipe" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'/equipe/') !== false) ? 'active' : '' ?>">
                <i class="fas fa-users"></i> <span>Équipe</span>
            </a>
            <a href="<?= SITE_URL ?>/admin/partenaires" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'/partenaires/') !== false) ? 'active' : '' ?>">
                <i class="fas fa-handshake"></i> <span>Partenaires</span>
            </a>
            <a href="<?= SITE_URL ?>/admin/temoignages" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'/temoignages/') !== false) ? 'active' : '' ?>">
                <i class="fas fa-quote-left"></i> <span>Témoignages</span>
            </a>
        </div>
        
        <div class="nav-section">
            <span class="nav-section-label">Communication</span>
            <a href="<?= SITE_URL ?>/admin/contacts" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'/contacts/') !== false) ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i> <span>Messages</span>
                <?php
                try {
                    $nb_messages = getDB()->query("SELECT COUNT(*) FROM contacts WHERE lu=0")->fetchColumn();
                    if ($nb_messages > 0) echo '<span class="nav-badge danger">' . $nb_messages . '</span>';
                } catch(Exception $e) {}
                ?>
            </a>
            <a href="<?= SITE_URL ?>/admin/newsletter" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'/newsletter/') !== false) ? 'active' : '' ?>">
                <i class="fas fa-at"></i> <span>Newsletter</span>
            </a>
        </div>
        
        <div class="nav-section">
            <span class="nav-section-label">Configuration</span>
            <a href="<?= SITE_URL ?>/admin/parametres" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'parametres') !== false) ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> <span>Paramètres</span>
            </a>
            <a href="<?= SITE_URL ?>/admin/admins" class="nav-link <?= (strpos($_SERVER['PHP_SELF'],'/admins/') !== false) ? 'active' : '' ?>">
                <i class="fas fa-user-shield"></i> <span>Administrateurs</span>
            </a>
        </div>
    </nav>
    
    <!-- User info -->
    <div class="sidebar-user">
        <div class="user-avatar"><?= strtoupper(substr($_SESSION['admin_nom'] ?? 'A', 0, 1)) ?></div>
        <div class="user-info">
            <span class="user-name"><?= sanitize($_SESSION['admin_nom'] ?? 'Admin') ?></span>
            <span class="user-role"><?= ucfirst(str_replace('_',' ',$_SESSION['admin_role'] ?? 'admin')) ?></span>
        </div>
        <a href="<?= SITE_URL ?>/admin/logout" title="Déconnexion" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</aside>

<!-- Main Content -->
<main class="admin-main" id="adminMain">
    <!-- Top bar -->
    <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-breadcrumb">
            <a href="<?= SITE_URL ?>/admin/dashboard">Admin</a>
            <?php if (isset($page_title) && $page_title !== 'Tableau de Bord'): ?>
            <i class="fas fa-chevron-right"></i>
            <span><?= $page_title ?></span>
            <?php endif; ?>
        </div>
        <div class="topbar-actions">
            <a href="<?= SITE_URL ?>/" target="_blank" class="topbar-action-btn" title="Voir le site public">
                <i class="fas fa-external-link-alt"></i>
            </a>
            <a href="<?= SITE_URL ?>/admin/logout" class="topbar-action-btn" title="Déconnexion">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
    
    <div class="admin-content">
