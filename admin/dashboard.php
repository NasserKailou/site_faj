<?php
require_once '../includes/config.php';
requireAdmin();
$page_title = 'Tableau de Bord';

// Stats du dashboard
try {
    $pdo = getDB();
    $total_dons = $pdo->query("SELECT COUNT(*) FROM dons WHERE statut='complete'")->fetchColumn() ?: 0;
    $total_montant = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE statut='complete'")->fetchColumn() ?: 0;
    $total_donateurs = $pdo->query("SELECT COUNT(DISTINCT donateur_email) FROM dons WHERE statut='complete'")->fetchColumn() ?: 0;
    $total_projets = $pdo->query("SELECT COUNT(*) FROM projets WHERE statut='actif'")->fetchColumn() ?: 0;
    $dons_attente = $pdo->query("SELECT COUNT(*) FROM dons WHERE statut='en_attente'")->fetchColumn() ?: 0;
    $messages_non_lus = $pdo->query("SELECT COUNT(*) FROM contacts WHERE lu=0")->fetchColumn() ?: 0;
    
    // Derniers dons
    $derniers_dons = $pdo->query("SELECT * FROM dons ORDER BY created_at DESC LIMIT 8")->fetchAll();
    
    // Dons par mois (12 derniers mois)
    $dons_mensuel = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as mois, 
               SUM(montant) as total,
               COUNT(*) as nb
        FROM dons 
        WHERE statut='complete' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY mois 
        ORDER BY mois ASC
    ")->fetchAll();
    
    // Dons par méthode
    $dons_methode = $pdo->query("
        SELECT methode_paiement, COUNT(*) as nb, SUM(montant) as total
        FROM dons WHERE statut='complete'
        GROUP BY methode_paiement
    ")->fetchAll();
    
} catch (Exception $e) {
    $total_dons = $total_montant = $total_donateurs = $total_projets = 0;
    $dons_attente = $messages_non_lus = 0;
    $derniers_dons = $dons_mensuel = $dons_methode = [];
}

include '../admin/includes/layout-header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-tachometer-alt"></i> Tableau de Bord</h1>
        <p class="page-subtitle">Vue d'ensemble de l'activité du FAJ Niger</p>
    </div>
    <div style="display:flex; gap:12px;">
        <a href="<?= SITE_URL ?>/pages/don.php" class="btn btn-outline" target="_blank">
            <i class="fas fa-external-link-alt"></i> Voir le site
        </a>
        <a href="<?= SITE_URL ?>/admin/dons/liste.php" class="btn btn-primary">
            <i class="fas fa-hand-holding-heart"></i> Voir les dons
        </a>
    </div>
</div>

<!-- Cartes stats -->
<div class="stats-cards">
    <div class="stat-card-admin primary">
        <div class="stat-card-icon"><i class="fas fa-hand-holding-heart"></i></div>
        <div class="stat-card-body">
            <span class="stat-card-number"><?= number_format($total_montant, 0, ',', ' ') ?></span>
            <span class="stat-card-unit">FCFA</span>
            <span class="stat-card-label">Total Collecté</span>
        </div>
    </div>
    <div class="stat-card-admin orange">
        <div class="stat-card-icon"><i class="fas fa-users"></i></div>
        <div class="stat-card-body">
            <span class="stat-card-number"><?= $total_donateurs ?></span>
            <span class="stat-card-label">Donateurs uniques</span>
        </div>
    </div>
    <div class="stat-card-admin green">
        <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
        <div class="stat-card-body">
            <span class="stat-card-number"><?= $total_dons ?></span>
            <span class="stat-card-label">Dons réussis</span>
        </div>
    </div>
    <div class="stat-card-admin blue">
        <div class="stat-card-icon"><i class="fas fa-project-diagram"></i></div>
        <div class="stat-card-body">
            <span class="stat-card-number"><?= $total_projets ?></span>
            <span class="stat-card-label">Projets actifs</span>
        </div>
    </div>
</div>

<!-- Alertes -->
<?php if ($dons_attente > 0 || $messages_non_lus > 0): ?>
<div class="alerts-row">
    <?php if ($dons_attente > 0): ?>
    <div class="alert-card warning">
        <i class="fas fa-clock"></i>
        <span><?= $dons_attente ?> don(s) en attente de confirmation</span>
        <a href="<?= SITE_URL ?>/admin/dons/liste.php?statut=en_attente">Voir</a>
    </div>
    <?php endif; ?>
    <?php if ($messages_non_lus > 0): ?>
    <div class="alert-card info">
        <i class="fas fa-envelope"></i>
        <span><?= $messages_non_lus ?> message(s) non lu(s)</span>
        <a href="<?= SITE_URL ?>/admin/contacts/liste.php">Voir</a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Graphiques et tableau -->
<div class="dashboard-grid">
    
    <!-- Graphique dons -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-line"></i> Évolution des dons (12 mois)</h3>
        </div>
        <div class="card-body">
            <canvas id="donsMensuelChart" height="250"></canvas>
        </div>
    </div>
    
    <!-- Répartition méthodes -->
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-pie"></i> Méthodes de paiement</h3>
        </div>
        <div class="card-body" style="display:flex; align-items:center; justify-content:center;">
            <canvas id="methodesChart" height="250"></canvas>
        </div>
    </div>
</div>

<!-- Derniers dons -->
<div class="card mt-4">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Derniers Dons</h3>
        <a href="<?= SITE_URL ?>/admin/dons/liste.php" class="btn btn-sm btn-outline">Voir tous</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Donateur</th>
                        <th>Montant</th>
                        <th>Méthode</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($derniers_dons)): ?>
                    <tr><td colspan="6" style="text-align:center; color:var(--admin-gray); padding:40px;">Aucun don pour le moment</td></tr>
                    <?php else: ?>
                    <?php foreach ($derniers_dons as $don): ?>
                    <tr>
                        <td><code><?= $don['reference'] ?></code></td>
                        <td>
                            <?php if ($don['anonyme']): ?>
                            <em style="color:var(--admin-gray);">Anonyme</em>
                            <?php else: ?>
                            <strong><?= sanitize($don['donateur_nom']) ?></strong>
                            <small style="display:block; color:var(--admin-gray);"><?= $don['donateur_email'] ?></small>
                            <?php endif; ?>
                        </td>
                        <td><strong style="color:var(--admin-primary);"><?= number_format($don['montant'],0,',',' ') ?> FCFA</strong></td>
                        <td>
                            <?php
                            $methodes_labels = [
                                'orange_money'=>'<span class="badge orange">Orange Money</span>',
                                'moov_money'=>'<span class="badge blue">Moov Money</span>',
                                'carte_visa'=>'<span class="badge navy">Visa</span>',
                                'carte_mastercard'=>'<span class="badge red">Mastercard</span>',
                            ];
                            echo $methodes_labels[$don['methode_paiement']] ?? $don['methode_paiement'];
                            ?>
                        </td>
                        <td>
                            <?php
                            $statuts = [
                                'complete'=>'<span class="status-badge success">Complété</span>',
                                'en_attente'=>'<span class="status-badge warning">En attente</span>',
                                'echoue'=>'<span class="status-badge danger">Échoué</span>',
                                'rembourse'=>'<span class="status-badge info">Remboursé</span>',
                            ];
                            echo $statuts[$don['statut']] ?? $don['statut'];
                            ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($don['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique dons mensuel
const donsMensuelData = <?= json_encode($dons_mensuel) ?>;
const labels = donsMensuelData.map(d => {
    const [y, m] = d.mois.split('-');
    const date = new Date(y, m-1);
    return date.toLocaleDateString('fr-FR', {month:'short', year:'2-digit'});
});
const totaux = donsMensuelData.map(d => d.total || 0);

new Chart(document.getElementById('donsMensuelChart'), {
    type: 'bar',
    data: {
        labels: labels.length ? labels : ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
        datasets: [{
            label: 'Dons (FCFA)',
            data: totaux.length ? totaux : [0, 0, 0, 0, 0, 0],
            backgroundColor: 'rgba(232, 135, 10, 0.8)',
            borderColor: '#E8870A',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Graphique méthodes
const methodesData = <?= json_encode($dons_methode) ?>;
const methLabels = methodesData.map(d => ({
    'orange_money':'Orange Money','moov_money':'Moov Money',
    'carte_visa':'Visa','carte_mastercard':'Mastercard'
}[d.methode_paiement] || d.methode_paiement));
const methTotaux = methodesData.map(d => d.nb || 0);

new Chart(document.getElementById('methodesChart'), {
    type: 'doughnut',
    data: {
        labels: methLabels.length ? methLabels : ['Orange Money', 'Moov Money', 'Visa', 'Mastercard'],
        datasets: [{
            data: methTotaux.length ? methTotaux : [40, 30, 20, 10],
            backgroundColor: ['#FF6600', '#0066CC', '#1A1F71', '#EB001B'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>

<?php include '../admin/includes/layout-footer.php'; ?>
