<?php
$page_title = 'Accueil';
require_once 'includes/config.php';

// Récupérer les données
try {
    $pdo = getDB();
    
    // Projets en vedette
    $projets = $pdo->query("SELECT * FROM projets WHERE statut = 'actif' ORDER BY priorite ASC LIMIT 6")->fetchAll();
    
    // Statistiques temps réel depuis la base de données
    $total_donateurs = (int)$pdo->query("SELECT COUNT(DISTINCT donateur_email) FROM dons WHERE statut='complete'")->fetchColumn();
    $total_collecte  = (float)$pdo->query("SELECT COALESCE(SUM(montant),0) FROM dons WHERE statut='complete'")->fetchColumn();
    $total_projets   = (int)$pdo->query("SELECT COUNT(*) FROM projets WHERE statut='actif'")->fetchColumn();
    
    // Actualités
    $actualites = $pdo->query("SELECT * FROM actualites WHERE statut='publie' ORDER BY created_at DESC LIMIT 3")->fetchAll();
    
    // Témoignages
    $temoignages = $pdo->query("SELECT * FROM temoignages WHERE actif=1 ORDER BY id LIMIT 6")->fetchAll();
    
    // Partenaires
    $partenaires = $pdo->query("SELECT * FROM partenaires WHERE actif=1 ORDER BY ordre ASC")->fetchAll();

} catch (Exception $e) {
    $projets = []; $actualites = []; $temoignages = []; $partenaires = [];
    $total_donateurs = 0; $total_collecte = 0; $total_projets = 0;
}

require_once 'includes/header.php';
?>

<!-- LOADER -->
<div class="loader-overlay" id="loaderOverlay">
    <div class="loader">
        <img src="<?= SITE_URL ?>/assets/images/logo-faj.png" alt="FAJ">
        <p>Chargement...</p>
    </div>
</div>

<!-- ===== HERO SECTION ===== -->
<section class="hero">
    <!-- Background Slider -->
    <div class="hero-slider">
        <div class="hero-slide active">
            <img src="<?= SITE_URL ?>/assets/images/hero-collecte.jpg" alt="Collecte de fonds">
        </div>
        <div class="hero-slide">
            <img src="<?= SITE_URL ?>/assets/images/hero-croissance.jpg" alt="Croissance">
        </div>
        <div class="hero-slide">
            <img src="<?= SITE_URL ?>/assets/images/hero-billets.jpg" alt="Financement">
        </div>
    </div>
    <div class="hero-overlay"></div>
    
    <!-- Slider dots -->
    <div class="hero-controls">
        <span class="hero-dot active"></span>
        <span class="hero-dot"></span>
        <span class="hero-dot"></span>
    </div>
    
    <!-- Content -->
    <div class="hero-content container">
        <div class="hero-inner" data-aos="fade-right">
            <div class="hero-badge">
                <i class="fas fa-balance-scale"></i>
                <span>Fonds d'Appui à la Justice du Niger</span>
            </div>
            
            <h1 class="hero-title">
                <?= getSiteParam('hero_titre', 'Votre don peut <span>changer des vies</span>') ?>
            </h1>
            
            <p class="hero-desc">
                <?= getSiteParam('hero_sous_titre', 'Participez à la modernisation du système judiciaire du Niger. Ensemble, nous pouvons garantir une justice accessible, équitable et transparente pour tous.') ?>
            </p>
            
            <div class="hero-actions">
                <a href="<?= SITE_URL ?>/don" class="btn btn-primary btn-lg">
                    <i class="fas fa-heart"></i> Faire un Don Maintenant
                </a>
                <a href="#projets" class="btn btn-outline-white btn-lg">
                    <i class="fas fa-project-diagram"></i> Voir nos Projets
                </a>
            </div>
            
            <!-- Hero Stats (mis à jour dynamiquement via API) -->
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number live-counter" id="heroStatDonateurs"
                          data-value="<?= $total_donateurs ?>">0</span>
                    <span class="hero-stat-label">Donateurs</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number" id="heroStatCollecte"
                          data-value="<?= $total_collecte ?>">0</span>
                    <span class="hero-stat-label">FCFA Collectés</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number live-counter" id="heroStatProjets"
                          data-value="<?= $total_projets ?>">0</span>
                    <span class="hero-stat-label">Projets Actifs</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">8</span>
                    <span class="hero-stat-label">Régions couvertes</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== STATS SECTION ===== -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="0">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <span class="stat-number live-counter" id="bandeauDonateurs"
                      data-value="<?= $total_donateurs ?>">0</span>
                <span class="stat-label">Donateurs</span>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-icon"><i class="fas fa-hand-holding-heart"></i></div>
                <span class="stat-number" id="bandeauCollecte"
                      data-value="<?= $total_collecte ?>">0 FCFA</span>
                <span class="stat-label">FCFA Collectés</span>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-icon"><i class="fas fa-project-diagram"></i></div>
                <span class="stat-number live-counter" id="bandeauProjets"
                      data-value="<?= $total_projets ?>">0</span>
                <span class="stat-label">Projets Actifs</span>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-icon"><i class="fas fa-balance-scale"></i></div>
                <span class="stat-number" data-count="8">0</span>
                <span class="stat-label">Régions Couvertes</span>
            </div>
        </div>
    </div>
