<?php
/**
 * Webhook Stripe — notification serveur-à-serveur.
 * ---------------------------------------------------------------------------
 * Vérifie la signature Stripe (STRIPE_WEBHOOK_SECRET) puis met à jour le don
 * de façon idempotente. Le corps BRUT est requis pour la vérification HMAC.
 *
 * @package FAJ\Api
 */

require_once '../includes/config.php';
require_once '../includes/payment/bootstrap.php';

use FAJ\Payment\PaymentFactory;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$rawBody = file_get_contents('php://input') ?: '';
$payload = json_decode($rawBody, true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Payload invalide']);
    exit;
}

$headers = [];
foreach ($_SERVER as $k => $v) {
    if (strncmp($k, 'HTTP_', 5) === 0) {
        $name = strtolower(str_replace('_', '-', substr($k, 5)));
        $headers[$name] = $v;
    }
}

try {
    $gateway = PaymentFactory::make('stripe');
    $result  = $gateway->handleCallback($payload, $headers, $rawBody);

    if (!$result->success || $result->reference === '') {
        // Événement ignoré ou non concluant : répondre 200 pour éviter les retries inutiles.
        echo json_encode(['success' => true, 'ignored' => true]);
        exit;
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, statut FROM dons WHERE reference = ?");
    $stmt->execute([$result->reference]);
    $don = $stmt->fetch();

    if (!$don) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Don introuvable']);
        exit;
    }

    if ($don['statut'] === 'complete') {
        echo json_encode(['success' => true, 'message' => 'Déjà traité (idempotent)']);
        exit;
    }

    $pdo->prepare("UPDATE dons SET statut = ?, transaction_id = COALESCE(?, transaction_id), updated_at = ? WHERE reference = ?")
        ->execute([$result->status, $result->transactionId, date('Y-m-d H:i:s'), $result->reference]);

    echo json_encode(['success' => true, 'status' => $result->status]);

} catch (Throwable $e) {
    error_log('[FAJ Webhook Stripe] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur interne']);
}
