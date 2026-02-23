-- ============================================================
-- Base de données FAJ - Fonds d'Appui à la Justice du Niger
-- ============================================================

CREATE DATABASE IF NOT EXISTS `faj_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `faj_db`;

-- Table des administrateurs
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) UNIQUE NOT NULL,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `role` ENUM('super_admin','admin','editeur') DEFAULT 'admin',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `actif` TINYINT(1) DEFAULT 1,
  `derniere_connexion` DATETIME DEFAULT NULL,
  `token_reset` VARCHAR(64) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des paramètres du site
CREATE TABLE IF NOT EXISTS `parametres` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cle` VARCHAR(100) UNIQUE NOT NULL,
  `valeur` TEXT,
  `type` ENUM('texte','nombre','image','booleen','html') DEFAULT 'texte',
  `groupe` VARCHAR(50) DEFAULT 'general',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des projets/programmes
CREATE TABLE IF NOT EXISTS `projets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titre` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `description_courte` TEXT,
  `description_longue` LONGTEXT,
  `categorie` ENUM('infrastructure','formation','humanisation','acces_justice','numerisation','autre') DEFAULT 'autre',
  `objectif_montant` DECIMAL(15,2) DEFAULT 0,
  `montant_collecte` DECIMAL(15,2) DEFAULT 0,
  `image` VARCHAR(255) DEFAULT NULL,
  `images_galerie` JSON DEFAULT NULL,
  `statut` ENUM('actif','termine','en_pause','brouillon') DEFAULT 'actif',
  `priorite` INT DEFAULT 0,
  `date_debut` DATE DEFAULT NULL,
  `date_fin` DATE DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des dons
CREATE TABLE IF NOT EXISTS `dons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `reference` VARCHAR(50) UNIQUE NOT NULL,
  `donateur_nom` VARCHAR(150) NOT NULL,
  `donateur_email` VARCHAR(150) NOT NULL,
  `donateur_telephone` VARCHAR(20) DEFAULT NULL,
  `donateur_pays` VARCHAR(100) DEFAULT 'Niger',
  `montant` DECIMAL(15,2) NOT NULL,
  `devise` VARCHAR(10) DEFAULT 'XOF',
  `methode_paiement` ENUM('orange_money','moov_money','carte_visa','carte_mastercard','virement','autre') NOT NULL,
  `transaction_id` VARCHAR(255) DEFAULT NULL,
  `statut` ENUM('en_attente','complete','echoue','rembourse') DEFAULT 'en_attente',
  `projet_id` INT DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `anonyme` TINYINT(1) DEFAULT 0,
  `recu_envoye` TINYINT(1) DEFAULT 0,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`projet_id`) REFERENCES `projets`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table des actualités
CREATE TABLE IF NOT EXISTS `actualites` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titre` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `contenu` LONGTEXT,
  `extrait` TEXT,
  `image` VARCHAR(255) DEFAULT NULL,
  `categorie` VARCHAR(100) DEFAULT NULL,
  `tags` JSON DEFAULT NULL,
  `statut` ENUM('publie','brouillon') DEFAULT 'publie',
  `en_vedette` TINYINT(1) DEFAULT 0,
  `nb_vues` INT DEFAULT 0,
  `admin_id` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`admin_id`) REFERENCES `admins`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table des membres de l'équipe
CREATE TABLE IF NOT EXISTS `equipe` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(150) NOT NULL,
  `poste` VARCHAR(150) NOT NULL,
  `biographie` TEXT DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `linkedin` VARCHAR(255) DEFAULT NULL,
  `ordre` INT DEFAULT 0,
  `actif` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des partenaires
CREATE TABLE IF NOT EXISTS `partenaires` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(150) NOT NULL,
  `logo` VARCHAR(255) NOT NULL,
  `site_web` VARCHAR(255) DEFAULT NULL,
  `type` ENUM('institutionnel','financier','technique','media') DEFAULT 'institutionnel',
  `ordre` INT DEFAULT 0,
  `actif` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des témoignages
