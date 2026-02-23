<?php
/**
 * API de traitement des dons - FAJ Niger
 */
require_once '../includes/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Lire les données
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

// Validation
$errors = [];

$montant = isset($input['montant']) ? intval($input['montant']) : 0;
if ($montant < 500) {
    $errors[] = 'Le montant minimum est de 500 FCFA';
}

$nom = sanitize($input['donateur_nom'] ?? '');
if (empty($nom)) {
    $errors[] = 'Le nom est obligatoire';
}

$email = filter_var($input['donateur_email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email) {
    $errors[] = 'L\'adresse email n\'est pas valide';
}

$methode = sanitize($input['methode_paiement'] ?? '');
$methodes_valides = ['orange_money', 'moov_money', 'carte_visa', 'carte_mastercard', 'virement'];
if (!in_array($methode, $methodes_valides)) {
    $errors[] = 'Veuillez sélectionner un moyen de paiement valide';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit();
}

// Générer une référence unique
$reference = 'FAJ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

try {
    $pdo = getDB();
    
    // Enregistrer le don en attente
    $stmt = $pdo->prepare("
        INSERT INTO dons (reference, donateur_nom, donateur_email, donateur_telephone, 
                          donateur_pays, montant, devise, methode_paiement, statut, 
                          projet_id, message, anonyme, ip_address)
        VALUES (?, ?, ?, ?, ?, ?, 'XOF', ?, 'en_attente', ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $reference,
        $nom,
        $email,
        sanitize($input['donateur_telephone'] ?? ''),
        sanitize($input['donateur_pays'] ?? 'Niger'),
        $montant,
        $methode,
        !empty($input['projet_id']) ? intval($input['projet_id']) : null,
        sanitize($input['message'] ?? ''),
        isset($input['anonyme']) ? 1 : 0,
        $_SERVER['REMOTE_ADDR'] ?? ''
    ]);
    
    $don_id = $pdo->lastInsertId();
    
    // Initialiser le paiement selon la méthode
    $redirect_url = null;
    
    switch ($methode) {
        case 'orange_money':
        case 'moov_money':
            // Intégration CinetPay (supporte Orange Money Niger)
            $redirect_url = initCinetPay($don_id, $reference, $montant, $nom, $email, $methode);
            break;
            
        case 'carte_visa':
        case 'carte_mastercard':
            // Intégration Stripe pour cartes internationales
            $redirect_url = initStripe($don_id, $reference, $montant, $nom, $email);
            break;
    }
    
    if ($redirect_url) {
        // Rediriger vers la passerelle de paiement
        echo json_encode([
            'success' => true,
            'reference' => $reference,
            'montant' => $montant,
            'redirect_url' => $redirect_url
        ]);
    } else {
        // Fallback: simuler succès (à remplacer par vraie intégration)
        echo json_encode([
            'success' => true,
            'reference' => $reference,
            'montant' => $montant,
            'message' => 'Don enregistré avec succès. Vous recevrez une confirmation par email.'
        ]);
    }
    
} catch (Exception $e) {
    if (DEBUG_MODE) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors du traitement. Veuillez réessayer.']);
    }
}

/**
 * Initialisation CinetPay (Orange Money, Moov Money)
 */
function initCinetPay($don_id, $reference, $montant, $nom, $email, $methode) {
    if (CINETPAY_APIKEY === 'VOTRE_APIKEY_CINETPAY') {
        return null; // Pas encore configuré
    }
    
    $data = [
        'apikey' => CINETPAY_APIKEY,
        'site_id' => CINETPAY_SITE_ID,
        'transaction_id' => $reference,
        'amount' => $montant,
        'currency' => 'XOF',
        'description' => 'Don FAJ Niger - ' . $reference,
        'notify_url' => SITE_URL . '/api/webhook-cinetpay.php',
        'return_url' => SITE_URL . '/pages/don-succes.php?ref=' . $reference,
        'cancel_url' => SITE_URL . '/pages/don-annule.php?ref=' . $reference,
        'customer_name' => $nom,
        'customer_email' => $email,
        'channels' => ($methode === 'orange_money') ? 'ORANGE_MONEY' : 'MOOV_MONEY',
        'metadata' => json_encode(['don_id' => $don_id])
    ];
    
    $ch = curl_init(CINETPAY_BASE_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (isset($result['data']['payment_url'])) {
        return $result['data']['payment_url'];
    }
    
    return null;
}

/**
 * Initialisation Stripe (Visa/Mastercard)
 */
function initStripe($don_id, $reference, $montant, $nom, $email) {
    if (STRIPE_SECRET_KEY === 'sk_test_VOTRE_CLE_SECRETE_STRIPE') {
        return null; // Pas encore configuré
    }
    
    // Créer une session Stripe Checkout
    $data = http_build_query([
        'line_items[0][price_data][currency]' => 'xof',
        'line_items[0][price_data][product_data][name]' => 'Don FAJ Niger',
        'line_items[0][price_data][product_data][description]' => 'Fonds d\'Appui à la Justice - Réf: ' . $reference,
        'line_items[0][price_data][unit_amount]' => $montant * 100, // En centimes
        'line_items[0][quantity]' => 1,
        'mode' => 'payment',
        'success_url' => SITE_URL . '/pages/don-succes.php?ref=' . $reference,
        'cancel_url' => SITE_URL . '/pages/don-annule.php?ref=' . $reference,
        'customer_email' => $email,
        'metadata[reference]' => $reference,
        'metadata[don_id]' => $don_id,
    ]);
    
    $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY . ':');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (isset($result['url'])) {
        return $result['url'];
    }
    
    return null;
}
?>
