<?php
$page_title = 'Faire un Don';
require_once '../includes/config.php';

// Récupérer les projets
try {
    $pdo = getDB();
    $projets = $pdo->query("SELECT id, titre, categorie FROM projets WHERE statut='actif' ORDER BY priorite")->fetchAll();
} catch (Exception $e) {
    $projets = [];
}

$projet_preselect = isset($_GET['projet']) ? intval($_GET['projet']) : null;

require_once '../includes/header.php';
?>

<!-- Page Hero -->
<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Faire un Don</span>
        </div>
        <h1>Faites un Don au <span style="color:var(--secondary)">FAJ Niger</span></h1>
        <p>Chaque contribution aide à construire un système judiciaire plus juste et accessible pour tous les Nigériens</p>
    </div>
</div>

<section style="padding: 80px 0; background: var(--light);">
    <div class="container">
        <div style="display:grid; grid-template-columns: 1fr 380px; gap:50px; align-items:start;">
            
            <!-- FORMULAIRE DON -->
            <div>
                <div class="don-form-container" style="background:white; border-radius:var(--radius-lg); padding:50px; box-shadow:var(--shadow);">
                    <h2 style="font-family:var(--font-display); font-size:26px; color:var(--primary); margin-bottom:8px;">
                        <i class="fas fa-heart" style="color:var(--secondary);"></i> Votre Don
                    </h2>
                    <p style="color:var(--gray); font-size:14px; margin-bottom:30px;">
                        Complétez le formulaire ci-dessous pour effectuer votre don sécurisé.
                    </p>
                    
                    <form id="donForm" method="POST">
                        <input type="hidden" name="methode_paiement" id="methode_paiement" value="">
                        
                        <!-- Projet (optionnel) -->
                        <div class="form-group">
                            <label for="projet_id">Projet à soutenir (optionnel)</label>
                            <select id="projet_id" name="projet_id" class="form-control">
                                <option value="">Don général au FAJ</option>
                                <?php foreach ($projets as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $projet_preselect == $p['id'] ? 'selected' : '' ?>>
                                    <?= sanitize($p['titre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Montants prédéfinis -->
                        <div class="form-group">
                            <label>Montant du don <span class="required">*</span></label>
                            <div class="amount-presets" style="grid-template-columns: repeat(4,1fr);">
                                <button type="button" class="amount-btn" data-amount="1000">1 000<br><small>FCFA</small></button>
                                <button type="button" class="amount-btn" data-amount="5000">5 000<br><small>FCFA</small></button>
                                <button type="button" class="amount-btn selected" data-amount="10000">10 000<br><small>FCFA</small></button>
                                <button type="button" class="amount-btn" data-amount="25000">25 000<br><small>FCFA</small></button>
                                <button type="button" class="amount-btn" data-amount="50000">50 000<br><small>FCFA</small></button>
                                <button type="button" class="amount-btn" data-amount="100000">100 000<br><small>FCFA</small></button>
                                <button type="button" class="amount-btn" data-amount="500000">500 000<br><small>FCFA</small></button>
                                <button type="button" class="amount-btn" data-amount="0">Autre<br><small>montant</small></button>
                            </div>
                            <div style="margin-top:12px;">
                                <input type="number" id="montant" name="montant" class="form-control" 
                                       placeholder="Ou saisissez un montant personnalisé (min. 500 FCFA)"
                                       value="10000" min="500" required data-amount-format>
                            </div>
                        </div>
                        
                        <!-- Infos donateur -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="donateur_nom">Nom complet <span class="required">*</span></label>
                                <input type="text" id="donateur_nom" name="donateur_nom" class="form-control" 
                                       placeholder="Votre nom complet" required>
                            </div>
                            <div class="form-group">
                                <label for="donateur_email">Email <span class="required">*</span></label>
                                <input type="email" id="donateur_email" name="donateur_email" class="form-control" 
                                       placeholder="votre@email.com" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="donateur_telephone">Téléphone</label>
                                <input type="tel" id="donateur_telephone" name="donateur_telephone" class="form-control" 
                                       placeholder="+227 XX XX XX XX">
                            </div>
                            <div class="form-group">
                                <label for="donateur_pays">Pays de résidence</label>
                                <select id="donateur_pays" name="donateur_pays" class="form-control">
                                    <option value="Niger" selected>🇳🇪 Niger</option>
                                    <option value="Sénégal">🇸🇳 Sénégal</option>
                                    <option value="Mali">🇲🇱 Mali</option>
                                    <option value="Burkina Faso">🇧🇫 Burkina Faso</option>
                                    <option value="Côte d'Ivoire">🇨🇮 Côte d'Ivoire</option>
                                    <option value="Nigeria">🇳🇬 Nigeria</option>
                                    <option value="France">🇫🇷 France</option>
                                    <option value="Belgique">🇧🇪 Belgique</option>
                                    <option value="Canada">🇨🇦 Canada</option>
                                    <option value="USA">🇺🇸 USA</option>
                                    <option value="Autre">🌍 Autre</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message de soutien (optionnel)</label>
                            <textarea id="message" name="message" class="form-control" rows="3" 
                                      placeholder="Partagez votre message de soutien à la justice au Niger..."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="anonyme" id="anonyme"> 
                                Je souhaite faire ce don anonymement
                            </label>
                        </div>
                        
                        <!-- Moyens de paiement -->
                        <div class="form-group">
                            <label style="font-size:16px; font-weight:700; color:var(--primary);">
                                Mode de paiement <span class="required">*</span>
                            </label>
                            
                            <div class="payment-methods-grid">
                                <button type="button" class="payment-method-btn orange" data-method="orange_money">
                                    <i class="fas fa-mobile-alt" style="color:#FF6600;font-size:22px;"></i>
                                    <div>
                                        <strong>Orange Money</strong><br>
                                        <small style="color:var(--gray);">Paiement mobile Niger</small>
                                    </div>
                                </button>
                                <button type="button" class="payment-method-btn moov" data-method="moov_money">
                                    <i class="fas fa-mobile-alt" style="color:#0066CC;font-size:22px;"></i>
                                    <div>
                                        <strong>Moov Money</strong><br>
                                        <small style="color:var(--gray);">Paiement mobile Niger</small>
                                    </div>
                                </button>
                                <button type="button" class="payment-method-btn visa" data-method="carte_visa">
                                    <i class="fab fa-cc-visa" style="color:#1A1F71;font-size:28px;"></i>
                                    <div>
                                        <strong>Carte Visa</strong><br>
                                        <small style="color:var(--gray);">International · SSL</small>
                                    </div>
                                </button>
                                <button type="button" class="payment-method-btn mastercard" data-method="carte_mastercard">
                                    <i class="fab fa-cc-mastercard" style="color:#EB001B;font-size:28px;"></i>
                                    <div>
                                        <strong>Mastercard</strong><br>
                                        <small style="color:var(--gray);">International · SSL</small>
                                    </div>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Panneaux détail paiement -->
                        <div id="panel-orange_money" class="payment-detail-panel" style="display:none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <span>Vous serez redirigé vers <strong>CinetPay</strong> pour payer via Orange Money Niger de façon sécurisée.</span>
                            </div>
                        </div>
                        <div id="panel-moov_money" class="payment-detail-panel" style="display:none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <span>Vous serez redirigé vers <strong>CinetPay</strong> pour payer via Moov Money Niger de façon sécurisée.</span>
                            </div>
                        </div>
                        <div id="panel-carte_visa" class="payment-detail-panel" style="display:none;">
                            <div class="alert alert-info">
                                <i class="fas fa-shield-alt"></i>
                                <span>Paiement sécurisé par <strong>Stripe</strong>. Vos données bancaires sont protégées par cryptage SSL 256 bits.</span>
                            </div>
                        </div>
                        <div id="panel-carte_mastercard" class="payment-detail-panel" style="display:none;">
                            <div class="alert alert-info">
                                <i class="fas fa-shield-alt"></i>
                                <span>Paiement sécurisé par <strong>Stripe</strong>. Vos données bancaires sont protégées par cryptage SSL 256 bits.</span>
                            </div>
                        </div>
                        
                        <!-- Résumé -->
                        <div class="don-summary" style="margin:24px 0;">
                            <div class="summary-row">
                                <span><i class="fas fa-hand-holding-heart" style="color:var(--secondary); margin-right:8px;"></i> Montant :</span>
                                <strong id="summaryMontant" style="color:var(--secondary); font-size:18px;">10 000 FCFA</strong>
                            </div>
                            <div class="summary-row">
                                <span><i class="fas fa-credit-card" style="color:var(--secondary); margin-right:8px;"></i> Mode de paiement :</span>
                                <span id="summaryMethode" style="color:var(--gray);">Non sélectionné</span>
                            </div>
                            <div class="summary-row" id="summaryProjetRow" style="display:none;">
                                <span><i class="fas fa-project-diagram" style="color:var(--secondary); margin-right:8px;"></i> Projet :</span>
                                <span id="summaryProjet" style="color:var(--gray);">—</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" required>
                                J'accepte les <a href="<?= SITE_URL ?>/pages/conditions-generales.php" style="color:var(--secondary);">conditions générales</a> et la <a href="<?= SITE_URL ?>/pages/politique-confidentialite.php" style="color:var(--secondary);">politique de confidentialité</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; font-size:18px; padding:20px; border-radius:var(--radius);">
                            <i class="fas fa-lock"></i> Valider Mon Don
                        </button>
                        
                        <p style="text-align:center; font-size:12px; color:var(--gray); margin-top:16px;">
                            <i class="fas fa-shield-alt" style="color:var(--secondary);"></i>
                            Paiement 100% sécurisé · Reçu officiel envoyé par email · Données protégées
                        </p>
                    </form>
                </div>
            </div>
            
            <!-- SIDEBAR -->
            <div>
                <!-- Impact Card -->
                <div style="background:var(--primary); color:white; border-radius:var(--radius-lg); padding:35px; margin-bottom:24px;">
                    <h3 style="font-size:18px; font-weight:700; margin-bottom:20px; color:var(--secondary-light);">
                        <i class="fas fa-chart-line"></i> Impact de votre Don
                    </h3>
                    <?php
                    $impacts = [
                        ['montant'=>500, 'impact'=>'Finance 1 heure d\'aide juridictionnelle'],
                        ['montant'=>5000, 'impact'=>'Couvre 1 journée de formation judiciaire'],
                        ['montant'=>25000, 'impact'=>'Équipe partiellement un greffe de tribunal'],
                        ['montant'=>100000, 'impact'=>'Finance une mission de sensibilisation dans 1 région'],
                        ['montant'=>500000, 'impact'=>'Contribue à la construction d\'une salle d\'audience'],
                    ];
                    foreach ($impacts as $i):
                    ?>
                    <div style="display:flex; gap:12px; margin-bottom:16px; align-items:flex-start;">
                        <div style="width:10px; height:10px; border-radius:50%; background:var(--secondary); flex-shrink:0; margin-top:5px;"></div>
                        <div>
                            <strong style="color:var(--secondary-light);"><?= number_format($i['montant'],0,',',' ') ?> FCFA</strong>
                            <span style="color:rgba(255,255,255,0.7); font-size:13px; display:block;"><?= $i['impact'] ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Sécurité -->
                <div style="background:white; border-radius:var(--radius-lg); padding:28px; box-shadow:var(--shadow); margin-bottom:24px;">
                    <h4 style="font-size:15px; font-weight:700; color:var(--primary); margin-bottom:16px;">
                        <i class="fas fa-lock" style="color:var(--secondary);"></i> Paiements Sécurisés
                    </h4>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div style="text-align:center; padding:12px; background:var(--light); border-radius:8px;">
                            <i class="fas fa-mobile-alt" style="font-size:24px; color:#FF6600; display:block; margin-bottom:6px;"></i>
                            <span style="font-size:11px; font-weight:600; color:var(--primary);">Orange Money</span>
                        </div>
                        <div style="text-align:center; padding:12px; background:var(--light); border-radius:8px;">
                            <i class="fas fa-mobile-alt" style="font-size:24px; color:#0066CC; display:block; margin-bottom:6px;"></i>
                            <span style="font-size:11px; font-weight:600; color:var(--primary);">Moov Money</span>
                        </div>
                        <div style="text-align:center; padding:12px; background:var(--light); border-radius:8px;">
                            <i class="fab fa-cc-visa" style="font-size:28px; color:#1A1F71; display:block; margin-bottom:6px;"></i>
                            <span style="font-size:11px; font-weight:600; color:var(--primary);">Visa</span>
                        </div>
                        <div style="text-align:center; padding:12px; background:var(--light); border-radius:8px;">
                            <i class="fab fa-cc-mastercard" style="font-size:28px; color:#EB001B; display:block; margin-bottom:6px;"></i>
                            <span style="font-size:11px; font-weight:600; color:var(--primary);">Mastercard</span>
                        </div>
                    </div>
                    <p style="font-size:12px; color:var(--gray); margin-top:16px; text-align:center;">
                        <i class="fas fa-shield-alt" style="color:var(--success);"></i>
                        Cryptage SSL 256 bits · PCI-DSS Compliant
                    </p>
                </div>
                
                <!-- Contact -->
                <div style="background:rgba(232,135,10,0.08); border:1px solid rgba(232,135,10,0.3); border-radius:var(--radius-lg); padding:25px;">
                    <h4 style="font-size:14px; font-weight:700; color:var(--primary); margin-bottom:12px;">
                        <i class="fas fa-question-circle" style="color:var(--secondary);"></i> Besoin d'aide ?
                    </h4>
                    <p style="font-size:13px; color:var(--gray); margin-bottom:12px;">
                        Notre équipe est disponible pour vous assister dans votre démarche de don.
                    </p>
                    <a href="<?= SITE_URL ?>/pages/contact.php" class="btn btn-outline-primary btn-sm" style="width:100%; justify-content:center;">
                        <i class="fas fa-envelope"></i> Nous Contacter
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal succès don -->
<div class="modal-overlay" id="donSuccessModal">
    <div class="modal">
        <div class="modal-icon"><i class="fas fa-check-circle"></i></div>
        <h3>🎉 Don Confirmé !</h3>
        <p>Merci pour votre généreux don au FAJ Niger.<br>
           <strong>Référence : <span id="successRef"></span></strong><br>
           <strong>Montant : <span id="successMontant"></span></strong><br><br>
           Un reçu officiel vous a été envoyé par email.</p>
        <a href="<?= SITE_URL ?>/" class="btn btn-primary" data-modal-close>
            <i class="fas fa-home"></i> Retour à l'Accueil
        </a>
    </div>
</div>

<style>
.payment-methods-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
}
.payment-method-btn {
    padding: 16px;
    gap: 12px;
    text-align: left;
    align-items: center;
}
@media (max-width: 1024px) {
    section > .container > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
// Projet select
document.getElementById('projet_id')?.addEventListener('change', function() {
    const row = document.getElementById('summaryProjetRow');
    const span = document.getElementById('summaryProjet');
    if (this.value) {
        row.style.display = 'flex';
        span.textContent = this.options[this.selectedIndex].text;
    } else {
        row.style.display = 'none';
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