CREATE TABLE IF NOT EXISTS `temoignages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(150) NOT NULL,
  `poste` VARCHAR(150) DEFAULT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `contenu` TEXT NOT NULL,
  `note` INT DEFAULT 5,
  `actif` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des messages de contact
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `telephone` VARCHAR(20) DEFAULT NULL,
  `sujet` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `lu` TINYINT(1) DEFAULT 0,
  `repondu` TINYINT(1) DEFAULT 0,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table newsletter
CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) UNIQUE NOT NULL,
  `nom` VARCHAR(150) DEFAULT NULL,
  `actif` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des statistiques
CREATE TABLE IF NOT EXISTS `statistiques` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cle` VARCHAR(100) UNIQUE NOT NULL,
  `valeur` BIGINT DEFAULT 0,
  `label` VARCHAR(150) DEFAULT NULL,
  `icone` VARCHAR(50) DEFAULT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table pages statiques
CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titre` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `contenu` LONGTEXT,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `statut` ENUM('publie','brouillon') DEFAULT 'publie',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DONNÉES INITIALES
-- ============================================================

-- Admin par défaut (mot de passe: Admin@FAJ2024)
INSERT INTO `admins` (`nom`, `email`, `mot_de_passe`, `role`) VALUES 
('Super Administrateur', 'admin@faj.ne', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- Paramètres par défaut
INSERT INTO `parametres` (`cle`, `valeur`, `type`, `groupe`) VALUES
('site_nom', 'Fonds d\'Appui à la Justice', 'texte', 'general'),
('site_slogan', 'Ensemble pour une Justice accessible à tous', 'texte', 'general'),
('site_description', 'Le FAJ collecte des fonds pour moderniser et améliorer le système judiciaire du Niger', 'texte', 'general'),
('site_email', 'contact@faj.ne', 'texte', 'contact'),
('site_telephone', '+227 20 XX XX XX', 'texte', 'contact'),
('site_adresse', 'Niamey, Niger', 'texte', 'contact'),
('site_facebook', 'https://facebook.com/fajniger', 'texte', 'reseaux'),
('site_twitter', 'https://twitter.com/fajniger', 'texte', 'reseaux'),
('site_linkedin', 'https://linkedin.com/company/fajniger', 'texte', 'reseaux'),
('site_youtube', '', 'texte', 'reseaux'),
('hero_titre', 'Votre don peut changer des vies', 'texte', 'accueil'),
('hero_sous_titre', 'Participez à la modernisation du système judiciaire du Niger', 'texte', 'accueil'),
('a_propos_titre', 'À Propos du FAJ', 'texte', 'a_propos'),
('a_propos_texte', 'Le Fonds d\'Appui à la Justice (FAJ) est un mécanisme de financement créé pour soutenir la modernisation et l\'amélioration du système judiciaire du Niger.', 'html', 'a_propos'),
('objectif_global', '500000000', 'nombre', 'statistiques'),
('total_collecte', '0', 'nombre', 'statistiques');

-- Statistiques
INSERT INTO `statistiques` (`cle`, `valeur`, `label`, `icone`) VALUES
('total_donateurs', 0, 'Donateurs', 'fas fa-users'),
('total_dons', 0, 'Dons collectés (FCFA)', 'fas fa-hand-holding-heart'),
('total_projets', 0, 'Projets financés', 'fas fa-project-diagram'),
('total_beneficiaires', 0, 'Bénéficiaires', 'fas fa-balance-scale');

-- Projets exemples
INSERT INTO `projets` (`titre`, `slug`, `description_courte`, `description_longue`, `categorie`, `objectif_montant`, `statut`, `priorite`) VALUES
('Construction et équipement de Tribunaux', 'construction-equipement-tribunaux', 
 'Financement de la construction et de l\'équipement des tribunaux dans les régions du Niger',
 '<p>Ce projet vise à améliorer l\'infrastructure judiciaire du Niger en construisant et équipant des tribunaux modernes dans toutes les régions du pays. L\'accès à la justice est un droit fondamental, et des infrastructures appropriées sont essentielles pour son exercice effectif.</p><p>Le projet comprend la construction de salles d\'audience, de bureaux pour les juges et greffiers, de salles d\'attente pour les justiciables, et l\'équipement en matériel informatique et mobilier de bureau.</p>',
 'infrastructure', 150000000, 'actif', 1),

('Programme de Formation des Acteurs Judiciaires', 'formation-acteurs-judiciaires',
 'Renforcement des capacités des magistrats, avocats et auxiliaires de justice',
 '<p>La formation continue des acteurs du système judiciaire est cruciale pour garantir une justice de qualité. Ce programme finance des sessions de formation pour les magistrats, avocats, huissiers, notaires et autres auxiliaires de justice.</p><p>Les formations couvrent les nouvelles législations, les procédures modernes, et les meilleures pratiques en matière de justice.</p>',
 'formation', 80000000, 'actif', 2),

('Humanisation du Milieu Carcéral', 'humanisation-milieu-carceral',
 'Amélioration des conditions de détention et réinsertion sociale des détenus',
 '<p>Ce projet s\'attaque aux conditions de détention dans les établissements pénitentiaires du Niger. Il finance l\'amélioration des infrastructures sanitaires, l\'accès aux soins médicaux, et les programmes de réinsertion sociale et professionnelle pour les détenus.</p>',
 'humanisation', 100000000, 'actif', 3),

('Accès à la Justice pour les Populations Vulnérables', 'acces-justice-vulnerables',
 'Aide juridictionnelle gratuite pour les personnes démunies et les femmes victimes de violence',
 '<p>Beaucoup de Nigériens n\'ont pas les moyens de se payer les services d\'un avocat. Ce projet finance l\'aide juridictionnelle gratuite pour les personnes sans ressources, notamment les femmes victimes de violence, les enfants en difficulté et les personnes âgées.</p>',
 'acces_justice', 60000000, 'actif', 4),

('Numérisation du Système Judiciaire', 'numerisation-systeme-judiciaire',
 'Modernisation et digitalisation des archives et procédures judiciaires',
 '<p>La numérisation du système judiciaire permettra d\'améliorer l\'efficacité des tribunaux, de réduire les délais de traitement des dossiers et d\'améliorer la transparence. Ce projet finance l\'acquisition de matériel informatique et le développement de logiciels spécialisés.</p>',
 'numerisation', 120000000, 'actif', 5);

-- Équipe exemple  
INSERT INTO `equipe` (`nom`, `poste`, `biographie`, `ordre`) VALUES
('M. Aboubacar MAHAMADOU', 'Président du Conseil d\'Administration', 'Magistrat de haut rang avec plus de 25 ans d\'expérience dans le système judiciaire nigérien.', 1),
('Mme Fatouma IBRAHIM', 'Directrice Générale', 'Juriste et gestionnaire expérimentée, ancienne conseillère au Ministère de la Justice.', 2),
('M. Moussa SANI', 'Directeur Financier', 'Expert financier spécialisé dans la gestion des fonds publics et privés.', 3),
('Mme Aïcha OUMAROU', 'Responsable Programmes', 'Spécialiste en développement avec une expertise en droits humains et accès à la justice.', 4);

-- Partenaires exemple
INSERT INTO `partenaires` (`nom`, `logo`, `type`, `ordre`) VALUES
('Ministère de la Justice du Niger', 'partenaire-1.png', 'institutionnel', 1),
('Programme des Nations Unies pour le Développement', 'partenaire-2.png', 'institutionnel', 2),
('Union Européenne', 'partenaire-3.png', 'financier', 3),
('Banque Mondiale', 'partenaire-4.png', 'financier', 4);

-- Témoignages
INSERT INTO `temoignages` (`nom`, `poste`, `contenu`, `note`) VALUES
('Alhaji Moussa', 'Commerçant, Niamey', 'Grâce au FAJ, j\'ai pu avoir accès à l\'aide juridictionnelle et résoudre mon litige commercial. Une initiative vraiment importante pour nous.', 5),
('Mme Mariama', 'Enseignante, Zinder', 'Le FAJ travaille pour que la justice ne soit plus un luxe réservé aux riches. Je soutiens cette cause de tout cœur.', 5),
('Dr. Ibrahim', 'Médecin, Agadez', 'La modernisation du système judiciaire est essentielle pour le développement du Niger. Le FAJ fait un travail remarquable.', 5);

-- Pages statiques
INSERT INTO `pages` (`titre`, `slug`, `contenu`) VALUES
('Mentions Légales', 'mentions-legales', '<h2>Mentions Légales</h2><p>Le site faj.ne est édité par le Fonds d\'Appui à la Justice du Niger (FAJ), établissement public.</p>'),
('Politique de Confidentialité', 'politique-confidentialite', '<h2>Politique de Confidentialité</h2><p>Nous nous engageons à protéger vos données personnelles conformément aux lois en vigueur au Niger.</p>'),
('Conditions Générales', 'conditions-generales', '<h2>Conditions Générales de Don</h2><p>En effectuant un don au FAJ, vous acceptez les présentes conditions générales.</p>');
