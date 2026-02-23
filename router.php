<?php
/**
 * Routeur PHP pour le serveur de développement
 * Gère les URLs propres (sans extension .php)
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Retirer le slash final
$path = rtrim($path, '/');

// Servir les fichiers statiques directement
if ($path !== '' && file_exists(__DIR__ . $path)) {
    // Fichier réel (CSS, JS, images...) → laisser PHP le servir
    if (!is_dir(__DIR__ . $path)) {
        return false;
    }
}

// ─── Table de routage ────────────────────────────────────────────────────────
$routes = [
    // Pages principales
    '/a-propos'                => '/pages/a-propos.php',
    '/projets'                 => '/pages/projets.php',
    '/actualites'              => '/pages/actualites.php',
    '/contact'                 => '/pages/contact.php',
    '/don'                     => '/pages/don.php',
    '/equipe'                  => '/pages/equipe.php',
    '/don-succes'              => '/pages/don-succes.php',
    '/don-annule'              => '/pages/don-annule.php',
    '/conditions-generales'    => '/pages/conditions-generales.php',
    '/politique-confidentialite'=> '/pages/politique-confidentialite.php',
    '/faq'                     => '/pages/faq.php',

    // Admin
    '/admin'                   => '/admin/index.php',
    '/admin/login'             => '/admin/login.php',
    '/admin/logout'            => '/admin/logout.php',
    '/admin/dashboard'         => '/admin/dashboard.php',
    '/admin/dons'              => '/admin/dons/liste.php',
    '/admin/projets'           => '/admin/projets/liste.php',
    '/admin/actualites'        => '/admin/actualites/liste.php',
    '/admin/equipe'            => '/admin/equipe/liste.php',
    '/admin/contacts'          => '/admin/contacts/liste.php',
    '/admin/parametres'        => '/admin/parametres.php',

    // API (sans extension)
    '/api/stats'               => '/api/stats.php',
    '/api/don'                 => '/api/don.php',
    '/api/newsletter'          => '/api/newsletter.php',
    '/api/webhook-cinetpay'    => '/api/webhook-cinetpay.php',
];

// Routes avec paramètres dynamiques
if (preg_match('#^/projets/([a-z0-9-]+)$#', $path, $m)) {
    $_GET['slug'] = $m[1];
    chdir(__DIR__ . '/pages');
    require __DIR__ . '/pages/projet-detail.php';
    exit;
}
if (preg_match('#^/actualites/([a-z0-9-]+)$#', $path, $m)) {
    $_GET['slug'] = $m[1];
    chdir(__DIR__ . '/pages');
    require __DIR__ . '/pages/actualite-detail.php';
    exit;
}

// Chercher une route exacte
if (isset($routes[$path])) {
    $target = __DIR__ . $routes[$path];
    if (file_exists($target)) {
        // Changer le répertoire courant pour que les chemins relatifs fonctionnent
        chdir(dirname($target));
        require $target;
        exit;
    }
}

// Racine → index.php
if ($path === '' || $path === '/') {
    chdir(__DIR__);
    require __DIR__ . '/index.php';
    exit;
}

// Fichier PHP avec extension dans l'URL → laisser passer
if (preg_match('/\.php$/', $path) && file_exists(__DIR__ . $path)) {
    return false;
}

// 404 par défaut
http_response_code(404);
if (file_exists(__DIR__ . '/pages/404.php')) {
    chdir(__DIR__ . '/pages');
    require __DIR__ . '/pages/404.php';
} else {
    echo '<h1>404 – Page non trouvée</h1>';
}