</section>

<!-- ===== À PROPOS ===== -->
<section class="about-section">
    <div class="container">
        <div class="about-grid">
            <!-- Image -->
            <div class="about-image" data-aos="fade-right">
                <div class="about-img-main">
                    <img src="<?= SITE_URL ?>/assets/images/hero-collecte.jpg" alt="À propos du FAJ">
                </div>
                <div class="about-img-badge">
                    <strong><?= date('Y') - 2023 ?>+</strong>
                    <span>Années d'engagement</span>
                </div>
            </div>
            
            <!-- Content -->
            <div class="about-content" data-aos="fade-left">
                <span class="section-tag">
                    <i class="fas fa-balance-scale"></i> À Propos du FAJ
                </span>
                <h2 class="section-title">
                    <?= getSiteParam('a_propos_titre', 'Pour une Justice <span>Accessible à Tous</span>') ?>
                </h2>
                <div class="section-text mb-4">
                    <?= getSiteParam('a_propos_texte', '<p>Le Fonds d\'Appui à la Justice (FAJ) est un mécanisme de financement innovant créé pour soutenir la modernisation et l\'amélioration du système judiciaire du Niger. Notre mission : garantir que la justice soit réellement indépendante, accessible et au service de tous les citoyens nigériens.</p><p>À l\'horizon 2035, notre ambition est d\'assurer un meilleur accès à la justice et un système carcéral modernisé à travers la mobilisation citoyenne et la collecte de fonds.</p>') ?>
                </div>
                
                <div class="about-features">
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="fas fa-building-columns"></i></div>
                        <div class="about-feature-text">
                            <h6>Infrastructures</h6>
                            <p>Construction et équipement de tribunaux</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="fas fa-graduation-cap"></i></div>
                        <div class="about-feature-text">
                            <h6>Formation</h6>
                            <p>Renforcement des capacités judiciaires</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="fas fa-hands-helping"></i></div>
                        <div class="about-feature-text">
                            <h6>Accès à la Justice</h6>
                            <p>Aide juridictionnelle pour les vulnérables</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature-icon"><i class="fas fa-laptop-code"></i></div>
                        <div class="about-feature-text">
                            <h6>Numérisation</h6>
                            <p>Modernisation digitale du système</p>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-3">
                    <a href="<?= SITE_URL ?>/a-propos" class="btn btn-secondary">
                        <i class="fas fa-info-circle"></i> En Savoir Plus
                    </a>
                    <a href="<?= SITE_URL ?>/don" class="btn btn-outline-primary">
                        <i class="fas fa-heart"></i> Faire un Don
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== MOT DU DIRECTEUR GÉNÉRAL ===== -->
<section class="dg-section" style="padding:80px 0; background:linear-gradient(135deg, #0a1628 0%, #1a2e50 50%, #0a1628 100%); position:relative; overflow:hidden;">

    <!-- Décoration de fond -->
    <div style="position:absolute;top:-60px;right:-60px;width:320px;height:320px;border-radius:50%;background:rgba(232,135,10,0.06);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-80px;left:-80px;width:400px;height:400px;border-radius:50%;background:rgba(232,135,10,0.04);pointer-events:none;"></div>
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:600px;height:600px;border-radius:50%;border:1px solid rgba(232,135,10,0.08);pointer-events:none;"></div>

    <div class="container" style="position:relative;z-index:2;">

        <!-- En-tête de section -->
        <div class="section-header centered" data-aos="fade-up" style="margin-bottom:56px;">
            <span class="section-tag" style="background:rgba(232,135,10,0.15);color:#E8870A;border:1px solid rgba(232,135,10,0.3);">
                <i class="fas fa-quote-left"></i>&nbsp; Mot du Directeur Général
            </span>
            <h2 class="section-title" style="color:white;">
                La Vision du <span style="color:#E8870A;">FAJ Niger</span>
            </h2>
            <p class="section-subtitle" style="color:rgba(255,255,255,0.65); max-width:600px; margin:0 auto;">
                Le message du Dr Idé Souleymane, Directeur Général du Fonds d'Appui à la Justice
            </p>
        </div>

        <!-- Carte principale DG -->
        <div data-aos="fade-up" data-aos-delay="100" style="max-width:1000px;margin:0 auto;background:rgba(255,255,255,0.04);border:1px solid rgba(232,135,10,0.2);border-radius:24px;overflow:hidden;backdrop-filter:blur(10px);">

            <div style="display:grid;grid-template-columns:300px 1fr;min-height:480px;">

                <!-- Colonne gauche — Photo + identité -->
                <div style="background:linear-gradient(180deg,rgba(232,135,10,0.18) 0%,rgba(10,22,40,0.9) 100%);padding:40px 32px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;border-right:1px solid rgba(232,135,10,0.15);">

                    <!-- Photo du DG -->
                    <div style="width:150px;height:150px;border-radius:50%;overflow:hidden;border:4px solid #E8870A;box-shadow:0 0 0 8px rgba(232,135,10,0.15);margin-bottom:24px;flex-shrink:0;">
                        <img src="<?= SITE_URL ?>/assets/images/photo-dg.jpg"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                             alt="Dr Idé Souleymane" style="width:100%;height:100%;object-fit:cover;object-position:center top;">
                        <div style="display:none;width:100%;height:100%;background:linear-gradient(135deg,#E8870A,#c96d00);align-items:center;justify-content:center;">
                            <i class="fas fa-user-tie" style="font-size:60px;color:white;opacity:0.8;"></i>
                        </div>
                    </div>

                    <!-- Logo FAJ -->
                    <div style="width:72px;height:72px;border-radius:50%;overflow:hidden;border:2px solid rgba(232,135,10,0.4);margin-bottom:20px;">
                        <img src="<?= SITE_URL ?>/assets/images/logo-faj.png" alt="FAJ" style="width:100%;height:100%;object-fit:contain;padding:6px;background:white;">
                    </div>

                    <h3 style="color:white;font-size:18px;font-weight:700;margin-bottom:6px;line-height:1.3;">Dr Idé Souleymane</h3>
                    <p style="color:#E8870A;font-size:13px;font-weight:600;margin-bottom:14px;text-transform:uppercase;letter-spacing:0.5px;">Directeur Général</p>
                    <p style="color:rgba(255,255,255,0.5);font-size:12px;font-style:italic;line-height:1.5;">Fonds d'Appui à la Justice — Niger</p>

                    <!-- Séparateur décoratif -->
                    <div style="width:40px;height:2px;background:#E8870A;margin:20px auto;border-radius:1px;"></div>

                    <!-- Slogan -->
                    <p style="color:rgba(255,255,255,0.6);font-size:12px;font-style:italic;line-height:1.6;text-align:center;">
                        « Le FAJ, l'assurance<br>d'une Justice moderne »
                    </p>
                </div>

                <!-- Colonne droite — Discours -->
                <div style="padding:48px 48px 40px;display:flex;flex-direction:column;justify-content:space-between;">

                    <!-- Guillemet décoratif -->
                    <div>
                        <div style="font-size:80px;line-height:1;color:rgba(232,135,10,0.2);font-family:Georgia,serif;margin-bottom:-20px;margin-left:-8px;">"</div>

                        <p style="color:rgba(255,255,255,0.85);font-size:15px;line-height:1.9;margin-bottom:20px;">
                            Le Fonds d'Appui à la Justice (FAJ) est un Fonds d'État créé par le décret N°2023-113/PRN/MJ du 26 janvier 2023. Il a pour mission de promouvoir l'investissement dans le domaine de la Justice à travers la mobilisation des fonds pour financer et soutenir les services judiciaires et pénitentiaires ainsi que l'assistance juridique et judiciaire pour un meilleur accès à la justice et la modernisation du système carcéral.
                        </p>

                        <!-- Missions en liste stylisée -->
                        <div style="margin-bottom:24px;">
                            <p style="color:rgba(255,255,255,0.5);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;">Nos missions spécifiques</p>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px 16px;">
                                <?php
                                $missions = [
                                    ['fas fa-building-columns', 'Construction & réhabilitation des juridictions'],
                                    ['fas fa-laptop-code',       'Informatisation du système judiciaire'],
                                    ['fas fa-prison',            'Équipement des établissements pénitentiaires'],
                                    ['fas fa-seedling',          'Réinsertion sociale des détenus'],
                                    ['fas fa-truck',             'Moyens logistiques des services judiciaires'],
                                    ['fas fa-hands-holding-child','Aide aux femmes, enfants et personnes indigentes'],
                                ];
                                foreach ($missions as [$icon, $label]):
                                ?>
                                <div style="display:flex;align-items:flex-start;gap:8px;">
                                    <i class="<?= $icon ?>" style="color:#E8870A;font-size:12px;margin-top:4px;flex-shrink:0;"></i>
                                    <span style="color:rgba(255,255,255,0.65);font-size:12px;line-height:1.5;"><?= $label ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Citation forte -->
                        <blockquote style="border-left:3px solid #E8870A;padding:14px 20px;background:rgba(232,135,10,0.07);border-radius:0 10px 10px 0;margin-bottom:24px;">
                            <p style="color:rgba(255,255,255,0.80);font-size:14px;font-style:italic;line-height:1.8;margin:0;">
                                « Notre ambition : qu'à l'horizon 2035, il y ait un meilleur accès à la Justice et un système carcéral modernisé. La Justice est rendue au nom du peuple, le peuple contribuera à sa modernisation. »
                            </p>
                        </blockquote>

                        <!-- Appel à l'action -->
                        <p style="color:#E8870A;font-weight:700;font-size:15px;font-style:italic;margin-bottom:28px;">
                            À l'unisson, nous pouvons relever tous les défis à travers nos contributions !
                        </p>
                    </div>

                    <!-- Footer carte : signature + boutons -->
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;padding-top:24px;border-top:1px solid rgba(255,255,255,0.08);">
                        <div>
                            <p style="color:rgba(255,255,255,0.4);font-size:11px;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Décret N°2023-113/PRN/MJ · 26 janv. 2023</p>
                            <p style="color:white;font-weight:700;font-size:14px;">Dr Idé Souleymane — Directeur Général, FAJ Niger</p>
                        </div>
                        <div style="display:flex;gap:12px;">
                            <a href="<?= SITE_URL ?>/a-propos" class="btn btn-outline" style="border-color:rgba(232,135,10,0.5);color:#E8870A;font-size:13px;padding:10px 20px;">
                                <i class="fas fa-info-circle"></i> En savoir plus
                            </a>
                            <a href="<?= SITE_URL ?>/don" class="btn btn-primary" style="font-size:13px;padding:10px 20px;">
                                <i class="fas fa-hand-holding-heart"></i> Contribuer
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<!-- ===== PROJETS ===== -->
<section class="projects-section" id="projets">
    <div class="container">
        <div class="section-header centered" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-project-diagram"></i> Nos Projets</span>
            <h2 class="section-title">Projets que <span>Nous Finançons</span></h2>
            <p class="section-subtitle">Découvrez nos programmes en cours pour améliorer le système judiciaire du Niger</p>
        </div>
        
        <div class="projects-grid">
            <?php if (empty($projets)): ?>
            <!-- Projets par défaut si BD vide -->
            <?php
            $projets_defaut = [
                ['titre' => 'Construction et Équipement de Tribunaux', 'description_courte' => 'Financement de la construction et de l\'équipement des tribunaux dans les régions du Niger', 'categorie' => 'infrastructure', 'objectif_montant' => 150000000, 'montant_collecte' => 45000000, 'image' => 'hero-collecte.jpg'],
                ['titre' => 'Formation des Acteurs Judiciaires', 'description_courte' => 'Renforcement des capacités des magistrats, avocats et auxiliaires de justice', 'categorie' => 'formation', 'objectif_montant' => 80000000, 'montant_collecte' => 20000000, 'image' => 'hero-croissance.jpg'],
                ['titre' => 'Humanisation du Milieu Carcéral', 'description_courte' => 'Amélioration des conditions de détention et réinsertion sociale des détenus', 'categorie' => 'humanisation', 'objectif_montant' => 100000000, 'montant_collecte' => 35000000, 'image' => 'hero-billets.jpg'],
                ['titre' => 'Accès à la Justice pour les Vulnérables', 'description_courte' => 'Aide juridictionnelle gratuite pour les personnes démunies', 'categorie' => 'acces_justice', 'objectif_montant' => 60000000, 'montant_collecte' => 18000000, 'image' => 'hero-collecte.jpg'],
                ['titre' => 'Numérisation du Système Judiciaire', 'description_courte' => 'Modernisation et digitalisation des archives et procédures judiciaires', 'categorie' => 'numerisation', 'objectif_montant' => 120000000, 'montant_collecte' => 30000000, 'image' => 'hero-croissance.jpg'],
                ['titre' => 'Aide aux Détenus en Attente de Jugement', 'description_courte' => 'Programme d\'assistance pour les personnes en détention préventive prolongée', 'categorie' => 'acces_justice', 'objectif_montant' => 40000000, 'montant_collecte' => 12000000, 'image' => 'hero-billets.jpg'],
            ];
            foreach ($projets_defaut as $p): 
                $pct = $p['objectif_montant'] > 0 ? min(100, round($p['montant_collecte']/$p['objectif_montant']*100)) : 0;
            ?>
            <div class="project-card" data-aos="fade-up">
                <div class="project-img">
                    <img src="<?= SITE_URL ?>/assets/images/<?= $p['image'] ?>" alt="<?= $p['titre'] ?>">
                    <span class="project-cat-badge"><?= ucfirst(str_replace('_', ' ', $p['categorie'])) ?></span>
                </div>
                <div class="project-body">
                    <h3 class="project-title"><?= $p['titre'] ?></h3>
                    <p class="project-desc"><?= $p['description_courte'] ?></p>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" data-width="<?= $pct ?>%" style="width: <?= $pct ?>%"></div>
                        </div>
                        <div class="progress-info">
                            <span class="progress-percent"><?= $pct ?>% financé</span>
                            <span class="progress-amount"><?= number_format($p['montant_collecte'], 0, ',', ' ') ?> / <?= number_format($p['objectif_montant'], 0, ',', ' ') ?> FCFA</span>
                        </div>
                    </div>
                    <div class="project-footer">
                        <span class="project-meta"><i class="fas fa-target"></i> Objectif : <?= number_format($p['objectif_montant'], 0, ',', ' ') ?> FCFA</span>
                        <a href="<?= SITE_URL ?>/don" class="btn btn-primary btn-sm">Soutenir <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <?php foreach ($projets as $projet):
                $pct = $projet['objectif_montant'] > 0 ? min(100, round($projet['montant_collecte']/$projet['objectif_montant']*100)) : 0;
            ?>
            <div class="project-card" data-aos="fade-up">
                <div class="project-img">
                    <?php if ($projet['image']): ?>
                    <img src="<?= UPLOADS_URL ?>/projets/<?= $projet['image'] ?>" alt="<?= $projet['titre'] ?>">
                    <?php else: ?>
                    <img src="<?= SITE_URL ?>/assets/images/hero-collecte.jpg" alt="<?= $projet['titre'] ?>">
                    <?php endif; ?>
                    <span class="project-cat-badge"><?= ucfirst(str_replace('_', ' ', $projet['categorie'])) ?></span>
                </div>
                <div class="project-body">
                    <h3 class="project-title"><?= sanitize($projet['titre']) ?></h3>
                    <p class="project-desc"><?= sanitize($projet['description_courte']) ?></p>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" data-width="<?= $pct ?>%" style="width: <?= $pct ?>%"></div>
                        </div>
                        <div class="progress-info">
                            <span class="progress-percent"><?= $pct ?>% financé</span>
                            <span class="progress-amount"><?= number_format($projet['montant_collecte'], 0, ',', ' ') ?> FCFA</span>
                        </div>
                    </div>
                    <div class="project-footer">
                        <span class="project-meta"><i class="fas fa-bullseye"></i> <?= number_format($projet['objectif_montant'], 0, ',', ' ') ?> FCFA</span>
                        <a href="<?= SITE_URL ?>/projets/<?= $projet['slug'] ?>" class="btn btn-primary btn-sm">Soutenir <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="<?= SITE_URL ?>/projets" class="btn btn-secondary">
                <i class="fas fa-th-large"></i> Voir tous les Projets
            </a>
        </div>
    </div>
