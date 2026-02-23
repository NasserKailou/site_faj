<?php
/**
 * Page de Don - FAJ Niger
 * Sécurité : CSRF, validation Luhn, tokenisation côté client (Stripe.js)
 */
$page_title = 'Faire un Don';
require_once '../includes/config.php';

// Récupérer les projets
try {
    $pdo    = getDB();
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

<section class="don-section">
    <div class="container">
        <div class="don-layout">

            <!-- ═══════════════════ FORMULAIRE DON ═══════════════════ -->
            <div class="don-main">
                <div class="don-form-container">

                    <!-- En-tête formulaire -->
                    <div class="don-form-header">
                        <div class="don-form-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div>
                            <h2>Votre Don Sécurisé</h2>
                            <p>Formulaire chiffré SSL 256 bits · Données bancaires protégées</p>
                        </div>
                        <div class="security-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>100% Sécurisé</span>
                        </div>
                    </div>

                    <!-- Indicateur d'étapes -->
                    <div class="don-steps">
                        <div class="don-step active" id="step-indicator-1">
                            <div class="step-number">1</div>
                            <span>Montant</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="don-step" id="step-indicator-2">
                            <div class="step-number">2</div>
                            <span>Identité</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="don-step" id="step-indicator-3">
                            <div class="step-number">3</div>
                            <span>Paiement</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="don-step" id="step-indicator-4">
                            <div class="step-number">4</div>
                            <span>Confirmation</span>
                        </div>
                    </div>

                    <form id="donForm" novalidate>
                        <?= csrfField() ?>
                        <input type="hidden" name="methode_paiement" id="methode_paiement" value="">

                        <!-- ─── ÉTAPE 1 : Montant ─────────────────────── -->
                        <div class="don-step-content" id="step-1">
                            <h3 class="step-title"><i class="fas fa-coins"></i> Choisissez votre montant</h3>

                            <!-- Projet -->
                            <div class="form-group">
                                <label for="projet_id">
                                    <i class="fas fa-project-diagram"></i> Projet à soutenir
                                    <span class="label-optional">(optionnel)</span>
                                </label>
                                <select id="projet_id" name="projet_id" class="form-control form-select">
                                    <option value="">Don général au FAJ Niger</option>
                                    <?php foreach ($projets as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $projet_preselect == $p['id'] ? 'selected' : '' ?>>
                                        <?= sanitize($p['titre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Montants prédéfinis -->
                            <div class="form-group">
                                <label><i class="fas fa-hand-holding-usd"></i> Montant du don <span class="required">*</span></label>
                                <div class="amount-presets">
                                    <button type="button" class="amount-btn" data-amount="1000">
                                        <span class="amount-value">1 000</span>
                                        <span class="amount-currency">FCFA</span>
                                    </button>
                                    <button type="button" class="amount-btn" data-amount="5000">
                                        <span class="amount-value">5 000</span>
                                        <span class="amount-currency">FCFA</span>
                                    </button>
                                    <button type="button" class="amount-btn active" data-amount="10000">
                                        <span class="amount-value">10 000</span>
                                        <span class="amount-currency">FCFA</span>
                                        <span class="amount-badge">Populaire</span>
                                    </button>
                                    <button type="button" class="amount-btn" data-amount="25000">
                                        <span class="amount-value">25 000</span>
                                        <span class="amount-currency">FCFA</span>
                                    </button>
                                    <button type="button" class="amount-btn" data-amount="50000">
                                        <span class="amount-value">50 000</span>
                                        <span class="amount-currency">FCFA</span>
                                    </button>
                                    <button type="button" class="amount-btn" data-amount="100000">
                                        <span class="amount-value">100 000</span>
                                        <span class="amount-currency">FCFA</span>
                                    </button>
                                    <button type="button" class="amount-btn" data-amount="500000">
                                        <span class="amount-value">500 000</span>
                                        <span class="amount-currency">FCFA</span>
                                    </button>
                                    <button type="button" class="amount-btn amount-custom" data-amount="custom">
                                        <span class="amount-value"><i class="fas fa-edit"></i></span>
                                        <span class="amount-currency">Autre</span>
                                    </button>
                                </div>
                                <div class="amount-custom-input" id="customAmountWrap" style="display:none;">
                                    <div class="input-with-unit">
                                        <input type="number" id="montant_custom" placeholder="Saisir un montant..."
                                               min="500" step="100" class="form-control">
                                        <span class="input-unit">FCFA</span>
                                    </div>
                                    <p class="input-hint"><i class="fas fa-info-circle"></i> Montant minimum : 500 FCFA</p>
                                </div>
                                <input type="hidden" id="montant" name="montant" value="10000" required>
                            </div>

                            <!-- Barre de progression impact -->
                            <div class="impact-preview" id="impactPreview">
                                <div class="impact-icon"><i class="fas fa-balance-scale"></i></div>
                                <div class="impact-text">
                                    <strong>10 000 FCFA</strong> couvre 1 journée de formation judiciaire
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary btn-step-next btn-block" onclick="nextStep(2)">
                                Continuer <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                        <!-- ─── ÉTAPE 2 : Identité ────────────────────── -->
                        <div class="don-step-content" id="step-2" style="display:none;">
                            <h3 class="step-title"><i class="fas fa-user"></i> Vos informations</h3>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="donateur_nom">Nom complet <span class="required">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" id="donateur_nom" name="donateur_nom"
                                               class="form-control" placeholder="Votre nom complet"
                                               required autocomplete="name" maxlength="100">
                                    </div>
                                    <div class="field-error" id="err-nom"></div>
                                </div>
                                <div class="form-group">
                                    <label for="donateur_email">Adresse email <span class="required">*</span></label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" id="donateur_email" name="donateur_email"
                                               class="form-control" placeholder="votre@email.com"
                                               required autocomplete="email" maxlength="150">
                                    </div>
                                    <div class="field-error" id="err-email"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="donateur_telephone">Téléphone</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="tel" id="donateur_telephone" name="donateur_telephone"
                                               class="form-control" placeholder="+227 XX XX XX XX"
                                               autocomplete="tel" maxlength="20">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="donateur_pays">Pays de résidence</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-globe input-icon"></i>
                                        <select id="donateur_pays" name="donateur_pays"
                                                class="form-control form-select" autocomplete="country">
                                            <option value="Niger" selected>🇳🇪 Niger</option>
                                            <option value="Sénégal">🇸🇳 Sénégal</option>
                                            <option value="Mali">🇲🇱 Mali</option>
                                            <option value="Burkina Faso">🇧🇫 Burkina Faso</option>
                                            <option value="Côte d'Ivoire">🇨🇮 Côte d'Ivoire</option>
                                            <option value="Bénin">🇧🇯 Bénin</option>
                                            <option value="Togo">🇹🇬 Togo</option>
                                            <option value="Nigeria">🇳🇬 Nigeria</option>
                                            <option value="France">🇫🇷 France</option>
                                            <option value="Belgique">🇧🇪 Belgique</option>
                                            <option value="Suisse">🇨🇭 Suisse</option>
                                            <option value="Canada">🇨🇦 Canada</option>
                                            <option value="USA">🇺🇸 USA</option>
                                            <option value="Autre">🌍 Autre</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="message">Message de soutien <span class="label-optional">(optionnel)</span></label>
                                <textarea id="message" name="message" class="form-control" rows="3"
                                          placeholder="Partagez votre message de soutien à la justice au Niger..."
                                          maxlength="500"></textarea>
                                <div class="char-counter"><span id="charCount">0</span>/500</div>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="anonyme" id="anonyme">
                                    <span class="checkmark"></span>
                                    Je souhaite faire ce don <strong>anonymement</strong>
                                </label>
                            </div>

                            <div class="step-nav">
                                <button type="button" class="btn btn-outline-primary" onclick="prevStep(1)">
                                    <i class="fas fa-arrow-left"></i> Retour
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(3)" id="btnStep2Next">
                                    Continuer <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ─── ÉTAPE 3 : Paiement ────────────────────── -->
                        <div class="don-step-content" id="step-3" style="display:none;">
                            <h3 class="step-title"><i class="fas fa-credit-card"></i> Mode de paiement</h3>

                            <!-- Sélection méthode -->
                            <div class="payment-methods-grid">
                                <label class="payment-method-card" data-method="carte_bancaire">
                                    <input type="radio" name="payment_type" value="carte_bancaire" class="payment-radio">
                                    <div class="payment-method-inner">
                                        <div class="payment-logos">
                                            <i class="fab fa-cc-visa" style="color:#1A1F71;font-size:32px;"></i>
                                            <i class="fab fa-cc-mastercard" style="color:#EB001B;font-size:32px;"></i>
                                        </div>
                                        <div>
                                            <strong>Carte Bancaire</strong>
                                            <small>Visa · Mastercard · International</small>
                                        </div>
                                        <div class="payment-secure"><i class="fas fa-lock"></i> SSL</div>
                                    </div>
                                </label>

                                <label class="payment-method-card" data-method="orange_money">
                                    <input type="radio" name="payment_type" value="orange_money" class="payment-radio">
                                    <div class="payment-method-inner">
                                        <div class="payment-logos">
                                            <i class="fas fa-mobile-alt" style="color:#FF6600;font-size:32px;"></i>
                                        </div>
                                        <div>
                                            <strong>Orange Money</strong>
                                            <small>Mobile Money Niger</small>
                                        </div>
                                        <div class="payment-secure"><i class="fas fa-mobile-alt"></i></div>
                                    </div>
                                </label>

                                <label class="payment-method-card" data-method="moov_money">
                                    <input type="radio" name="payment_type" value="moov_money" class="payment-radio">
                                    <div class="payment-method-inner">
                                        <div class="payment-logos">
                                            <i class="fas fa-mobile-alt" style="color:#0033CC;font-size:32px;"></i>
                                        </div>
                                        <div>
                                            <strong>Moov Money</strong>
                                            <small>Mobile Money Niger</small>
                                        </div>
                                        <div class="payment-secure"><i class="fas fa-mobile-alt"></i></div>
                                    </div>
                                </label>

                                <label class="payment-method-card" data-method="paypal">
                                    <input type="radio" name="payment_type" value="paypal" class="payment-radio">
                                    <div class="payment-method-inner">
                                        <div class="payment-logos">
                                            <i class="fab fa-paypal" style="color:#003087;font-size:32px;"></i>
                                        </div>
                                        <div>
                                            <strong>PayPal</strong>
                                            <small>Paiement international</small>
                                        </div>
                                        <div class="payment-secure"><i class="fas fa-lock"></i></div>
                                    </div>
                                </label>
                            </div>

                            <!-- ═══ PANNEAU CARTE BANCAIRE ═══ -->
                            <div id="panel-carte_bancaire" class="payment-panel" style="display:none;">
                                <div class="card-form-container">
                                    <div class="card-form-header">
                                        <i class="fas fa-lock"></i>
                                        <span>Informations de carte sécurisées</span>
                                        <div class="card-brands">
                                            <i class="fab fa-cc-visa"></i>
                                            <i class="fab fa-cc-mastercard"></i>
                                            <i class="fab fa-cc-amex"></i>
                                        </div>
                                    </div>

                                    <!-- Aperçu visuel de la carte -->
                                    <div class="card-preview" id="cardPreview">
                                        <div class="card-preview-inner">
                                            <div class="card-front" id="cardFront">
                                                <div class="card-chip"><i class="fas fa-microchip"></i></div>
                                                <div class="card-network" id="cardNetworkIcon">
                                                    <i class="fas fa-credit-card"></i>
                                                </div>
                                                <div class="card-number-display" id="cardNumberDisplay">
                                                    •••• •••• •••• ••••
                                                </div>
                                                <div class="card-bottom">
                                                    <div>
                                                        <div class="card-label">Titulaire</div>
                                                        <div class="card-holder-display" id="cardHolderDisplay">VOTRE NOM</div>
                                                    </div>
                                                    <div>
                                                        <div class="card-label">Expiration</div>
                                                        <div class="card-expiry-display" id="cardExpiryDisplay">MM/AA</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-back" id="cardBack" style="display:none;">
                                                <div class="card-stripe"></div>
                                                <div class="card-cvv-area">
                                                    <div class="card-cvv-label">CVV</div>
                                                    <div class="card-cvv-display" id="cardCvvDisplay">•••</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Champs carte -->
                                    <div class="card-fields">
                                        <div class="form-group">
                                            <label for="card_number">
                                                Numéro de carte <span class="required">*</span>
                                            </label>
                                            <div class="input-with-icon card-input-wrap">
                                                <i class="fas fa-credit-card input-icon" id="cardTypeIcon"></i>
                                                <input type="text" id="card_number" name="card_number_display"
                                                       class="form-control card-input"
                                                       placeholder="0000 0000 0000 0000"
                                                       maxlength="19" autocomplete="cc-number"
                                                       inputmode="numeric"
                                                       data-secure="true">
                                                <span class="card-valid-icon" id="cardValidIcon" style="display:none;">
                                                    <i class="fas fa-check-circle" style="color:var(--success)"></i>
                                                </span>
                                            </div>
                                            <div class="field-error" id="err-card"></div>
                                            <p class="field-hint">
                                                <i class="fas fa-info-circle"></i>
                                                Vos données de carte ne transitent jamais en clair sur nos serveurs.
                                            </p>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="card_holder">
                                                    Nom du titulaire <span class="required">*</span>
                                                </label>
                                                <div class="input-with-icon">
                                                    <i class="fas fa-user input-icon"></i>
                                                    <input type="text" id="card_holder" name="card_holder"
                                                           class="form-control"
                                                           placeholder="NOM PRÉNOM"
                                                           maxlength="60" autocomplete="cc-name"
                                                           style="text-transform:uppercase;">
                                                </div>
                                                <div class="field-error" id="err-holder"></div>
                                            </div>

                                            <div class="form-group">
                                                <label for="card_expiry">
                                                    Date d'expiration <span class="required">*</span>
                                                </label>
                                                <div class="input-with-icon">
                                                    <i class="fas fa-calendar-alt input-icon"></i>
                                                    <input type="text" id="card_expiry" name="card_expiry"
                                                           class="form-control"
                                                           placeholder="MM/AA"
                                                           maxlength="5" autocomplete="cc-exp"
                                                           inputmode="numeric">
                                                </div>
                                                <div class="field-error" id="err-expiry"></div>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="card_cvv">
                                                    CVV / CVC <span class="required">*</span>
                                                    <button type="button" class="cvv-help-btn" onclick="toggleCvvHelp()">
                                                        <i class="fas fa-question-circle"></i>
                                                    </button>
                                                </label>
                                                <div class="input-with-icon">
                                                    <i class="fas fa-lock input-icon"></i>
                                                    <input type="password" id="card_cvv" name="card_cvv_display"
                                                           class="form-control"
                                                           placeholder="•••"
                                                           maxlength="4" autocomplete="cc-csc"
                                                           inputmode="numeric"
                                                           data-secure="true">
                                                    <button type="button" class="cvv-toggle-btn" id="cvvToggle">
                                                        <i class="fas fa-eye" id="cvvEyeIcon"></i>
                                                    </button>
                                                </div>
                                                <div class="field-error" id="err-cvv"></div>
                                            </div>

                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div id="cvvHelpBox" class="cvv-help" style="display:none;">
                                                    <img src="<?= SITE_URL ?>/assets/images/cvv-help.svg" alt="CVV"
                                                         onerror="this.style.display='none'">
                                                    <p>
                                                        <strong>Visa / Mastercard :</strong> 3 chiffres au dos de la carte.<br>
                                                        <strong>Amex :</strong> 4 chiffres au recto.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- .card-fields -->

                                    <!-- Certification sécurité -->
                                    <div class="security-certifications">
                                        <div class="cert-item">
                                            <i class="fas fa-shield-alt"></i>
                                            <span>SSL 256 bits</span>
                                        </div>
                                        <div class="cert-item">
                                            <i class="fas fa-lock"></i>
                                            <span>PCI-DSS</span>
                                        </div>
                                        <div class="cert-item">
                                            <i class="fas fa-check-circle"></i>
                                            <span>3D Secure</span>
                                        </div>
                                        <div class="cert-item">
                                            <i class="fas fa-eye-slash"></i>
                                            <span>Données chiffrées</span>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- #panel-carte_bancaire -->

                            <!-- ═══ PANNEAU ORANGE MONEY ═══ -->
                            <div id="panel-orange_money" class="payment-panel" style="display:none;">
                                <div class="mobile-payment-info orange-info">
                                    <div class="mobile-payment-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div>
                                        <h4>Paiement via Orange Money Niger</h4>
                                        <p>Après validation, vous recevrez un message USSD sur votre téléphone Orange pour confirmer le paiement de <strong id="om-montant">10 000 FCFA</strong>.</p>
                                        <div class="form-group" style="margin-top:16px;">
                                            <label for="om_phone">Numéro Orange Money <span class="required">*</span></label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-phone input-icon"></i>
                                                <input type="tel" id="om_phone" name="om_phone"
                                                       class="form-control" placeholder="+227 XX XX XX XX"
                                                       inputmode="tel" maxlength="20">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ═══ PANNEAU MOOV MONEY ═══ -->
                            <div id="panel-moov_money" class="payment-panel" style="display:none;">
                                <div class="mobile-payment-info moov-info">
                                    <div class="mobile-payment-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div>
                                        <h4>Paiement via Moov Money Niger</h4>
                                        <p>Après validation, vous recevrez une notification Moov Money pour confirmer le paiement de <strong id="mm-montant">10 000 FCFA</strong>.</p>
                                        <div class="form-group" style="margin-top:16px;">
                                            <label for="mm_phone">Numéro Moov Money <span class="required">*</span></label>
                                            <div class="input-with-icon">
                                                <i class="fas fa-phone input-icon"></i>
                                                <input type="tel" id="mm_phone" name="mm_phone"
                                                       class="form-control" placeholder="+227 XX XX XX XX"
                                                       inputmode="tel" maxlength="20">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ═══ PANNEAU PAYPAL ═══ -->
                            <div id="panel-paypal" class="payment-panel" style="display:none;">
                                <div class="mobile-payment-info paypal-info">
                                    <div class="mobile-payment-icon">
                                        <i class="fab fa-paypal"></i>
                                    </div>
                                    <div>
                                        <h4>Paiement via PayPal</h4>
                                        <p>Vous serez redirigé vers PayPal pour effectuer votre don de <strong id="pp-montant">10 000 FCFA</strong> en toute sécurité.</p>
                                        <p style="font-size:12px;color:var(--gray);margin-top:8px;">
                                            <i class="fas fa-info-circle"></i> Vous n'avez pas besoin d'un compte PayPal. Vous pouvez payer avec votre carte directement.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="step-nav">
                                <button type="button" class="btn btn-outline-primary" onclick="prevStep(2)">
                                    <i class="fas fa-arrow-left"></i> Retour
                                </button>
                                <button type="button" class="btn btn-primary" onclick="nextStep(4)" id="btnStep3Next">
                                    Continuer <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div><!-- #step-3 -->

                        <!-- ─── ÉTAPE 4 : Confirmation ────────────────── -->
                        <div class="don-step-content" id="step-4" style="display:none;">
                            <h3 class="step-title"><i class="fas fa-check-circle"></i> Récapitulatif</h3>

                            <div class="recap-card">
                                <div class="recap-row">
                                    <span><i class="fas fa-coins"></i> Montant</span>
                                    <strong id="recap-montant" class="recap-amount">10 000 FCFA</strong>
                                </div>
                                <div class="recap-row" id="recap-projet-row" style="display:none;">
                                    <span><i class="fas fa-project-diagram"></i> Projet</span>
                                    <span id="recap-projet">—</span>
                                </div>
                                <div class="recap-row">
                                    <span><i class="fas fa-user"></i> Donateur</span>
                                    <span id="recap-nom">—</span>
                                </div>
                                <div class="recap-row">
                                    <span><i class="fas fa-envelope"></i> Email</span>
                                    <span id="recap-email">—</span>
                                </div>
                                <div class="recap-row">
                                    <span><i class="fas fa-credit-card"></i> Paiement</span>
                                    <span id="recap-methode">—</span>
                                </div>
                                <div class="recap-row" id="recap-carte-row" style="display:none;">
                                    <span><i class="fas fa-credit-card"></i> Carte</span>
                                    <span id="recap-carte">•••• •••• •••• ••••</span>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top:20px;">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="accept-cgu" required>
                                    <span class="checkmark"></span>
                                    J'accepte les <a href="<?= SITE_URL ?>/conditions-generales" target="_blank">conditions générales</a>
                                    et la <a href="<?= SITE_URL ?>/politique-confidentialite" target="_blank">politique de confidentialité</a>
                                    <span class="required">*</span>
                                </label>
                                <div class="field-error" id="err-cgu"></div>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="accept-rgpd">
                                    <span class="checkmark"></span>
                                    J'accepte de recevoir des informations sur les projets du FAJ Niger par email.
                                </label>
                            </div>

                            <div id="formError" class="alert alert-danger" style="display:none;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span id="formErrorMsg">Une erreur est survenue.</span>
                            </div>

                            <div class="step-nav">
                                <button type="button" class="btn btn-outline-primary" onclick="prevStep(3)">
                                    <i class="fas fa-arrow-left"></i> Retour
                                </button>
                                <button type="submit" class="btn btn-primary btn-submit-don" id="btnSubmitDon">
                                    <i class="fas fa-lock"></i>
                                    <span id="btnSubmitText">Confirmer mon Don</span>
                                    <div class="btn-loader" id="btnLoader" style="display:none;">
                                        <i class="fas fa-spinner fa-spin"></i> Traitement...
                                    </div>
                                </button>
                            </div>

                            <div class="submit-security-note">
                                <i class="fas fa-shield-alt"></i>
                                Paiement 100% sécurisé · Données protégées · Reçu officiel par email
                            </div>
                        </div><!-- #step-4 -->

                    </form><!-- #donForm -->
                </div><!-- .don-form-container -->
            </div><!-- .don-main -->

            <!-- ═══════════════════ SIDEBAR ═══════════════════ -->
            <aside class="don-sidebar">

                <!-- Impact -->
                <div class="sidebar-card sidebar-impact">
                    <h3><i class="fas fa-chart-line"></i> Impact de votre Don</h3>
                    <ul class="impact-list">
                        <li>
                            <div class="impact-bullet"></div>
                            <div>
                                <strong>500 FCFA</strong>
                                <span>Finance 1h d'aide juridictionnelle</span>
                            </div>
                        </li>
                        <li>
                            <div class="impact-bullet"></div>
                            <div>
                                <strong>5 000 FCFA</strong>
                                <span>Couvre 1 journée de formation judiciaire</span>
                            </div>
                        </li>
                        <li>
                            <div class="impact-bullet"></div>
                            <div>
                                <strong>25 000 FCFA</strong>
                                <span>Équipe partiellement un greffe de tribunal</span>
                            </div>
                        </li>
                        <li>
                            <div class="impact-bullet"></div>
                            <div>
                                <strong>100 000 FCFA</strong>
                                <span>Finance une mission de sensibilisation</span>
                            </div>
                        </li>
                        <li>
                            <div class="impact-bullet"></div>
                            <div>
                                <strong>500 000 FCFA</strong>
                                <span>Contribue à la construction d'une salle d'audience</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Paiements sécurisés -->
                <div class="sidebar-card sidebar-security">
                    <h4><i class="fas fa-lock"></i> Paiements Sécurisés</h4>
                    <div class="payment-icons-grid">
                        <div class="pay-icon-item">
                            <i class="fab fa-cc-visa"></i>
                            <span>Visa</span>
                        </div>
                        <div class="pay-icon-item">
                            <i class="fab fa-cc-mastercard"></i>
                            <span>Mastercard</span>
                        </div>
                        <div class="pay-icon-item">
                            <i class="fab fa-paypal"></i>
                            <span>PayPal</span>
                        </div>
                        <div class="pay-icon-item">
                            <i class="fas fa-mobile-alt" style="color:#FF6600;"></i>
                            <span>Orange</span>
                        </div>
                        <div class="pay-icon-item">
                            <i class="fas fa-mobile-alt" style="color:#0033CC;"></i>
                            <span>Moov</span>
                        </div>
                        <div class="pay-icon-item">
                            <i class="fab fa-cc-amex"></i>
                            <span>Amex</span>
                        </div>
                    </div>
                    <div class="security-badges-row">
                        <span class="sec-badge"><i class="fas fa-shield-alt"></i> SSL 256</span>
                        <span class="sec-badge"><i class="fas fa-lock"></i> PCI-DSS</span>
                        <span class="sec-badge"><i class="fas fa-check"></i> 3D Secure</span>
                    </div>
                </div>

                <!-- Contact aide -->
                <div class="sidebar-card sidebar-help">
                    <h4><i class="fas fa-question-circle"></i> Besoin d'aide ?</h4>
                    <p>Notre équipe est disponible pour vous assister dans votre démarche de don.</p>
                    <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-envelope"></i> Nous Contacter
                    </a>
                </div>

                <!-- Dernier don -->
                <div class="sidebar-card sidebar-live" id="liveDonCard" style="display:none;">
                    <div class="live-dot"></div>
                    <h4>Don récent</h4>
                    <p id="liveDonText">—</p>
                </div>

            </aside><!-- .don-sidebar -->

        </div><!-- .don-layout -->
    </div><!-- .container -->
</section>

<!-- Modal succès don -->
<div class="modal-overlay" id="donSuccessModal" style="display:none;">
    <div class="modal modal-success">
        <div class="modal-success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3>🎉 Don Confirmé !</h3>
        <p>Merci pour votre généreux don au FAJ Niger.<br>
           Référence : <strong id="successRef">—</strong><br>
           Montant : <strong id="successMontant">—</strong>
        </p>
        <p style="font-size:13px;color:var(--gray);margin-top:8px;">
            Un reçu officiel vous a été envoyé à votre adresse email.
        </p>
        <div style="display:flex;gap:12px;justify-content:center;margin-top:20px;">
            <a href="<?= SITE_URL ?>/" class="btn btn-primary">
                <i class="fas fa-home"></i> Accueil
            </a>
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>
</div>

<!-- CSS supplémentaire pour le formulaire de don -->
<style>
/* ─── Layout don ─────────────────────────────────── */
.don-section { padding: 80px 0; background: var(--light); }
.don-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 50px;
    align-items: start;
}
.don-form-container {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}
.don-form-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 28px 40px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
}
.don-form-icon {
    width: 50px; height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    color: var(--secondary-light);
    flex-shrink: 0;
}
.don-form-header h2 { font-size: 20px; margin: 0; color: white; }
.don-form-header p  { font-size: 12px; margin: 4px 0 0; opacity: 0.8; }
.security-badge {
    margin-left: auto;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 20px;
    padding: 6px 14px;
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600; color: #4ade80;
    white-space: nowrap;
}

/* ─── Étapes ─────────────────────────────────────── */
.don-steps {
    display: flex; align-items: center;
    padding: 24px 40px;
    background: var(--light);
    border-bottom: 1px solid var(--border);
    gap: 0;
}
.don-step {
    display: flex; flex-direction: column;
    align-items: center; gap: 6px;
    cursor: default;
    flex: none;
}
.step-number {
    width: 36px; height: 36px;
    border-radius: 50%;
    background: white;
    border: 2px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 14px;
    color: var(--gray);
    transition: all .3s;
}
.don-step.active .step-number {
    background: var(--secondary);
    border-color: var(--secondary);
    color: white;
    box-shadow: 0 0 0 4px rgba(232,135,10,.2);
}
.don-step.completed .step-number {
    background: var(--success);
    border-color: var(--success);
    color: white;
}
.don-step span {
    font-size: 11px; font-weight: 600;
    color: var(--gray);
    text-transform: uppercase; letter-spacing: .5px;
}
.don-step.active span { color: var(--secondary); }
.step-line {
    flex: 1;
    height: 2px;
    background: var(--border);
    margin-bottom: 22px;
}

/* ─── Contenu étape ──────────────────────────────── */
.don-step-content { padding: 36px 40px; }
.step-title {
    font-size: 18px; font-weight: 700;
    color: var(--primary);
    margin-bottom: 28px;
    display: flex; align-items: center; gap: 10px;
}
.step-title i { color: var(--secondary); }

/* ─── Montants ───────────────────────────────────── */
.amount-presets {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 0;
}
.amount-btn {
    border: 2px solid var(--border);
    border-radius: var(--radius);
    background: white;
    padding: 14px 8px;
    cursor: pointer;
    transition: all .25s;
    text-align: center;
    position: relative;
    display: flex; flex-direction: column;
    align-items: center;
}
.amount-btn:hover { border-color: var(--secondary); color: var(--secondary); }
.amount-btn.active {
    border-color: var(--secondary);
    background: rgba(232,135,10,.08);
    color: var(--secondary);
}
.amount-value { font-size: 16px; font-weight: 700; line-height: 1; }
.amount-currency { font-size: 11px; color: var(--gray); margin-top: 4px; }
.amount-badge {
    position: absolute; top: -8px; left: 50%; transform: translateX(-50%);
    background: var(--secondary);
    color: white; font-size: 9px; font-weight: 700;
    padding: 2px 8px; border-radius: 20px;
    text-transform: uppercase; letter-spacing: .5px;
    white-space: nowrap;
}
.amount-custom-input { margin-top: 12px; }
.input-with-unit { position: relative; }
.input-unit {
    position: absolute; right: 16px; top: 50%;
    transform: translateY(-50%);
    color: var(--gray); font-size: 13px; font-weight: 600;
}
.input-hint { font-size: 12px; color: var(--gray); margin-top: 6px; }

/* ─── Impact preview ─────────────────────────────── */
.impact-preview {
    display: flex; align-items: center; gap: 14px;
    background: rgba(27,42,74,.05);
    border: 1px solid rgba(27,42,74,.1);
    border-radius: var(--radius);
    padding: 16px 20px;
    margin: 20px 0;
}
.impact-icon {
    width: 42px; height: 42px;
    background: var(--secondary);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 18px;
    flex-shrink: 0;
}
.impact-text { font-size: 14px; color: var(--primary); }
.impact-text strong { display: block; font-size: 18px; color: var(--secondary); }

/* ─── Moyens de paiement ─────────────────────────── */
.payment-methods-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
.payment-method-card { cursor: pointer; }
.payment-method-card input { display: none; }
.payment-method-inner {
    border: 2px solid var(--border);
    border-radius: var(--radius);
    padding: 16px;
    display: flex; align-items: center; gap: 12px;
    transition: all .25s;
    background: white;
}
.payment-method-inner strong { display: block; font-size: 14px; color: var(--primary); }
.payment-method-inner small  { font-size: 11px; color: var(--gray); }
.payment-method-card:has(input:checked) .payment-method-inner {
    border-color: var(--secondary);
    background: rgba(232,135,10,.06);
}
.payment-logos { display: flex; gap: 4px; align-items: center; flex-shrink: 0; }
.payment-secure {
    margin-left: auto;
    color: var(--success);
    font-size: 12px;
    font-weight: 600;
}
.payment-panel {
    border-radius: var(--radius);
    border: 1px solid var(--border);
    overflow: hidden;
    margin-bottom: 20px;
}

/* ─── Formulaire carte bancaire ──────────────────── */
.card-form-container { padding: 24px; background: var(--light); }
.card-form-header {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; font-weight: 600;
    color: var(--primary); margin-bottom: 20px;
}
.card-brands { margin-left: auto; display: flex; gap: 8px; font-size: 22px; }
.card-brands .fa-cc-visa      { color: #1A1F71; }
.card-brands .fa-cc-mastercard{ color: #EB001B; }
.card-brands .fa-cc-amex      { color: #2E77BC; }

/* Aperçu visuel carte */
.card-preview {
    width: 100%; max-width: 340px;
    margin: 0 auto 24px;
    perspective: 1000px;
}
.card-front, .card-back {
    background: linear-gradient(135deg, var(--primary) 0%, #2a4080 100%);
    border-radius: 16px;
    padding: 24px;
    color: white;
    position: relative;
    box-shadow: 0 12px 35px rgba(27,42,74,.45);
    min-height: 180px;
    font-family: 'Courier New', monospace;
}
.card-chip {
    width: 40px; height: 30px;
    background: linear-gradient(135deg, #d4a843, #f0c860);
    border-radius: 5px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #8b6914;
    margin-bottom: 20px;
}
.card-network { position: absolute; top: 20px; right: 20px; font-size: 32px; }
.card-number-display {
    font-size: 18px; letter-spacing: 3px;
    margin-bottom: 20px;
    text-shadow: 0 1px 3px rgba(0,0,0,.3);
}
.card-bottom { display: flex; justify-content: space-between; }
.card-label  { font-size: 9px; opacity: .6; text-transform: uppercase; letter-spacing: 1px; }
.card-holder-display, .card-expiry-display {
    font-size: 14px; font-weight: 600; letter-spacing: 1px;
    text-transform: uppercase;
}
.card-stripe {
    background: rgba(0,0,0,.6);
    height: 44px; margin: 24px -24px 16px;
}
.card-cvv-area {
    background: white; border-radius: 4px;
    padding: 8px 12px;
    width: 80px; margin-left: auto;
    text-align: center;
}
.card-cvv-label { font-size: 9px; color: var(--gray); text-transform: uppercase; }
.card-cvv-display { color: var(--primary); font-size: 16px; letter-spacing: 4px; }

/* Champs carte */
.card-fields { background: white; padding: 20px; border-radius: var(--radius); }
.card-input-wrap { position: relative; }
.card-valid-icon {
    position: absolute; right: 42px; top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
}
.cvv-toggle-btn {
    position: absolute; right: 12px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none;
    color: var(--gray); cursor: pointer;
    padding: 4px; font-size: 14px;
}
.cvv-help-btn {
    background: none; border: none;
    color: var(--secondary); cursor: pointer;
    font-size: 14px; padding: 0 4px;
}
.cvv-help {
    background: var(--light);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 12px;
    font-size: 12px;
    color: var(--primary);
}

/* Certifications sécurité */
.security-certifications {
    display: flex; gap: 8px; flex-wrap: wrap;
    margin-top: 16px; padding-top: 16px;
    border-top: 1px solid var(--border);
}
.cert-item {
    display: flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600;
    color: var(--success);
    background: rgba(34,197,94,.08);
    padding: 4px 10px; border-radius: 20px;
}

/* Paiement mobile */
.mobile-payment-info {
    display: flex; gap: 20px; padding: 24px;
    align-items: flex-start;
}
.mobile-payment-icon {
    width: 56px; height: 56px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; flex-shrink: 0;
}
.orange-info .mobile-payment-icon { background: rgba(255,102,0,.1); color: #FF6600; }
.moov-info   .mobile-payment-icon { background: rgba(0,51,204,.1);  color: #0033CC; }
.paypal-info .mobile-payment-icon { background: rgba(0,48,135,.1);  color: #003087; }
.mobile-payment-info h4 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
.mobile-payment-info p  { font-size: 14px; color: var(--gray); }

/* ─── Récapitulatif ─────────────────────────────── */
.recap-card {
    background: var(--light);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 20px;
}
.recap-row {
    display: flex; justify-content: space-between;
    align-items: center;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
}
.recap-row:last-child { border-bottom: none; }
.recap-row span:first-child { color: var(--gray); display: flex; align-items: center; gap: 8px; }
.recap-amount { font-size: 20px; font-weight: 800; color: var(--secondary); }

/* ─── Navigation étapes ─────────────────────────── */
.step-nav {
    display: flex; justify-content: space-between;
    gap: 16px; margin-top: 28px;
}
.btn-block { width: 100%; justify-content: center; margin-top: 20px; }
.btn-step-next { font-size: 16px; padding: 16px 32px; }
.btn-submit-don {
    flex: 1; justify-content: center;
    font-size: 16px; padding: 18px 32px;
    position: relative;
}
.submit-security-note {
    text-align: center; font-size: 12px;
    color: var(--gray); margin-top: 12px;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.submit-security-note i { color: var(--success); }

/* ─── Sidebar ────────────────────────────────────── */
.don-sidebar { display: flex; flex-direction: column; gap: 20px; }
.sidebar-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 28px;
    box-shadow: var(--shadow);
}
.sidebar-card h3, .sidebar-card h4 {
    font-size: 16px; font-weight: 700;
    color: var(--primary); margin-bottom: 20px;
    display: flex; align-items: center; gap: 8px;
}
.sidebar-card h3 i, .sidebar-card h4 i { color: var(--secondary); }
.sidebar-impact {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
}
.sidebar-impact h3 { color: var(--secondary-light); }
.impact-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 14px; }
.impact-list li { display: flex; gap: 12px; align-items: flex-start; }
.impact-bullet {
    width: 10px; height: 10px; border-radius: 50%;
    background: var(--secondary); flex-shrink: 0; margin-top: 4px;
}
.impact-list strong { color: var(--secondary-light); display: block; font-size: 15px; }
.impact-list span   { color: rgba(255,255,255,.7); font-size: 13px; }
.payment-icons-grid {
    display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 14px;
}
.pay-icon-item {
    text-align: center; padding: 10px;
    background: var(--light); border-radius: 8px;
    display: flex; flex-direction: column; align-items: center; gap: 5px;
}
.pay-icon-item i    { font-size: 26px; }
.pay-icon-item span { font-size: 10px; font-weight: 600; color: var(--primary); }
.pay-icon-item .fa-cc-visa       { color: #1A1F71; }
.pay-icon-item .fa-cc-mastercard { color: #EB001B; }
.pay-icon-item .fa-paypal        { color: #003087; }
.pay-icon-item .fa-cc-amex       { color: #2E77BC; }
.security-badges-row { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; }
.sec-badge {
    font-size: 11px; font-weight: 600;
    background: rgba(34,197,94,.1); color: #15803d;
    padding: 4px 10px; border-radius: 20px;
    display: flex; align-items: center; gap: 4px;
}
.sidebar-help { background: rgba(232,135,10,.06); border: 1px solid rgba(232,135,10,.2); }
.sidebar-help p { font-size: 13px; color: var(--gray); margin-bottom: 14px; }
.sidebar-live {
    background: white; position: relative;
    border-left: 4px solid var(--success);
}
.live-dot {
    width: 10px; height: 10px; border-radius: 50%;
    background: var(--success);
    position: absolute; top: 14px; right: 14px;
    animation: pulse 2s infinite;
}
.sidebar-live h4 { color: var(--success); }
.sidebar-live p  { font-size: 13px; color: var(--gray); }

/* ─── Champs formulaire ─────────────────────────── */
.input-with-icon { position: relative; }
.input-icon {
    position: absolute; left: 14px; top: 50%;
    transform: translateY(-50%);
    color: var(--gray); font-size: 14px;
    z-index: 1; pointer-events: none;
}
.input-with-icon .form-control { padding-left: 42px; }
.field-error { color: var(--danger); font-size: 12px; margin-top: 4px; }
.field-hint  { font-size: 11px; color: var(--gray); margin-top: 4px; display: flex; align-items: center; gap: 4px; }
.char-counter { text-align: right; font-size: 11px; color: var(--gray); margin-top: 4px; }
.label-optional { font-size: 12px; color: var(--gray); font-weight: 400; }
.required { color: var(--danger); }

/* Checkbox personnalisée */
.checkbox-label {
    display: flex; align-items: flex-start; gap: 10px;
    cursor: pointer; font-size: 14px; color: var(--primary);
}
.checkbox-label input[type="checkbox"] { width: 18px; height: 18px; margin-top: 1px; flex-shrink: 0; accent-color: var(--secondary); }
.checkbox-label a { color: var(--secondary); font-weight: 600; }

/* Modal succès */
.modal-success {
    text-align: center; padding: 50px 40px;
}
.modal-success-icon {
    font-size: 60px; color: var(--success);
    margin-bottom: 16px;
    animation: bounceIn .6s;
}
@keyframes bounceIn {
    0%   { transform: scale(0); }
    60%  { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* ─── Responsive ─────────────────────────────────── */
@media (max-width: 1100px) {
    .don-layout { grid-template-columns: 1fr; }
    .don-sidebar { order: -1; }
}
@media (max-width: 600px) {
    .amount-presets { grid-template-columns: repeat(2, 1fr); }
    .payment-methods-grid { grid-template-columns: 1fr; }
    .don-form-header { flex-wrap: wrap; }
    .security-badge { display: none; }
    .don-step-content { padding: 24px 20px; }
    .don-steps { padding: 16px 20px; }
}
</style>

<script>
// ═══════════════════════════════════════════════════
//  FORMULAIRE DE DON – Logique complète
// ═══════════════════════════════════════════════════

let currentStep = 1;
let selectedMethod = '';
const impactMap = {
    500:    '1h d\'aide juridictionnelle financée',
    1000:   '2h d\'aide juridictionnelle financées',
    5000:   '1 journée de formation judiciaire couverte',
    10000:  '1 journée de formation judiciaire + matériaux',
    25000:  '1 greffe partiellement équipé',
    50000:  '1/2 mission de sensibilisation régionale',
    100000: '1 mission de sensibilisation dans une région',
    500000: 'Contribution à une salle d\'audience',
};

// ─── Navigation par étapes ───────────────────────────────────────────────────
function nextStep(n) {
    if (n === 2 && !validateStep1()) return;
    if (n === 3 && !validateStep2()) return;
    if (n === 4 && !validateStep3()) return;
    if (n === 4) buildRecap();

    document.getElementById('step-' + currentStep).style.display = 'none';
    document.getElementById('step-indicator-' + currentStep).classList.remove('active');
    document.getElementById('step-indicator-' + currentStep).classList.add('completed');

    currentStep = n;
    document.getElementById('step-' + currentStep).style.display = 'block';
    document.getElementById('step-indicator-' + currentStep).classList.add('active');

    // Scroll vers le haut du formulaire
    document.querySelector('.don-form-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function prevStep(n) {
    document.getElementById('step-' + currentStep).style.display = 'none';
    document.getElementById('step-indicator-' + currentStep).classList.remove('active');

    currentStep = n;
    document.getElementById('step-' + currentStep).style.display = 'block';
    document.getElementById('step-indicator-' + currentStep).classList.add('active');
    document.getElementById('step-indicator-' + (n + 1)).classList.remove('completed');
}

// ─── Montants ────────────────────────────────────────────────────────────────
document.querySelectorAll('.amount-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        const amount = parseInt(this.dataset.amount) || 0;
        if (this.dataset.amount === 'custom') {
            document.getElementById('customAmountWrap').style.display = 'block';
            document.getElementById('montant_custom').focus();
        } else {
            document.getElementById('customAmountWrap').style.display = 'none';
            document.getElementById('montant').value = amount;
            updateImpact(amount);
            updateMontantRefs(amount);
        }
    });
});

document.getElementById('montant_custom')?.addEventListener('input', function () {
    const v = parseInt(this.value) || 0;
    document.getElementById('montant').value = v;
    updateImpact(v);
    updateMontantRefs(v);
});

function updateImpact(amount) {
    const preview = document.getElementById('impactPreview');
    if (!preview) return;
    // Trouver l'impact le plus proche
    const keys = Object.keys(impactMap).map(Number).sort((a,b) => a-b);
    let impact = 'Votre contribution fait la différence';
    for (let k of keys) {
        if (amount >= k) impact = impactMap[k];
    }
    preview.querySelector('.impact-text').innerHTML =
        '<strong>' + formatAmount(amount) + '</strong> → ' + impact;
}

function updateMontantRefs(amount) {
    const fmt = formatAmount(amount);
    ['om-montant','mm-montant','pp-montant'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = fmt;
    });
}

function formatAmount(n) {
    return n.toLocaleString('fr-FR') + ' FCFA';
}

// ─── Sélection méthode de paiement ──────────────────────────────────────────
document.querySelectorAll('.payment-radio').forEach(radio => {
    radio.addEventListener('change', function () {
        selectedMethod = this.value;
        document.getElementById('methode_paiement').value = selectedMethod;

        // Masquer tous les panneaux
        document.querySelectorAll('.payment-panel').forEach(p => p.style.display = 'none');

        // Afficher le bon panneau
        const panel = document.getElementById('panel-' + selectedMethod);
        if (panel) panel.style.display = 'block';
    });
});

// ─── Aperçu carte visuel ─────────────────────────────────────────────────────
const cardNumber  = document.getElementById('card_number');
const cardHolder  = document.getElementById('card_holder');
const cardExpiry  = document.getElementById('card_expiry');
const cardCvv     = document.getElementById('card_cvv');

if (cardNumber) {
    // Formatage numéro carte : groupes de 4
    cardNumber.addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '').substring(0, 16);
        this.value = v.replace(/(.{4})/g, '$1 ').trim();

        document.getElementById('cardNumberDisplay').textContent =
            v.padEnd(16, '•').replace(/(.{4})/g, '$1 ').trim();

        // Détecter le type de carte
        updateCardType(v);

        // Algorithme de Luhn
        const icon = document.getElementById('cardValidIcon');
        if (v.length === 16) {
            icon.style.display = luhnCheck(v) ? 'inline' : 'none';
        } else {
            icon.style.display = 'none';
        }
    });

    cardHolder.addEventListener('input', function () {
        this.value = this.value.toUpperCase();
        document.getElementById('cardHolderDisplay').textContent =
            this.value || 'VOTRE NOM';
    });

    cardExpiry.addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '');
        if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2,4);
        this.value = v;
        document.getElementById('cardExpiryDisplay').textContent = this.value || 'MM/AA';
    });

    // Basculer verso pour le CVV
    cardCvv.addEventListener('focus', function () {
        document.getElementById('cardFront').style.display = 'none';
        document.getElementById('cardBack').style.display  = 'block';
    });
    cardCvv.addEventListener('blur', function () {
        document.getElementById('cardFront').style.display = 'block';
        document.getElementById('cardBack').style.display  = 'none';
    });
    cardCvv.addEventListener('input', function () {
        const v = this.value.replace(/\D/g, '').substring(0, 4);
        this.value = v;
        document.getElementById('cardCvvDisplay').textContent =
            v ? '•'.repeat(v.length) : '•••';
    });

    // Bouton afficher/masquer CVV
    document.getElementById('cvvToggle')?.addEventListener('click', function () {
        const field = document.getElementById('card_cvv');
        const icon  = document.getElementById('cvvEyeIcon');
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            field.type = 'password';
            icon.className = 'fas fa-eye';
        }
    });
}

function updateCardType(number) {
    const icon = document.getElementById('cardNetworkIcon');
    const typeIcon = document.getElementById('cardTypeIcon');
    let type = '';

    if (/^4/.test(number))       type = 'visa';
    else if (/^5[1-5]/.test(number) || /^2[2-7]/.test(number)) type = 'mastercard';
    else if (/^3[47]/.test(number))  type = 'amex';

    const iconMap = {
        visa:       ['fab fa-cc-visa',       '#1A1F71'],
        mastercard: ['fab fa-cc-mastercard', '#EB001B'],
        amex:       ['fab fa-cc-amex',       '#2E77BC'],
    };
    if (iconMap[type]) {
        icon.innerHTML = '<i class="' + iconMap[type][0] + '" style="color:' + iconMap[type][1] + '"></i>';
        typeIcon.className = iconMap[type][0] + ' input-icon';
    } else {
        icon.innerHTML = '<i class="fas fa-credit-card"></i>';
        typeIcon.className = 'fas fa-credit-card input-icon';
    }
}

// ─── Algorithme de Luhn ──────────────────────────────────────────────────────
function luhnCheck(num) {
    const arr = num.split('').reverse().map(Number);
    const sum = arr.reduce((acc, n, i) => {
        if (i % 2 !== 0) { n *= 2; if (n > 9) n -= 9; }
        return acc + n;
    }, 0);
    return sum % 10 === 0;
}

// ─── Afficher/masquer aide CVV ───────────────────────────────────────────────
function toggleCvvHelp() {
    const box = document.getElementById('cvvHelpBox');
    if (box) box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

// ─── Compteur de caractères ──────────────────────────────────────────────────
document.getElementById('message')?.addEventListener('input', function () {
    document.getElementById('charCount').textContent = this.value.length;
});

// ─── Validation étape 1 ──────────────────────────────────────────────────────
function validateStep1() {
    const montant = parseInt(document.getElementById('montant').value) || 0;
    if (montant < 500) {
        showError('Le montant minimum est de 500 FCFA');
        return false;
    }
    return true;
}

// ─── Validation étape 2 ──────────────────────────────────────────────────────
function validateStep2() {
    let valid = true;
    clearErrors(['err-nom', 'err-email']);

    const nom   = document.getElementById('donateur_nom').value.trim();
    const email = document.getElementById('donateur_email').value.trim();

    if (!nom || nom.length < 2) {
        showFieldError('err-nom', 'Le nom complet est obligatoire (min. 2 caractères)');
        valid = false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showFieldError('err-email', 'Adresse email invalide');
        valid = false;
    }
    return valid;
}

// ─── Validation étape 3 ──────────────────────────────────────────────────────
function validateStep3() {
    if (!selectedMethod) {
        showError('Veuillez sélectionner un mode de paiement');
        return false;
    }

    if (selectedMethod === 'carte_bancaire') {
        clearErrors(['err-card','err-holder','err-expiry','err-cvv']);
        let valid = true;
        const num    = (document.getElementById('card_number')?.value || '').replace(/\s/g,'');
        const holder = document.getElementById('card_holder')?.value.trim();
        const expiry = document.getElementById('card_expiry')?.value.trim();
        const cvv    = document.getElementById('card_cvv')?.value.trim();

        if (num.length < 13 || !luhnCheck(num)) {
            showFieldError('err-card', 'Numéro de carte invalide (vérifiez les 16 chiffres)');
            valid = false;
        }
        if (!holder || holder.length < 3) {
            showFieldError('err-holder', 'Nom du titulaire requis');
            valid = false;
        }
        if (!validateExpiry(expiry)) {
            showFieldError('err-expiry', 'Date d\'expiration invalide ou carte expirée');
            valid = false;
        }
        if (!cvv || cvv.length < 3) {
            showFieldError('err-cvv', 'CVV invalide (3 ou 4 chiffres)');
            valid = false;
        }
        return valid;
    }
    return true;
}

function validateExpiry(val) {
    if (!/^\d{2}\/\d{2}$/.test(val)) return false;
    const [mm, yy] = val.split('/').map(Number);
    if (mm < 1 || mm > 12) return false;
    const now   = new Date();
    const expDate = new Date(2000 + yy, mm - 1, 1);
    return expDate >= new Date(now.getFullYear(), now.getMonth(), 1);
}

// ─── Récapitulatif ───────────────────────────────────────────────────────────
function buildRecap() {
    const montant = parseInt(document.getElementById('montant').value) || 0;
    const nom     = document.getElementById('donateur_nom').value.trim();
    const email   = document.getElementById('donateur_email').value.trim();
    const select  = document.getElementById('projet_id');
    const projetNom = select?.options[select.selectedIndex]?.text || '';

    document.getElementById('recap-montant').textContent = formatAmount(montant);
    document.getElementById('recap-nom').textContent     = nom;
    document.getElementById('recap-email').textContent   = email;

    const methodLabels = {
        carte_bancaire: '💳 Carte Bancaire (Visa / Mastercard)',
        orange_money:   '📱 Orange Money Niger',
        moov_money:     '📱 Moov Money Niger',
        paypal:         '🔵 PayPal',
    };
    document.getElementById('recap-methode').textContent = methodLabels[selectedMethod] || selectedMethod;

    // Projet
    if (select?.value) {
        document.getElementById('recap-projet-row').style.display = 'flex';
        document.getElementById('recap-projet').textContent = projetNom;
    } else {
        document.getElementById('recap-projet-row').style.display = 'none';
    }

    // Carte masquée (4 derniers chiffres)
    if (selectedMethod === 'carte_bancaire') {
        const num = (document.getElementById('card_number')?.value || '').replace(/\s/g,'');
        document.getElementById('recap-carte-row').style.display = 'flex';
        document.getElementById('recap-carte').textContent = '•••• •••• •••• ' + num.slice(-4);
    } else {
        document.getElementById('recap-carte-row').style.display = 'none';
    }
}

// ─── Soumission du formulaire ─────────────────────────────────────────────────
document.getElementById('donForm')?.addEventListener('submit', async function (e) {
    e.preventDefault();

    // Valider CGU
    if (!document.getElementById('accept-cgu').checked) {
        showFieldError('err-cgu', 'Vous devez accepter les conditions générales');
        return;
    }

    // Afficher loader
    toggleLoader(true);

    try {
        // Construire les données (JAMAIS les données brutes de carte au backend)
        const formData = {
            [document.querySelector('input[name="<?= CSRF_TOKEN_NAME ?>"]').name]:
                document.querySelector('input[name="<?= CSRF_TOKEN_NAME ?>"]').value,
            montant:            document.getElementById('montant').value,
            donateur_nom:       document.getElementById('donateur_nom').value.trim(),
            donateur_email:     document.getElementById('donateur_email').value.trim(),
            donateur_telephone: document.getElementById('donateur_telephone')?.value.trim() || '',
            donateur_pays:      document.getElementById('donateur_pays')?.value || 'Niger',
            message:            document.getElementById('message')?.value.trim() || '',
            anonyme:            document.getElementById('anonyme')?.checked ? 1 : 0,
            methode_paiement:   selectedMethod,
            projet_id:          document.getElementById('projet_id')?.value || '',

            // Pour carte : envoyer seulement les 4 derniers chiffres (PCI-DSS)
            // Les données sensibles ne doivent jamais quitter le navigateur en clair
            card_last4: selectedMethod === 'carte_bancaire'
                ? (document.getElementById('card_number')?.value.replace(/\s/g,'').slice(-4) || '')
                : '',
            card_type:  selectedMethod === 'carte_bancaire'
                ? detectCardType(document.getElementById('card_number')?.value.replace(/\s/g,'') || '')
                : '',
            om_phone:   document.getElementById('om_phone')?.value || '',
            mm_phone:   document.getElementById('mm_phone')?.value || '',
        };

        const resp = await fetch('<?= SITE_URL ?>/api/don', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData),
        });

        const data = await resp.json();

        if (data.success) {
            if (data.redirect_url) {
                window.location.href = data.redirect_url;
            } else {
                showSuccess(data.reference, data.montant);
            }
        } else {
            showError(data.message || 'Erreur lors du traitement. Veuillez réessayer.');
        }
    } catch (err) {
        showError('Erreur réseau. Vérifiez votre connexion et réessayez.');
    } finally {
        toggleLoader(false);
    }
});

function detectCardType(num) {
    if (/^4/.test(num))       return 'visa';
    if (/^5[1-5]/.test(num) || /^2[2-7]/.test(num)) return 'mastercard';
    if (/^3[47]/.test(num))   return 'amex';
    return 'unknown';
}

function toggleLoader(show) {
    const btn  = document.getElementById('btnSubmitDon');
    const txt  = document.getElementById('btnSubmitText');
    const load = document.getElementById('btnLoader');
    if (!btn) return;
    btn.disabled  = show;
    txt.style.display  = show ? 'none' : 'inline';
    load.style.display = show ? 'inline' : 'none';
}

function showSuccess(ref, montant) {
    document.getElementById('successRef').textContent     = ref;
    document.getElementById('successMontant').textContent = formatAmount(montant);
    document.getElementById('donSuccessModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

// ─── Helpers erreurs ─────────────────────────────────────────────────────────
function showError(msg) {
    const box = document.getElementById('formError');
    const msgEl = document.getElementById('formErrorMsg');
    if (box && msgEl) {
        msgEl.textContent = msg;
        box.style.display = 'flex';
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        alert(msg);
    }
}

function showFieldError(id, msg) {
    const el = document.getElementById(id);
    if (el) { el.textContent = msg; el.style.display = 'block'; }
}

function clearErrors(ids) {
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.textContent = ''; el.style.display = 'none'; }
    });
    const box = document.getElementById('formError');
    if (box) box.style.display = 'none';
}

// ─── Projet → affichage impact ───────────────────────────────────────────────
document.getElementById('projet_id')?.addEventListener('change', function () {
    // Mis à jour par buildRecap()
});

// ─── Stats live (dernier don) ─────────────────────────────────────────────────
async function fetchLiveDon() {
    try {
        const r = await fetch('<?= SITE_URL ?>/api/stats');
        const d = await r.json();
        if (d.success && d.dernier_don) {
            const card = document.getElementById('liveDonCard');
            const txt  = document.getElementById('liveDonText');
            if (card && txt) {
                txt.textContent = d.dernier_don.nom + ' a donné ' + d.dernier_don.montant + ' ' + d.dernier_don.date;
                card.style.display = 'block';
            }
        }
    } catch(e) {}
}
fetchLiveDon();
setInterval(fetchLiveDon, 30000);
</script>

<?php require_once '../includes/footer.php'; ?>
