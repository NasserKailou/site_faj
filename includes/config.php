<?php
/**
 * Configuration principale FAJ Niger - Version sécurisée
 * Sécurité : CSRF, XSS, SQLi, rate limiting, session sécurisée
 */

// ─── Paramètres du site ──────────────────────────────────────────────────────
define('SITE_NAME',    'Fonds d\'Appui à la Justice');
define('SITE_ABBR',    'F.A.J');
define('SITE_URL',     'https://3000-ie7yyfrxzjw94sky3utmf-cbeee0f9.sandbox.novita.ai');
define('SITE_EMAIL',   'contact@faj.ne');
define('SITE_PHONE',   '+227 20 XX XX XX');
define('SITE_ADDRESS', 'Niamey, Niger');

// ─── Base de données ─────────────────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'faj_db');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// ─── Chemins ─────────────────────────────────────────────────────────────────
define('BASE_PATH',    dirname(__DIR__));
define('ASSETS_PATH',  BASE_PATH . '/assets');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('UPLOADS_URL',  SITE_URL . '/uploads');

// ─── Sécurité ────────────────────────────────────────────────────────────────
define('ADMIN_SECRET',      'faj_admin_2024_secure_!@#$%');
define('SESSION_TIMEOUT',   3600);          // 1 heure
define('CSRF_TOKEN_NAME',   'faj_csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME',      900);           // 15 min en secondes
define('RATE_LIMIT_WINDOW', 60);            // Fenêtre 60 sec
define('RATE_LIMIT_MAX',    30);            // 30 req / fenêtre

// ─── Clés de paiement (à remplacer par les vraies clés en production) ────────
define('CINETPAY_APIKEY',   'VOTRE_APIKEY_CINETPAY');
define('CINETPAY_SITE_ID',  'VOTRE_SITE_ID');
define('CINETPAY_BASE_URL', 'https://api-checkout.cinetpay.com/v2/payment');

define('STRIPE_PUBLIC_KEY', 'pk_test_VOTRE_CLE_PUBLIQUE_STRIPE');
define('STRIPE_SECRET_KEY', 'sk_test_VOTRE_CLE_SECRETE_STRIPE');

define('PAYDUNYA_MASTER_KEY',  'VOTRE_MASTER_KEY_PAYDUNYA');
define('PAYDUNYA_PUBLIC_KEY',  'VOTRE_PUBLIC_KEY_PAYDUNYA');
define('PAYDUNYA_PRIVATE_KEY', 'VOTRE_PRIVATE_KEY_PAYDUNYA');
define('PAYDUNYA_TOKEN',       'VOTRE_TOKEN_PAYDUNYA');

// ─── Email ───────────────────────────────────────────────────────────────────
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@faj.ne');
define('SMTP_PASS', 'votre_mot_de_passe');

// ─── Mode débogage ───────────────────────────────────────────────────────────
define('DEBUG_MODE', false);

// ─── Erreurs PHP ─────────────────────────────────────────────────────────────
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/php_errors.log');
}

// ─── Session sécurisée ───────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly',  1);
    ini_set('session.cookie_secure',    0);   // Mettre 1 en production HTTPS
    ini_set('session.cookie_samesite',  'Strict');
    ini_set('session.use_strict_mode',  1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.gc_maxlifetime',   SESSION_TIMEOUT);
    session_name('FAJ_SESS');
    session_start();
    
    // Régénération de l'ID de session toutes les 30 min
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
    }
    
    // Vérification expiration de session
    if (isset($_SESSION['admin_logged']) && isset($_SESSION['_last_activity'])) {
        if (time() - $_SESSION['_last_activity'] > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            session_start();
        }
    }
    if (isset($_SESSION['admin_logged'])) {
        $_SESSION['_last_activity'] = time();
    }
    
    // Fixation de session : vérifier l'IP et user-agent
    $current_fingerprint = md5(
        ($_SERVER['HTTP_USER_AGENT'] ?? '') .
        (isset($_SERVER['REMOTE_ADDR']) ? substr($_SERVER['REMOTE_ADDR'], 0, 8) : '')
    );
    if (isset($_SESSION['_fingerprint'])) {
        if ($_SESSION['_fingerprint'] !== $current_fingerprint) {
            session_unset();
            session_destroy();
            session_start();
        }
    } else {
        $_SESSION['_fingerprint'] = $current_fingerprint;
    }
}

