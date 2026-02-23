<?php
// Configuration principale du site FAJ
define('SITE_NAME', 'Fonds d\'Appui à la Justice');
define('SITE_ABBR', 'F.A.J');
define('SITE_URL', 'https://3000-ie7yyfrxzjw94sky3utmf-cbeee0f9.sandbox.novita.ai'); // À modifier en production
define('SITE_EMAIL', 'contact@faj.ne');
define('SITE_PHONE', '+227 20 XX XX XX');
define('SITE_ADDRESS', 'Niamey, Niger');

// Base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'faj_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Chemins
define('BASE_PATH', dirname(__DIR__));
define('ASSETS_PATH', BASE_PATH . '/assets');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('UPLOADS_URL', SITE_URL . '/uploads');

// Sécurité
define('ADMIN_SECRET', 'faj_admin_2024_secure');
define('SESSION_TIMEOUT', 3600); // 1 heure

// Paiement - CinetPay (adapté Afrique de l'Ouest)
define('CINETPAY_APIKEY', 'VOTRE_APIKEY_CINETPAY');
define('CINETPAY_SITE_ID', 'VOTRE_SITE_ID');
define('CINETPAY_BASE_URL', 'https://api-checkout.cinetpay.com/v2/payment');

// Paiement - Stripe (Visa/MasterCard International)
define('STRIPE_PUBLIC_KEY', 'pk_test_VOTRE_CLE_PUBLIQUE_STRIPE');
define('STRIPE_SECRET_KEY', 'sk_test_VOTRE_CLE_SECRETE_STRIPE');

// Paiement - PayDunya (Afrique de l'Ouest)
define('PAYDUNYA_MASTER_KEY', 'VOTRE_MASTER_KEY_PAYDUNYA');
define('PAYDUNYA_PUBLIC_KEY', 'VOTRE_PUBLIC_KEY_PAYDUNYA');
define('PAYDUNYA_PRIVATE_KEY', 'VOTRE_PRIVATE_KEY_PAYDUNYA');
define('PAYDUNYA_TOKEN', 'VOTRE_TOKEN_PAYDUNYA');

// Email (PHPMailer ou SMTP)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@faj.ne');
define('SMTP_PASS', 'votre_mot_de_passe');

// Mode débogage
define('DEBUG_MODE', false);

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion PDO avec SQLite fallback
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        // Essayer MySQL d'abord
        if (DB_HOST !== 'localhost_disabled') {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                return $pdo;
            } catch (PDOException $e) {
                // Fallback vers SQLite si MySQL non disponible
            }
        }
        
        // SQLite fallback (pour développement/test)
        try {
            $sqlite_path = BASE_PATH . '/faj_data.sqlite';
            $pdo = new PDO('sqlite:' . $sqlite_path);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Créer les tables si elles n'existent pas
            initSQLiteDB($pdo);
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die("Erreur DB SQLite: " . $e->getMessage());
            } else {
                die("Erreur de connexion à la base de données.");
            }
        }
    }
    return $pdo;
}

