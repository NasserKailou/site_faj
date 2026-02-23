<?php
/**
 * Actions admin via AJAX - FAJ Niger
 */
require_once '../includes/config.php';
requireAdmin();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = sanitize($input['action'] ?? '');

try {
    $pdo = getDB();
    
    switch ($action) {
        case 'confirm_don':
            $id = intval($input['id'] ?? 0);
            $pdo->prepare("UPDATE dons SET statut='complete' WHERE id=?")->execute([$id]);
            
            // Mettre à jour montant collecté du projet
            $don = $pdo->prepare("SELECT * FROM dons WHERE id=?");
            $don->execute([$id]);
            $donData = $don->fetch();
            if ($donData && $donData['projet_id']) {
                $pdo->prepare("UPDATE projets SET montant_collecte = (SELECT COALESCE(SUM(montant),0) FROM dons WHERE projet_id=? AND statut='complete') WHERE id=?")
                    ->execute([$donData['projet_id'], $donData['projet_id']]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Don confirmé']);
            break;
            
        case 'toggle_statut_projet':
            $id = intval($input['id'] ?? 0);
            $statut = sanitize($input['statut'] ?? 'actif');
            $pdo->prepare("UPDATE projets SET statut=? WHERE id=?")->execute([$statut, $id]);
            echo json_encode(['success' => true]);
            break;
            
        case 'delete_contact':
            $id = intval($input['id'] ?? 0);
            $pdo->prepare("DELETE FROM contacts WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true]);
            break;
            
        case 'mark_contact_read':
            $id = intval($input['id'] ?? 0);
            $pdo->prepare("UPDATE contacts SET lu=1 WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Action inconnue']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
