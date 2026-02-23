<?php
/**
 * API Contact - FAJ Niger
 */
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

// Validation
$nom = sanitize($input['nom'] ?? '');
$email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
$sujet = sanitize($input['sujet'] ?? '');
$message = sanitize($input['message'] ?? '');

if (empty($nom) || !$email || empty($sujet) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis']);
    exit();
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO contacts (nom, email, telephone, sujet, message, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $email, sanitize($input['telephone'] ?? ''), $sujet, $message, $_SERVER['REMOTE_ADDR'] ?? '']);
    
    echo json_encode(['success' => true, 'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les 24h.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi. Veuillez réessayer.']);
}
?>