function initSQLiteDB($pdo) {
    // Vérifier si déjà initialisé
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='parametres'")->fetchColumn();
    if ($tables) return;
    
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        mot_de_passe TEXT NOT NULL,
        role TEXT DEFAULT 'admin',
        actif INTEGER DEFAULT 1,
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
    ");
    
    // Données initiales
    $pdo->exec("INSERT OR IGNORE INTO admins (nom, email, mot_de_passe, role) VALUES 
        ('Super Administrateur', 'admin@faj.ne', '\$2y\$12\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');");
    
    $params = [
        ['site_nom', 'Fonds d\'Appui à la Justice'],
        ['site_slogan', 'Ensemble pour une Justice accessible à tous'],
        ['site_description', 'Le FAJ collecte des fonds pour moderniser et améliorer le système judiciaire du Niger'],
        ['site_email', 'contact@faj.ne'],
        ['site_telephone', '+227 20 XX XX XX'],
        ['site_adresse', 'Niamey, Niger'],
        ['site_facebook', 'https://facebook.com/fajniger'],
        ['site_twitter', 'https://twitter.com/fajniger'],
        ['site_linkedin', 'https://linkedin.com/company/fajniger'],
        ['site_youtube', ''],
        ['hero_titre', 'Votre don peut <span>changer des vies</span>'],
        ['hero_sous_titre', 'Participez à la modernisation du système judiciaire du Niger. Ensemble, nous pouvons garantir une justice accessible, équitable et transparente pour tous.'],
        ['a_propos_titre', 'Pour une Justice <span>Accessible à Tous</span>'],
        ['a_propos_texte', '<p>Le <strong>Fonds d\'Appui à la Justice (FAJ)</strong> est un mécanisme de financement innovant créé pour soutenir la modernisation et l\'amélioration du système judiciaire du Niger.</p><p>Notre mission est de mobiliser des ressources financières auprès de la société civile, des entreprises et des partenaires internationaux pour financer des projets structurants dans le domaine de la justice.</p>'],
    ];
    
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO parametres (cle, valeur) VALUES (?, ?)");
    foreach ($params as $p) $stmt->execute($p);
    
    // Projets exemples
    $projets = [
        ['Construction et Équipement de Tribunaux', 'construction-equipement-tribunaux', 'Financement de la construction et équipement des tribunaux dans les régions du Niger', 'infrastructure', 150000000, 0],
        ['Formation des Acteurs Judiciaires', 'formation-acteurs-judiciaires', 'Renforcement des capacités des magistrats, avocats et auxiliaires de justice', 'formation', 80000000, 0],
        ['Humanisation du Milieu Carcéral', 'humanisation-milieu-carceral', 'Amélioration des conditions de détention et réinsertion sociale des détenus', 'humanisation', 100000000, 0],
        ['Accès à la Justice pour les Vulnérables', 'acces-justice-vulnerables', 'Aide juridictionnelle gratuite pour les personnes démunies', 'acces_justice', 60000000, 0],
        ['Numérisation du Système Judiciaire', 'numerisation-systeme-judiciaire', 'Modernisation et digitalisation des archives et procédures judiciaires', 'numerisation', 120000000, 0],
    ];
    
    $ps = $pdo->prepare("INSERT OR IGNORE INTO projets (titre, slug, description_courte, categorie, objectif_montant, priorite) VALUES (?,?,?,?,?,?)");
    foreach ($projets as $i => $p) { array_push($p, $i+1); $ps->execute($p); }
    
    // Témoignages
    $temoignages = [
        ['Alhaji Moussa', 'Commerçant, Niamey', 'Grâce au FAJ, j\'ai pu avoir accès à l\'aide juridictionnelle. Une initiative vraiment importante pour nous.', 5],
        ['Mme Mariama', 'Enseignante, Zinder', 'Le FAJ travaille pour que la justice ne soit plus un luxe réservé aux riches. Je soutiens cette cause.', 5],
        ['Dr. Ibrahim', 'Médecin, Agadez', 'La modernisation du système judiciaire est essentielle pour le développement du Niger.', 5],
    ];
    $ts = $pdo->prepare("INSERT OR IGNORE INTO temoignages (nom, poste, contenu, note) VALUES (?,?,?,?)");
    foreach ($temoignages as $t) $ts->execute($t);
    
    // Statistiques
    $stats = [['total_donateurs',0,'Donateurs'],['total_dons',0,'Dons collectés'],['total_projets',5,'Projets Actifs'],['total_beneficiaires',0,'Bénéficiaires']];
    $ss = $pdo->prepare("INSERT OR IGNORE INTO statistiques (cle, valeur, label) VALUES (?,?,?)");
    foreach ($stats as $s) $ss->execute($s);
}

// Récupérer un paramètre du site
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

// Fonction utilitaires
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function isAdmin() {
    return isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect(SITE_URL . '/admin/login.php');
    }
}

function formatMontant($montant, $devise = 'XOF') {
    if ($devise === 'XOF') {
        return number_format($montant, 0, ',', ' ') . ' FCFA';
    }
    return number_format($montant, 2, ',', ' ') . ' ' . $devise;
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    if ($time < 60) return 'Il y a quelques secondes';
    if ($time < 3600) return 'Il y a ' . floor($time/60) . ' min';
    if ($time < 86400) return 'Il y a ' . floor($time/3600) . 'h';
    return date('d/m/Y', strtotime($datetime));
}
?>