</section>

<!-- ===== COMMENT ÇA MARCHE ===== -->
<section class="how-it-works">
    <div class="container">
        <div class="section-header centered" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-info-circle"></i> Comment ça marche</span>
            <h2 class="section-title">Faire un Don en <span>4 Étapes Simples</span></h2>
            <p class="section-subtitle">Un processus simple et sécurisé pour contribuer à la justice au Niger</p>
        </div>
        
        <div class="steps-grid">
            <div class="step-card" data-aos="fade-up" data-aos-delay="0">
                <div class="step-number">1</div>
                <h4 class="step-title">Choisissez un Projet</h4>
                <p class="step-desc">Parcourez nos projets et sélectionnez celui que vous souhaitez soutenir ou faites un don général.</p>
            </div>
            <div class="step-card" data-aos="fade-up" data-aos-delay="100">
                <div class="step-number">2</div>
                <h4 class="step-title">Définissez le Montant</h4>
                <p class="step-desc">Choisissez le montant de votre contribution. Chaque FCFA compte pour améliorer la justice.</p>
            </div>
            <div class="step-card" data-aos="fade-up" data-aos-delay="200">
                <div class="step-number">3</div>
                <h4 class="step-title">Mode de Paiement</h4>
                <p class="step-desc">Orange Money, Moov Money, Carte Visa/Mastercard — choisissez votre méthode préférée.</p>
            </div>
            <div class="step-card" data-aos="fade-up" data-aos-delay="300">
                <div class="step-number">4</div>
                <h4 class="step-title">Confirmez le Don</h4>
                <p class="step-desc">Recevez votre reçu par email et suivez l'impact de votre contribution sur nos projets.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== FAIRE UN DON ===== -->
