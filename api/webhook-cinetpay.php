<?php
/**
 * Webhook CinetPay — notification serveur-à-serveur (IPN).
 * ---------------------------------------------------------------------------
 * Reçoit les notifications de paiement de CinetPay, vérifie la signature,
 * re-vérifie le statut côté serveur (source de vérité), puis met à jour le
 * don de façon idempotente. Ne fait jamais confiance au seul payload.
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
    $payload = $_POST; // CinetPay peut poster en x-www-form-urlencoded
}

// Normaliser les en-têtes (clés en minuscules).
$headers = [];
foreach ($_SERVER as $k => $v) {
    if (strncmp($k, 'HTTP_', 5) === 0) {
        $name = strtolower(str_replace('_', '-', substr($k, 5)));
        $headers[$name] = $v;
    }
}

try {
    $gateway = PaymentFactory::make('cinetpay');
    $result  = $gateway->handleCallback($payload, $headers, $rawBody);

    if ($result->reference === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Référence absente']);
        exit;
    }

    $pdo = getDB();

    // Idempotence : ne pas re-traiter un don déjà complété.
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
    error_log('[FAJ Webhook CinetPay] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur interne']);
}
