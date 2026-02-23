<?php
// Configuration principale du site FAJ
define('SITE_NAME', 'Fonds d\'Appui à la Justice');
define('SITE_ABBR', 'F.A.J');
define('SITE_URL', 'http://localhost'); // À modifier en production
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

// Connexion PDO
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die("Erreur DB: " . $e->getMessage());
            } else {
                die("Erreur de connexion à la base de données.");
            }
        }
    }
    return $pdo;
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
