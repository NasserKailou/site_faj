<?php
/**
 * API de traitement des dons - FAJ Niger
 * Sécurité : CSRF, rate limiting, validation complète, PCI-DSS
 */
require_once '../includes/config.php';
require_once '../includes/payment/bootstrap.php';

use FAJ\Payment\PaymentFactory;

header('Content-Type: application/json; charset=utf-8');

// Uniquement POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Rate limiting : 10 soumissions de don par fenêtre de 60 secondes par IP
if (!rateLimit('don_submit', 10, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Trop de tentatives. Veuillez patienter quelques minutes.']);
    exit;
}

// Lire les données JSON
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

// ─── Vérification CSRF ───────────────────────────────────────────────────────
$csrfToken = $input[CSRF_TOKEN_NAME] ?? '';
if (!verifyCsrfToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Jeton de sécurité invalide. Veuillez recharger la page.']);
    exit;
}

// ─── Validation des données ──────────────────────────────────────────────────
$errors = [];

// Montant
$montant = isset($input['montant']) ? (int)$input['montant'] : 0;
if ($montant < 500) $errors[] = 'Le montant minimum est de 500 FCFA';
if ($montant > 100000000) $errors[] = 'Montant trop élevé';

// Nom
$nom = sanitize($input['donateur_nom'] ?? '');
if (mb_strlen($nom) < 2)   $errors[] = 'Le nom complet est obligatoire';
if (mb_strlen($nom) > 100) $errors[] = 'Le nom est trop long';

// Email
$email = filter_var(trim($input['donateur_email'] ?? ''), FILTER_VALIDATE_EMAIL);
if (!$email) $errors[] = 'Adresse email invalide';
if (mb_strlen($input['donateur_email'] ?? '') > 150) $errors[] = 'Email trop long';

// Méthode de paiement
$methode = sanitize($input['methode_paiement'] ?? '');
$methodes_valides = ['orange_money', 'moov_money', 'carte_bancaire', 'paypal', 'virement'];
if (!in_array($methode, $methodes_valides, true)) {
    $errors[] = 'Mode de paiement invalide';
}

// Validation spécifique carte bancaire
$card_last4 = '';
$card_type  = '';
if ($methode === 'carte_bancaire') {
    // Recevoir seulement le type et les 4 derniers chiffres (PCI-DSS)
    // Les données brutes de carte NE transitent pas par notre backend
    $card_last4 = preg_replace('/\D/', '', sanitize($input['card_last4'] ?? ''));
    $card_type  = sanitize($input['card_type'] ?? '');
    $types_valides = ['visa', 'mastercard', 'amex', 'discover', 'unknown'];
    if (!in_array($card_type, $types_valides, true)) $card_type = 'unknown';
    if (mb_strlen($card_last4) !== 4) $errors[] = 'Données de carte invalides';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' | ', $errors)]);
    exit;
}

// ─── Autres champs ───────────────────────────────────────────────────────────
$telephone  = sanitize($input['donateur_telephone'] ?? '');
$pays       = sanitize($input['donateur_pays'] ?? 'Niger');
$message    = sanitize($input['message'] ?? '');
$anonyme    = isset($input['anonyme']) && (int)$input['anonyme'] === 1 ? 1 : 0;
$projet_id  = !empty($input['projet_id']) ? (int)$input['projet_id'] : null;
$om_phone   = sanitize($input['om_phone'] ?? '');
$mm_phone   = sanitize($input['mm_phone'] ?? '');

// Limiter les champs
if (mb_strlen($message) > 500) $message = mb_substr($message, 0, 500);
if (mb_strlen($telephone) > 20) $telephone = mb_substr($telephone, 0, 20);

// ─── Générer une référence unique ────────────────────────────────────────────
$reference = 'FAJ-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

// ─── Enregistrement en base ──────────────────────────────────────────────────
try {
    $pdo = getDB();

    // Vérifier que le projet existe (si fourni)
    if ($projet_id !== null) {
        $pcheck = $pdo->prepare("SELECT id FROM projets WHERE id=? AND statut='actif'");
        $pcheck->execute([$projet_id]);
        if (!$pcheck->fetch()) $projet_id = null;
    }

    $stmt = $pdo->prepare("
        INSERT INTO dons (
            reference, donateur_nom, donateur_email, donateur_telephone,
            donateur_pays, montant, devise, methode_paiement, statut,
            projet_id, message, anonyme, ip_address, transaction_id
        ) VALUES (?, ?, ?, ?, ?, ?, 'XOF', ?, 'en_attente', ?, ?, ?, ?, ?)
    ");

    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $card_info = $card_last4 ? $card_type . ':****' . $card_last4 : '';

    $stmt->execute([
        $reference, $nom, $email, $telephone,
        $pays, $montant, $methode, $projet_id,
        $message, $anonyme, $ip, $card_info,
    ]);

    $don_id = (int)$pdo->lastInsertId();

    // ─── Initialiser le paiement via l'abstraction PaymentGatewayInterface ────
    // La passerelle est choisie selon la méthode (Mobile Money → CinetPay,
    // carte → Stripe). Si aucune n'est configurée, la fabrique retombe
    // automatiquement sur le mode démo (DisabledGateway).
    $phone   = ($methode === 'orange_money') ? $om_phone : (($methode === 'moov_money') ? $mm_phone : $telephone);
    $channel = match ($methode) {
        'orange_money' => 'ORANGE_MONEY',
        'moov_money'   => 'MOOV_MONEY',
        default        => 'ALL',
    };

    $gateway = PaymentFactory::forMethod($methode);
    $result  = $gateway->initiatePayment([
        'reference'   => $reference,
        'amount'      => $montant,
        'currency'    => defined('PAYMENT_CURRENCY') ? PAYMENT_CURRENCY : 'XOF',
        'description' => 'Don FAJ Niger - ' . $reference,
        'channel'     => $channel,
        'customer'    => [
            'name'    => $nom,
            'email'   => $email,
            'phone'   => $phone,
            'country' => $pays,
        ],
        'metadata'    => ['don_id' => $don_id, 'projet_id' => $projet_id],
    ]);

    // Tracer la passerelle et le statut normalisé pour l'audit.
    $pdo->prepare("UPDATE dons SET statut=?, updated_at=? WHERE id=?")
        ->execute([$result->status, date('Y-m-d H:i:s'), $don_id]);

    if ($result->redirectUrl) {
        echo json_encode([
            'success'      => true,
            'reference'    => $reference,
            'montant'      => $montant,
            'redirect_url' => $result->redirectUrl,
        ]);
    } elseif ($result->success) {
        // Passerelle non configurée : mode démo (aucun débit réel).
        echo json_encode([
            'success'   => true,
            'reference' => $reference,
            'montant'   => $montant,
            'message'   => $result->message,
            'demo'      => $result->status === 'demo',
        ]);
    } else {
        echo json_encode([
            'success'   => false,
            'reference' => $reference,
            'message'   => $result->message,
        ]);
    }

} catch (Exception $e) {
    error_log('[FAJ Don] Erreur : ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => DEBUG_MODE
            ? $e->getMessage()
            : 'Erreur lors du traitement. Veuillez réessayer.',
    ]);
}
