<?php
/**
 * API Newsletter - FAJ Niger
 */
require_once '../includes/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit();
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT IGNORE INTO newsletter (email, nom) VALUES (?, ?)");
    $stmt->execute([$email, sanitize($input['nom'] ?? '')]);
    
    echo json_encode(['success' => true, 'message' => 'Inscription réussie !']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription']);
}
?>
