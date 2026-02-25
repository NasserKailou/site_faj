<?php
require_once '../../includes/config.php';
requireAdmin();

try {
    $pdo = getDB();
    $dons = $pdo->query("SELECT d.*, p.titre as projet_titre FROM dons d LEFT JOIN projets p ON d.projet_id = p.id ORDER BY d.created_at DESC")->fetchAll();
} catch (Exception $e) {
    $dons = [];
}

// Générer le CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="dons_faj_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
// BOM UTF-8 pour Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// En-tête
fputcsv($output, ['Référence','Donateur','Email','Téléphone','Pays','Montant','Devise','Méthode','Projet','Statut','Date'], ';');

foreach ($dons as $don) {
    fputcsv($output, [
        $don['reference'],
        $don['anonyme'] ? 'Anonyme' : $don['donateur_nom'],
        $don['anonyme'] ? '' : $don['donateur_email'],
        $don['donateur_telephone'] ?? '',
        $don['donateur_pays'] ?? '',
        $don['montant'],
        $don['devise'],
        $don['methode_paiement'],
        $don['projet_titre'] ?? 'Don général',
        $don['statut'],
        $don['created_at'],
    ], ';');
}

fclose($output);
exit;