// ─── Connexion PDO sécurisée ─────────────────────────────────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    // Essayer MySQL d'abord
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,   // IMPORTANT : vraies requêtes préparées
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Fallback SQLite
    }

    // SQLite fallback
    try {
        $sqlite_path = BASE_PATH . '/faj_data.sqlite';
        $pdo = new PDO('sqlite:' . $sqlite_path, '', '', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec("PRAGMA journal_mode=WAL");
        $pdo->exec("PRAGMA foreign_keys=ON");
        initSQLiteDB($pdo);
        return $pdo;
    } catch (PDOException $e) {
        die(DEBUG_MODE ? "Erreur DB : " . $e->getMessage() : "Erreur de connexion à la base de données.");
    }
}

function initSQLiteDB(PDO $pdo): void {
    $exists = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='parametres'")->fetchColumn();
    if ($exists) return;

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        mot_de_passe TEXT NOT NULL,
        role TEXT DEFAULT 'admin',
        actif INTEGER DEFAULT 1,
        tentatives_connexion INTEGER DEFAULT 0,
        bloque_jusqu INTEGER DEFAULT 0,
        derniere_connexion TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS parametres (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cle TEXT UNIQUE NOT NULL,
        valeur TEXT,
        type TEXT DEFAULT 'texte',
        groupe TEXT DEFAULT 'general',
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS projets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titre TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        description_courte TEXT,
        description_longue TEXT,
        categorie TEXT DEFAULT 'autre',
        objectif_montant REAL DEFAULT 0,
        montant_collecte REAL DEFAULT 0,
        image TEXT,
        statut TEXT DEFAULT 'actif',
        priorite INTEGER DEFAULT 0,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS dons (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        reference TEXT UNIQUE NOT NULL,
        donateur_nom TEXT NOT NULL,
        donateur_email TEXT NOT NULL,
        donateur_telephone TEXT,
        donateur_pays TEXT DEFAULT 'Niger',
        montant REAL NOT NULL,
        devise TEXT DEFAULT 'XOF',
        methode_paiement TEXT NOT NULL,
        transaction_id TEXT,
        statut TEXT DEFAULT 'en_attente',
        projet_id INTEGER,
        message TEXT,
        anonyme INTEGER DEFAULT 0,
        ip_address TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS actualites (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        titre TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        contenu TEXT,
        extrait TEXT,
        image TEXT,
        categorie TEXT,
        statut TEXT DEFAULT 'publie',
        en_vedette INTEGER DEFAULT 0,
        nb_vues INTEGER DEFAULT 0,
        admin_id INTEGER,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS equipe (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        poste TEXT NOT NULL,
        biographie TEXT,
        photo TEXT,
        email TEXT,
        linkedin TEXT,
        ordre INTEGER DEFAULT 0,
        actif INTEGER DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS partenaires (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        logo TEXT NOT NULL,
        site_web TEXT,
        type TEXT DEFAULT 'institutionnel',
        ordre INTEGER DEFAULT 0,
        actif INTEGER DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS temoignages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        poste TEXT,
        photo TEXT,
        contenu TEXT NOT NULL,
        note INTEGER DEFAULT 5,
        actif INTEGER DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS contacts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        email TEXT NOT NULL,
        telephone TEXT,
        sujet TEXT NOT NULL,
        message TEXT NOT NULL,
        lu INTEGER DEFAULT 0,
        repondu INTEGER DEFAULT 0,
        ip_address TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS newsletter (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        nom TEXT,
        actif INTEGER DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS statistiques (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        cle TEXT UNIQUE NOT NULL,
        valeur INTEGER DEFAULT 0,
        label TEXT,
        icone TEXT,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS rate_limits (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ip TEXT NOT NULL,
        action TEXT NOT NULL,
        compteur INTEGER DEFAULT 1,
        fenetre_debut INTEGER NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    );
    CREATE INDEX IF NOT EXISTS idx_rate_limits ON rate_limits(ip, action, fenetre_debut);
    ");

    // Admin par défaut (mot de passe : Admin@FAJ2024!)
    $pdo->exec("INSERT OR IGNORE INTO admins (nom, email, mot_de_passe, role) VALUES 
        ('Super Administrateur', 'admin@faj.ne', '\$2y\$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');");

    $params = [
        ['site_nom',          'Fonds d\'Appui à la Justice'],
        ['site_slogan',       'Ensemble pour une Justice accessible à tous'],
        ['site_description',  'Le FAJ collecte des fonds pour moderniser et améliorer le système judiciaire du Niger'],
        ['site_email',        'contact@faj.ne'],
        ['site_telephone',    '+227 20 XX XX XX'],
        ['site_adresse',      'Niamey, Niger'],
        ['site_facebook',     'https://facebook.com/fajniger'],
        ['site_twitter',      'https://twitter.com/fajniger'],
        ['site_linkedin',     'https://linkedin.com/company/fajniger'],
        ['site_youtube',      ''],
        ['hero_titre',        'Votre don peut <span>changer des vies</span>'],
        ['hero_sous_titre',   'Participez à la modernisation du système judiciaire du Niger. Ensemble, nous pouvons garantir une justice accessible, équitable et transparente pour tous.'],
        ['a_propos_titre',    'Pour une Justice <span>Accessible à Tous</span>'],
        ['a_propos_texte',    '<p>Le <strong>Fonds d\'Appui à la Justice (FAJ)</strong> est un mécanisme de financement innovant créé pour soutenir la modernisation et l\'amélioration du système judiciaire du Niger.</p><p>Notre mission est de mobiliser des ressources financières auprès de la société civile, des entreprises et des partenaires internationaux pour financer des projets structurants dans le domaine de la justice.</p>'],
    ];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO parametres (cle, valeur) VALUES (?, ?)");
    foreach ($params as $p) $stmt->execute($p);

    $projets = [
        ['Construction et Équipement de Tribunaux', 'construction-equipement-tribunaux', 'Financement de la construction et équipement des tribunaux dans les régions du Niger', 'infrastructure', 150000000, 1],
        ['Formation des Acteurs Judiciaires',       'formation-acteurs-judiciaires',       'Renforcement des capacités des magistrats, avocats et auxiliaires de justice',          'formation',      80000000,  2],
        ['Humanisation du Milieu Carcéral',         'humanisation-milieu-carceral',         'Amélioration des conditions de détention et réinsertion sociale des détenus',            'humanisation',   100000000, 3],
        ['Accès à la Justice pour les Vulnérables', 'acces-justice-vulnerables',            'Aide juridictionnelle gratuite pour les personnes démunies',                            'acces_justice',  60000000,  4],
        ['Numérisation du Système Judiciaire',      'numerisation-systeme-judiciaire',      'Modernisation et digitalisation des archives et procédures judiciaires',                'numerisation',   120000000, 5],
    ];
    $ps = $pdo->prepare("INSERT OR IGNORE INTO projets (titre, slug, description_courte, categorie, objectif_montant, priorite) VALUES (?,?,?,?,?,?)");
    foreach ($projets as $p) $ps->execute($p);

    $temoignages = [
        ['Alhaji Moussa', 'Commerçant, Niamey',  'Grâce au FAJ, j\'ai pu avoir accès à l\'aide juridictionnelle. Une initiative vraiment importante pour nous.', 5],
        ['Mme Mariama',   'Enseignante, Zinder',  'Le FAJ travaille pour que la justice ne soit plus un luxe réservé aux riches. Je soutiens cette cause.',        5],
        ['Dr. Ibrahim',   'Médecin, Agadez',      'La modernisation du système judiciaire est essentielle pour le développement du Niger.',                         5],
    ];
    $ts = $pdo->prepare("INSERT OR IGNORE INTO temoignages (nom, poste, contenu, note) VALUES (?,?,?,?)");
    foreach ($temoignages as $t) $ts->execute($t);

    $stats = [
        ['total_donateurs',    0, 'Donateurs',     'fas fa-users'],
        ['total_dons',         0, 'Dons collectés', 'fas fa-hand-holding-heart'],
        ['total_projets',      5, 'Projets Actifs', 'fas fa-project-diagram'],
        ['total_beneficiaires',0, 'Bénéficiaires',  'fas fa-balance-scale'],
    ];
    $ss = $pdo->prepare("INSERT OR IGNORE INTO statistiques (cle, valeur, label, icone) VALUES (?,?,?,?)");
    foreach ($stats as $s) $ss->execute($s);
}

// ─── Fonctions de sécurité ───────────────────────────────────────────────────

/**
 * Génère un jeton CSRF et le stocke en session
 */
function generateCsrfToken(): string {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Vérifie le jeton CSRF soumis
 */
function verifyCsrfToken(string $token): bool {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) return false;
    $valid = hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    // Renouveler le token après chaque vérification (Double-Submit)
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    return $valid;
}

/**
 * Retourne le champ HTML CSRF caché
 */
function csrfField(): string {
    $token = generateCsrfToken();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Rate limiting basé sur SQLite
 */
function rateLimit(string $action, int $max = RATE_LIMIT_MAX, int $window = RATE_LIMIT_WINDOW): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $now = time();
    $fenetre = (int)floor($now / $window) * $window;

    try {
        $pdo = getDB();
        // Nettoyage des anciennes entrées
        $pdo->prepare("DELETE FROM rate_limits WHERE fenetre_debut < ?")->execute([$now - $window * 2]);

        $row = $pdo->prepare("SELECT compteur FROM rate_limits WHERE ip=? AND action=? AND fenetre_debut=?");
        $row->execute([$ip, $action, $fenetre]);
        $r = $row->fetch();

        if ($r) {
            if ($r['compteur'] >= $max) return false; // Limite atteinte
            $pdo->prepare("UPDATE rate_limits SET compteur=compteur+1 WHERE ip=? AND action=? AND fenetre_debut=?")
                ->execute([$ip, $action, $fenetre]);
        } else {
            $pdo->prepare("INSERT INTO rate_limits (ip, action, compteur, fenetre_debut) VALUES (?,?,1,?)")
                ->execute([$ip, $action, $fenetre]);
        }
        return true;
    } catch (Exception $e) {
        return true; // En cas d'erreur DB, ne pas bloquer
    }
}

/**
 * Vérification anti-brute-force pour l'admin login
 */
function checkLoginAttempts(string $email): array {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT tentatives_connexion, bloque_jusqu FROM admins WHERE email=?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        if (!$admin) return ['blocked' => false];

        if ($admin['bloque_jusqu'] && time() < $admin['bloque_jusqu']) {
            $reste = ceil(($admin['bloque_jusqu'] - time()) / 60);
            return ['blocked' => true, 'minutes' => $reste];
        }
        return ['blocked' => false, 'attempts' => $admin['tentatives_connexion']];
    } catch (Exception $e) {
        return ['blocked' => false];
    }
}

function recordFailedLogin(string $email): void {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT tentatives_connexion FROM admins WHERE email=?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        if (!$admin) return;

        $attempts = $admin['tentatives_connexion'] + 1;
        $bloque = $attempts >= MAX_LOGIN_ATTEMPTS ? time() + LOCKOUT_TIME : 0;
        $pdo->prepare("UPDATE admins SET tentatives_connexion=?, bloque_jusqu=? WHERE email=?")
            ->execute([$attempts, $bloque, $email]);
    } catch (Exception $e) {}
}

function resetLoginAttempts(string $email): void {
    try {
        $pdo = getDB();
        $pdo->prepare("UPDATE admins SET tentatives_connexion=0, bloque_jusqu=0, derniere_connexion=? WHERE email=?")
            ->execute([date('Y-m-d H:i:s'), $email]);
    } catch (Exception $e) {}
}

/**
 * Sanitisation avancée (HTML entities + strip_tags)
 */
function sanitize(mixed $input): string {
    if (is_array($input)) return '';
    return htmlspecialchars(strip_tags(trim((string)$input)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Sanitisation HTML enrichi (pour contenu admin)
 */
function sanitizeHtml(string $html): string {
    $allowed = '<p><br><strong><em><ul><ol><li><h2><h3><h4><a><span><blockquote><img>';
    return strip_tags($html, $allowed);
}

/**
 * Valider et chiffrer un numéro de carte de crédit (tokenisation locale)
 * NOTE : En production, utiliser Stripe.js qui ne transmet jamais les données brutes au serveur
 */
function tokenizeCard(string $cardNumber): string {
    $clean = preg_replace('/\D/', '', $cardNumber);
    if (strlen($clean) < 13 || strlen($clean) > 19) return '';
    // Retourner seulement les 4 derniers chiffres (masquage PCI-DSS)
    return str_repeat('*', strlen($clean) - 4) . substr($clean, -4);
}

/**
 * Valider un numéro de carte (algorithme de Luhn)
 */
function validateCard(string $cardNumber): bool {
    $number = preg_replace('/\D/', '', $cardNumber);
    if (strlen($number) < 13 || strlen($number) > 19) return false;

    $sum = 0;
    $alt = false;
    for ($i = strlen($number) - 1; $i >= 0; $i--) {
        $n = intval($number[$i]);
        if ($alt) {
            $n *= 2;
            if ($n > 9) $n -= 9;
        }
        $sum += $n;
        $alt = !$alt;
    }
    return $sum % 10 === 0;
}

/**
 * Détecter le type de carte
 */
function detectCardType(string $cardNumber): string {
    $number = preg_replace('/\D/', '', $cardNumber);
    if (preg_match('/^4/', $number))              return 'visa';
    if (preg_match('/^5[1-5]/', $number))         return 'mastercard';
    if (preg_match('/^2[2-7]/', $number))         return 'mastercard';
    if (preg_match('/^3[47]/', $number))          return 'amex';
    if (preg_match('/^6(?:011|5)/', $number))     return 'discover';
    if (preg_match('/^(?:2131|1800|35)/', $number)) return 'jcb';
    return 'unknown';
}

// ─── Fonctions utilitaires ───────────────────────────────────────────────────

function redirect(string $url): void {
    header('Location: ' . filter_var($url, FILTER_SANITIZE_URL));
    exit;
}

function isAdmin(): bool {
    return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

function requireAdmin(): void {
    if (!isAdmin()) redirect(SITE_URL . '/admin/login');
}

function getSiteParam(string $cle, string $default = ''): string {
    static $cache = [];
    if (isset($cache[$cle])) return $cache[$cle];
    try {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT valeur FROM parametres WHERE cle = ?");
        $stmt->execute([$cle]);
        $r = $stmt->fetch();
        $cache[$cle] = $r ? (string)$r['valeur'] : $default;
    } catch (Exception $e) {
        $cache[$cle] = $default;
    }
    return $cache[$cle];
}

function formatMontant(float $montant, string $devise = 'XOF'): string {
    return $devise === 'XOF'
        ? number_format($montant, 0, ',', ' ') . ' FCFA'
        : number_format($montant, 2, ',', ' ') . ' ' . $devise;
}

function generateToken(int $bytes = 32): string {
    return bin2hex(random_bytes($bytes));
}

function timeAgo(string $datetime): string {
    $time = time() - strtotime($datetime);
    if ($time < 60)    return 'Il y a quelques secondes';
    if ($time < 3600)  return 'Il y a ' . floor($time / 60) . ' min';
    if ($time < 86400) return 'Il y a ' . floor($time / 3600) . 'h';
    return date('d/m/Y', strtotime($datetime));
}

function slugify(string $text): string {
    $text = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $text) ?? mb_strtolower($text);
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Headers de sécurité HTTP (appeler en haut de chaque page)
 */
function setSecurityHeaders(): void {
    if (headers_sent()) return;
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    header("Content-Security-Policy: default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'; img-src 'self' https: data: blob:; font-src 'self' https: data:;");
}

// Appliquer les headers dès le chargement
setSecurityHeaders();

// Créer le dossier logs si nécessaire
@mkdir(BASE_PATH . '/logs', 0750, true);
?>