<section class="don-section" id="don">
    <div class="container">
        <div class="section-header centered" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-heart"></i> Contribuer</span>
            <h2 class="section-title">Faites un <span style="color:var(--secondary-light)">Don Maintenant</span></h2>
            <p class="section-subtitle">Votre contribution finance directement les projets de modernisation de la justice au Niger</p>
        </div>
        
        <div class="don-form-container" data-aos="fade-up">
            <form id="donForm" method="POST">
                <input type="hidden" name="methode_paiement" id="methode_paiement" value="">
                
                <!-- Étape 1: Montant -->
                <div class="form-step">
                    <h3 class="form-step-title"><span class="step-badge">1</span> Choisissez un montant</h3>
                    
                    <div class="amount-presets">
                        <button type="button" class="amount-btn" data-amount="1000">1 000 FCFA</button>
                        <button type="button" class="amount-btn" data-amount="5000">5 000 FCFA</button>
                        <button type="button" class="amount-btn selected" data-amount="10000">10 000 FCFA</button>
                        <button type="button" class="amount-btn" data-amount="25000">25 000 FCFA</button>
                        <button type="button" class="amount-btn" data-amount="50000">50 000 FCFA</button>
                        <button type="button" class="amount-btn" data-amount="100000">100 000 FCFA</button>
                        <button type="button" class="amount-btn" data-amount="500000">500 000 FCFA</button>
                        <button type="button" class="amount-btn" data-amount="0">Autre</button>
                    </div>
                    
                    <div class="form-group">
                        <label for="montant">Montant personnalisé (FCFA) <span class="required">*</span></label>
                        <input type="number" id="montant" name="montant" class="form-control" 
                               placeholder="Ex: 15000" value="10000" min="500" required data-amount-format>
                    </div>
                </div>
                
                <hr style="margin: 30px 0; border-color: var(--light-gray);">
                
                <!-- Étape 2: Informations -->
                <div class="form-step">
                    <h3 class="form-step-title"><span class="step-badge">2</span> Vos informations</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="donateur_nom">Nom complet <span class="required">*</span></label>
                            <input type="text" id="donateur_nom" name="donateur_nom" class="form-control" 
                                   placeholder="Votre nom" required>
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
                            <label for="donateur_pays">Pays</label>
                            <select id="donateur_pays" name="donateur_pays" class="form-control">
                                <option value="Niger" selected>Niger</option>
                                <option value="Sénégal">Sénégal</option>
                                <option value="Mali">Mali</option>
                                <option value="Burkina Faso">Burkina Faso</option>
                                <option value="Côte d'Ivoire">Côte d'Ivoire</option>
                                <option value="France">France</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message (optionnel)</label>
                        <textarea id="message" name="message" class="form-control" rows="2" 
                                  placeholder="Votre message de soutien..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="anonyme" id="anonyme"> 
                            Faire ce don anonymement
                        </label>
                    </div>
                </div>
                
                <hr style="margin: 30px 0; border-color: var(--light-gray);">
                
                <!-- Étape 3: Moyen de paiement -->
                <div class="form-step">
                    <h3 class="form-step-title"><span class="step-badge">3</span> Mode de paiement</h3>
                    
                    <div class="payment-methods-grid">
                        <button type="button" class="payment-method-btn orange" data-method="orange_money">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Orange Money</span>
                        </button>
                        <button type="button" class="payment-method-btn moov" data-method="moov_money">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Moov Money</span>
                        </button>
                        <button type="button" class="payment-method-btn visa" data-method="carte_visa">
                            <i class="fab fa-cc-visa"></i>
                            <span>Carte Visa</span>
                        </button>
                        <button type="button" class="payment-method-btn mastercard" data-method="carte_mastercard">
                            <i class="fab fa-cc-mastercard"></i>
                            <span>Mastercard</span>
                        </button>
                    </div>
                    
                    <!-- Panneaux de détail paiement -->
                    <div id="panel-orange_money" class="payment-detail-panel" style="display:none; margin-top:20px;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Vous serez redirigé vers la page Orange Money pour finaliser votre paiement sécurisé.</span>
                        </div>
                    </div>
                    
                    <div id="panel-moov_money" class="payment-detail-panel" style="display:none; margin-top:20px;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Vous serez redirigé vers la page Moov Money pour finaliser votre paiement sécurisé.</span>
                        </div>
                    </div>
                    
                    <div id="panel-carte_visa" class="payment-detail-panel" style="display:none; margin-top:20px;">
                        <div class="alert alert-info">
                            <i class="fas fa-lock"></i>
                            <span>Paiement sécurisé SSL. Vous serez redirigé vers notre plateforme de paiement sécurisée.</span>
                        </div>
                    </div>
                    
                    <div id="panel-carte_mastercard" class="payment-detail-panel" style="display:none; margin-top:20px;">
                        <div class="alert alert-info">
                            <i class="fas fa-lock"></i>
                            <span>Paiement sécurisé SSL via Mastercard. Redirection vers la plateforme sécurisée.</span>
                        </div>
                    </div>
                </div>
                
                <hr style="margin: 30px 0; border-color: var(--light-gray);">
                
                <!-- Résumé -->
                <div class="don-summary">
                    <div class="summary-row">
                        <span>Montant du don :</span>
                        <strong id="summaryMontant" class="text-secondary">10 000 FCFA</strong>
                    </div>
                    <div class="summary-row">
                        <span>Mode de paiement :</span>
                        <span id="summaryMethode" class="text-gray">—</span>
                    </div>
                </div>
                
                <div class="form-group" style="margin-top:24px;">
                    <label class="checkbox-label" style="font-size:13px; color: var(--gray);">
                        <input type="checkbox" required>
                        J'accepte les <a href="<?= SITE_URL ?>/conditions-generales" style="color:var(--secondary);">conditions générales</a> et la <a href="<?= SITE_URL ?>/politique-confidentialite" style="color:var(--secondary);">politique de confidentialité</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg" style="width:100%; justify-content:center; font-size:17px; padding:18px;">
                    <i class="fas fa-lock"></i> Valider Mon Don
                </button>
                
                <p class="text-center text-gray" style="font-size:13px; margin-top:16px;">
                    <i class="fas fa-shield-alt" style="color:var(--secondary);"></i>
                    Paiement 100% sécurisé · Reçu envoyé par email
                </p>
            </form>
        </div>
    </div>
