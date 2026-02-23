<?php
/**
 * API de traitement des dons - FAJ Niger
 * Sécurité : CSRF, rate limiting, validation complète, PCI-DSS
 */
require_once '../includes/config.php';

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

    // ─── Initialiser le paiement selon la méthode ────────────────────────────
    $redirect_url = null;

    switch ($methode) {
        case 'orange_money':
        case 'moov_money':
            $phone        = ($methode === 'orange_money') ? $om_phone : $mm_phone;
            $redirect_url = initCinetPay($don_id, $reference, $montant, $nom, $email, $methode, $phone);
            break;

        case 'carte_bancaire':
            // En production : Stripe Checkout redirect
            // Les données de carte sont gérées côté Stripe.js (jamais sur notre serveur)
            $redirect_url = initStripeCheckout($don_id, $reference, $montant, $nom, $email);
            break;

        case 'paypal':
            $redirect_url = initPayPal($don_id, $reference, $montant, $nom, $email);
            break;
    }

    if ($redirect_url) {
        echo json_encode([
            'success'      => true,
            'reference'    => $reference,
            'montant'      => $montant,
            'redirect_url' => $redirect_url,
        ]);
    } else {
        // Mode démo / passerelle non configurée : simuler succès
        // En production, toujours rediriger vers la passerelle
        $pdo->prepare("UPDATE dons SET statut='demo', updated_at=? WHERE id=?")
            ->execute([date('Y-m-d H:i:s'), $don_id]);

        echo json_encode([
            'success'   => true,
            'reference' => $reference,
            'montant'   => $montant,
            'message'   => 'Don enregistré (mode démo). Configurez les clés API pour activer les paiements réels.',
            'demo'      => true,
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

// ════════════════════════════════════════════════════════════════════════════
//  FONCTIONS D'INTÉGRATION PASSERELLES
// ════════════════════════════════════════════════════════════════════════════

/**
 * CinetPay – Orange Money / Moov Money Niger
 */
function initCinetPay(int $don_id, string $ref, int $montant, string $nom, string $email, string $methode, string $phone): ?string {
    if (CINETPAY_APIKEY === 'VOTRE_APIKEY_CINETPAY') return null;

    $channels = ($methode === 'orange_money') ? 'ORANGE_MONEY' : 'MOOV_MONEY';

    $data = [
        'apikey'         => CINETPAY_APIKEY,
        'site_id'        => CINETPAY_SITE_ID,
        'transaction_id' => $ref,
        'amount'         => $montant,
        'currency'       => 'XOF',
        'description'    => 'Don FAJ Niger – ' . $ref,
        'notify_url'     => SITE_URL . '/api/webhook-cinetpay',
        'return_url'     => SITE_URL . '/don-succes?ref=' . $ref,
        'cancel_url'     => SITE_URL . '/don?annule=1',
        'customer_name'  => $nom,
        'customer_email' => $email,
        'customer_phone_number' => $phone,
        'channels'       => $channels,
        'metadata'       => json_encode(['don_id' => $don_id]),
    ];

    $ch = curl_init(CINETPAY_BASE_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['data']['payment_url'] ?? null;
}

/**
 * Stripe – Carte Visa / Mastercard
 * Note : Stripe Checkout redirige vers une page Stripe hébergée.
 * Les données de carte sont saisies directement chez Stripe (PCI-DSS Level 1).
 */
function initStripeCheckout(int $don_id, string $ref, int $montant, string $nom, string $email): ?string {
    if (STRIPE_SECRET_KEY === 'sk_test_VOTRE_CLE_SECRETE_STRIPE') return null;

    $data = http_build_query([
        'line_items[0][price_data][currency]'                        => 'xof',
        'line_items[0][price_data][product_data][name]'              => 'Don FAJ Niger',
        'line_items[0][price_data][product_data][description]'       => 'Fonds d\'Appui à la Justice – Réf : ' . $ref,
        'line_items[0][price_data][unit_amount]'                     => $montant,
        'line_items[0][quantity]'                                    => 1,
        'mode'                                                       => 'payment',
        'success_url'                                                => SITE_URL . '/don-succes?ref=' . $ref,
        'cancel_url'                                                 => SITE_URL . '/don?annule=1',
        'customer_email'                                             => $email,
        'metadata[reference]'                                        => $ref,
        'metadata[don_id]'                                           => $don_id,
        'payment_method_types[0]'                                    => 'card',
        'billing_address_collection'                                 => 'required',
    ]);

    $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => STRIPE_SECRET_KEY . ':',
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['url'] ?? null;
}

/**
 * PayPal – Paiement international
 */
function initPayPal(int $don_id, string $ref, int $montant, string $nom, string $email): ?string {
    // Conversion XOF → EUR (approximatif : 1 EUR ≈ 656 XOF)
    $montant_eur = round($montant / 656, 2);
    if ($montant_eur < 0.01) return null;

    // Utiliser l'API REST PayPal en production
    // Pour l'instant, retourner null (mode démo)
    return null;
}
?>
