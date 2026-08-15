<?php
/**
 * Sitemap XML dynamique — FAJ Niger.
 * Généré à partir des routes publiques et des projets/actualités en base.
 */
require_once __DIR__ . '/includes/config.php';
header('Content-Type: application/xml; charset=utf-8');

$base = rtrim(SITE_URL, '/');
$urls = [
    ['/', '1.0', 'daily'],
    ['/a-propos', '0.8', 'monthly'],
    ['/gouvernance', '0.7', 'monthly'],
    ['/cadre-financier', '0.7', 'monthly'],
    ['/projets', '0.8', 'weekly'],
    ['/actualites', '0.7', 'weekly'],
    ['/equipe', '0.5', 'monthly'],
    ['/contact', '0.6', 'monthly'],
    ['/don', '0.9', 'monthly'],
    ['/faq', '0.4', 'monthly'],
    ['/mentions-legales', '0.3', 'yearly'],
    ['/politique-confidentialite', '0.3', 'yearly'],
    ['/conditions-generales', '0.3', 'yearly'],
];

try {
    $pdo = getDB();
    foreach ($pdo->query("SELECT slug FROM projets WHERE statut='actif'") as $r) {
        $urls[] = ['/projets/' . $r['slug'], '0.6', 'monthly'];
    }
    foreach ($pdo->query("SELECT slug FROM actualites WHERE statut='publie'") as $r) {
        $urls[] = ['/actualites/' . $r['slug'], '0.5', 'monthly'];
    }
} catch (Throwable $e) { /* silencieux */ }

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as [$loc, $prio, $freq]) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($base . $loc, ENT_XML1) . "</loc>\n";
    echo "    <changefreq>{$freq}</changefreq>\n";
    echo "    <priority>{$prio}</priority>\n";
    echo "  </url>\n";
}
echo '</urlset>' . "\n";