</section>

<!-- ===== TÉMOIGNAGES ===== -->
<?php if (!empty($temoignages)): ?>
<section class="testimonials-section">
    <div class="container">
        <div class="section-header centered" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-quote-left"></i> Témoignages</span>
            <h2 class="section-title">Ce que disent <span>nos Donateurs</span></h2>
        </div>
        
        <div class="swiper testimonials-slider">
            <div class="swiper-wrapper">
                <?php foreach ($temoignages as $t): ?>
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <?php for($i=0; $i<$t['note']; $i++): ?><i class="fas fa-star"></i><?php endfor; ?>
                        </div>
                        <p class="testimonial-text"><?= sanitize($t['contenu']) ?></p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar">
                                <?php if ($t['photo']): ?>
                                <img src="<?= UPLOADS_URL ?>/team/<?= $t['photo'] ?>" alt="<?= $t['nom'] ?>">
                                <?php else: ?>
                                <?= strtoupper(substr($t['nom'], 0, 1)) ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="testimonial-name"><?= sanitize($t['nom']) ?></div>
                                <div class="testimonial-role"><?= sanitize($t['poste']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== ACTUALITÉS ===== -->
<?php if (!empty($actualites)): ?>
<section class="news-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="d-flex justify-between align-center">
                <div>
                    <span class="section-tag"><i class="fas fa-newspaper"></i> Actualités</span>
                    <h2 class="section-title">Dernières <span>Nouvelles</span></h2>
                </div>
                <a href="<?= SITE_URL ?>/actualites" class="btn btn-outline-primary">Toutes les actualités</a>
            </div>
        </div>
        
        <div class="news-grid">
            <?php foreach ($actualites as $actu): ?>
            <article class="news-card" data-aos="fade-up">
                <div class="news-img">
                    <?php if ($actu['image']): ?>
                    <img src="<?= UPLOADS_URL ?>/actualites/<?= $actu['image'] ?>" alt="<?= $actu['titre'] ?>">
                    <?php else: ?>
                    <img src="<?= SITE_URL ?>/assets/images/hero-collecte.jpg" alt="<?= $actu['titre'] ?>">
                    <?php endif; ?>
                </div>
                <div class="news-body">
                    <span class="news-cat"><?= $actu['categorie'] ?? 'Actualité' ?></span>
                    <div class="news-meta">
                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($actu['created_at'])) ?></span>
                    </div>
                    <h3 class="news-title">
                        <a href="<?= SITE_URL ?>/actualites/<?= $actu['slug'] ?>">
                            <?= sanitize($actu['titre']) ?>
                        </a>
                    </h3>
                    <p class="news-excerpt"><?= sanitize($actu['extrait'] ?? '') ?></p>
                    <a href="<?= SITE_URL ?>/actualites/<?= $actu['slug'] ?>" class="read-more">
                        Lire la suite <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===== CTA BAND ===== -->
