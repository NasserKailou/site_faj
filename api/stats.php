<?php
/**
 * API Stats dynamiques - FAJ Niger
 * Retourne le nombre de donateurs et le total collecté en temps réel
 */
require_once '../includes/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// Rate limiting
if (!rateLimit('api_stats', 60, 60)) {
    http_response_code(429);
    echo json_encode(['error' => 'Trop de requêtes']);
    exit;
}

try {
    $pdo = getDB();

    // Nombre de donateurs uniques (dons confirmés)
    $total_donateurs = (int)$pdo->query(
        "SELECT COUNT(DISTINCT donateur_email) FROM dons WHERE statut='complete'"
    )->fetchColumn();

    // Total collecté (dons confirmés uniquement)
    $total_collecte = (float)$pdo->query(
        "SELECT COALESCE(SUM(montant), 0) FROM dons WHERE statut='complete'"
    )->fetchColumn();

    // Nombre de projets actifs
    $total_projets = (int)$pdo->query(
        "SELECT COUNT(*) FROM projets WHERE statut='actif'"
    )->fetchColumn();

    // Bénéficiaires (paramètre configurable)
    $total_beneficiaires = (int)getSiteParam('total_beneficiaires', '0');

    // Dernier don (pour affichage live)
    $dernier_don = $pdo->query(
        "SELECT donateur_nom, montant, anonyme, created_at FROM dons WHERE statut='complete' ORDER BY created_at DESC LIMIT 1"
    )->fetch();

    echo json_encode([
        'success'           => true,
        'total_donateurs'   => $total_donateurs,
        'total_collecte'    => $total_collecte,
        'total_collecte_fmt'=> formatMontant($total_collecte),
        'total_projets'     => $total_projets,
        'total_beneficiaires'=> $total_beneficiaires,
        'dernier_don'       => $dernier_don ? [
            'nom'    => $dernier_don['anonyme'] ? 'Donateur anonyme' : sanitize($dernier_don['donateur_nom']),
            'montant'=> formatMontant((float)$dernier_don['montant']),
            'date'   => timeAgo($dernier_don['created_at']),
        ] : null,
        'timestamp' => time(),
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erreur serveur']);
}
?>