<section class="cta-band">
    <div class="container" data-aos="fade-up">
        <h2>Rejoignez le mouvement pour une Justice <br>au service du peuple nigérien</h2>
        <p>Chaque contribution, petite ou grande, contribue à construire un système judiciaire meilleur pour le Niger.</p>
        <div class="d-flex justify-center gap-3">
            <a href="<?= SITE_URL ?>/don" class="btn btn-white btn-lg">
                <i class="fas fa-heart"></i> Faire un Don
            </a>
            <a href="<?= SITE_URL ?>/contact" class="btn btn-outline-white btn-lg">
                <i class="fas fa-envelope"></i> Nous Contacter
            </a>
        </div>
    </div>
</section>

<!-- ===== PARTENAIRES ===== -->
<section class="partners-section">
    <div class="container">
        <div class="section-header centered" data-aos="fade-up">
            <span class="section-tag"><i class="fas fa-handshake"></i> Partenaires</span>
            <h2 class="section-title">Nos <span>Partenaires</span></h2>
        </div>
        
        <div class="partners-track">
            <?php if (!empty($partenaires)): ?>
            <?php foreach ($partenaires as $p): ?>
            <div class="partner-item" data-aos="fade-up">
                <img src="<?= UPLOADS_URL ?>/partenaires/<?= $p['logo'] ?>" alt="<?= $p['nom'] ?>">
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <!-- Partenaires par défaut -->
            <?php 
            $partenaires_defaut = ['Ministère de la Justice', 'PNUD Niger', 'Union Européenne', 'Banque Mondiale', 'ONU Droits de l\'Homme'];
            foreach ($partenaires_defaut as $p): ?>
            <div class="partner-item" data-aos="fade-up">
                <div style="background:var(--light); padding:15px 25px; border-radius:8px; font-weight:700; color:var(--primary); font-size:13px; text-align:center; min-width:150px;">
                    <?= $p ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Modal succès don -->
<div class="modal-overlay" id="donSuccessModal">
    <div class="modal">
        <div class="modal-icon"><i class="fas fa-check"></i></div>
        <h3>Don Confirmé !</h3>
        <p>Merci pour votre généreux don. Votre référence : <strong id="successRef"></strong><br>
           Montant : <strong id="successMontant"></strong><br>
           Un reçu vous a été envoyé par email.</p>
        <a href="<?= SITE_URL ?>" class="btn btn-primary" data-modal-close>
            <i class="fas fa-home"></i> Retour à l'Accueil
        </a>
    </div>
</div>

<style>
.form-step-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.step-badge {
    width: 32px;
    height: 32px;
    background: var(--secondary);
    color: white;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
}
.don-summary {
    background: var(--light);
    border-radius: var(--radius);
    padding: 20px;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--light-gray);
    font-size: 15px;
}
.summary-row:last-child { border-bottom: none; }
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
    color: var(--gray);
}
.checkbox-label input {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--secondary);
}

/* Swiper custom */
.swiper-pagination-bullet-active {
    background: var(--secondary) !important;
}

/* Mobile overlay */
@media (max-width: 768px) {
    body.menu-open::after {
        content: '';
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 998;
    }
}
</style>

<!-- Script de mise à jour dynamique des compteurs -->
<script>
// ═══════════════════════════════════════════════════
//  COMPTEURS DYNAMIQUES – Mise à jour temps réel
// ═══════════════════════════════════════════════════

const STATS_API = '<?= SITE_URL ?>/api/stats';
const REFRESH_INTERVAL = 30000; // 30 secondes

/**
 * Animation de compteur (0 → valeur cible)
 */
function animateCounter(el, targetValue, duration = 1800) {
    if (!el) return;
    const start = performance.now();
    const initial = parseInt(el.textContent.replace(/\D/g,'')) || 0;
    const diff = targetValue - initial;
    if (diff === 0) return;

    function step(now) {
        const elapsed  = now - start;
        const progress = Math.min(elapsed / duration, 1);
        // Easing ease-out
        const ease = 1 - Math.pow(1 - progress, 3);
        const current = Math.round(initial + diff * ease);
        el.textContent = current.toLocaleString('fr-FR');
        if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

/**
 * Formater un montant en FCFA (compact : 1 500 000 → 1,5M)
 */
function formatMontantCompact(n) {
    if (n >= 1000000000) return (n / 1000000000).toFixed(1).replace('.', ',') + ' Md FCFA';
    if (n >= 1000000)    return (n / 1000000).toFixed(1).replace('.', ',') + 'M FCFA';
    if (n >= 1000)       return (n / 1000).toFixed(0) + ' K FCFA';
    return n.toLocaleString('fr-FR') + ' FCFA';
}

/**
 * Mettre à jour les compteurs depuis l'API
 */
async function updateStats() {
    try {
        const resp = await fetch(STATS_API + '?t=' + Date.now());
        if (!resp.ok) return;
        const data = await resp.json();
        if (!data.success) return;

        // ── Hero stats ──────────────────────────────────────────────────
        const heroDon = document.getElementById('heroStatDonateurs');
        const heroColl = document.getElementById('heroStatCollecte');
        const heroProj = document.getElementById('heroStatProjets');

        if (heroDon)  animateCounter(heroDon,  data.total_donateurs);
        if (heroProj) animateCounter(heroProj, data.total_projets);
        if (heroColl) heroColl.textContent = formatMontantCompact(data.total_collecte);

        // ── Bandeau statistiques ────────────────────────────────────────
        const banDon  = document.getElementById('bandeauDonateurs');
        const banColl = document.getElementById('bandeauCollecte');
        const banProj = document.getElementById('bandeauProjets');

        if (banDon)  animateCounter(banDon,  data.total_donateurs);
        if (banProj) animateCounter(banProj, data.total_projets);
        if (banColl) banColl.textContent = formatMontantCompact(data.total_collecte);

        // ── Dernier don (notification live) ────────────────────────────
        if (data.dernier_don) {
            showLiveDon(data.dernier_don);
        }

    } catch (e) {
        // Silencieux en production
    }
}

/**
 * Afficher une notification "dernier don" en coin de page
 */
function showLiveDon(don) {
    let toast = document.getElementById('liveDonToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'liveDonToast';
        toast.style.cssText = [
            'position:fixed','bottom:90px','right:20px','z-index:9999',
            'background:white','border-radius:12px',
            'box-shadow:0 8px 30px rgba(0,0,0,.18)',
            'padding:14px 18px','max-width:280px',
            'border-left:4px solid #22c55e',
            'display:flex','align-items:center','gap:12px',
            'animation:slideInRight .4s ease',
            'font-family:inherit'
        ].join(';');
        document.body.appendChild(toast);

        // Injecter keyframe si nécessaire
        if (!document.getElementById('liveToastStyle')) {
            const s = document.createElement('style');
            s.id = 'liveToastStyle';
            s.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(120%); opacity:0; }
                    to   { transform: translateX(0);    opacity:1; }
                }
            `;
            document.head.appendChild(s);
        }
    }
    toast.innerHTML = `
        <div style="width:38px;height:38px;background:#dcfce7;border-radius:50%;
                    display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-heart" style="color:#16a34a;font-size:16px;"></i>
        </div>
        <div>
            <p style="margin:0;font-size:12px;font-weight:700;color:#166534;">Don récent</p>
            <p style="margin:0;font-size:13px;color:#374151;">
                <strong>${don.nom}</strong> · <span style="color:#16a34a;font-weight:700;">${don.montant}</span>
            </p>
            <p style="margin:0;font-size:11px;color:#9ca3af;">${don.date}</p>
        </div>
    `;
    toast.style.display = 'flex';

    // Masquer après 8 secondes
    clearTimeout(window._donToastTimer);
    window._donToastTimer = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity .5s';
        setTimeout(() => toast.style.display = 'none', 500);
    }, 8000);
}

// ── Initialisation ──────────────────────────────────────────────────────────
// Animer les valeurs initiales (chargées côté PHP)
document.addEventListener('DOMContentLoaded', () => {
    const initCounters = [
        ['heroStatDonateurs', parseInt('<?= $total_donateurs ?>')],
        ['heroStatProjets',   parseInt('<?= $total_projets ?>')],
        ['bandeauDonateurs',  parseInt('<?= $total_donateurs ?>')],
        ['bandeauProjets',    parseInt('<?= $total_projets ?>')],
    ];
    initCounters.forEach(([id, val]) => {
        const el = document.getElementById(id);
        if (el) animateCounter(el, val);
    });

    // Montant collecté formaté
    const collecte = <?= $total_collecte ?>;
    ['heroStatCollecte','bandeauCollecte'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.textContent = formatMontantCompact(collecte);
    });

    // Rafraîchir toutes les 30 secondes
    setInterval(updateStats, REFRESH_INTERVAL);

    // Premier appel API après 5 secondes
    setTimeout(updateStats, 5000);
});
</script>

<?php require_once 'includes/footer.php'; ?>

