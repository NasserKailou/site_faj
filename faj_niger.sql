-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 08 avr. 2026 à 12:47
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `faj_niger`
--

-- --------------------------------------------------------

--
-- Structure de la table `actualites`
--

CREATE TABLE `actualites` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `contenu` longtext DEFAULT NULL,
  `extrait` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `statut` enum('publie','brouillon') DEFAULT 'publie',
  `en_vedette` tinyint(1) DEFAULT 0,
  `nb_vues` int(11) DEFAULT 0,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `actualites`
--

INSERT INTO `actualites` (`id`, `titre`, `slug`, `contenu`, `extrait`, `image`, `categorie`, `tags`, `statut`, `en_vedette`, `nb_vues`, `admin_id`, `created_at`, `updated_at`) VALUES
(1, 'TEST ACTU FAJ', 'test-actu-faj', 'rehzzzzzzzzzzzzzzzzzzzzzzzzzzbfffgsgdcbsbsbesbhegergre', 'zgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzgzg', 'actu_1772034821.png', '', NULL, 'publie', 0, 2, 1, '2026-02-25 16:53:41', '2026-02-25 17:16:51');

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','editeur') DEFAULT 'admin',
  `avatar` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `derniere_connexion` datetime DEFAULT NULL,
  `token_reset` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `avatar`, `actif`, `derniere_connexion`, `token_reset`, `created_at`) VALUES
(1, 'Super Administrateur', 'admin@faj.ne', '$2y$12$LjpqZHL7iq1U7SdycSbUXO7FwI4ATM3gWCp29eNJC9pLRrkSbJoQy', 'super_admin', NULL, 1, '2026-02-26 09:35:50', NULL, '2026-02-24 17:37:39');

-- --------------------------------------------------------

--
-- Structure de la table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `sujet` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `repondu` tinyint(1) DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dons`
--

CREATE TABLE `dons` (
  `id` int(11) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `donateur_nom` varchar(150) NOT NULL,
  `donateur_email` varchar(150) NOT NULL,
  `donateur_telephone` varchar(20) DEFAULT NULL,
  `donateur_pays` varchar(100) DEFAULT 'Niger',
  `montant` decimal(15,2) NOT NULL,
  `devise` varchar(10) DEFAULT 'XOF',
  `methode_paiement` enum('orange_money','moov_money','carte_visa','carte_mastercard','virement','autre') NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `statut` enum('en_attente','complete','echoue','rembourse') DEFAULT 'en_attente',
  `projet_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `anonyme` tinyint(1) DEFAULT 0,
  `recu_envoye` tinyint(1) DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `equipe`
--

CREATE TABLE `equipe` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `poste` varchar(150) NOT NULL,
  `biographie` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT 0,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `equipe`
--

INSERT INTO `equipe` (`id`, `nom`, `poste`, `biographie`, `photo`, `email`, `linkedin`, `ordre`, `actif`, `created_at`) VALUES
(1, 'M. Aboubacar MAHAMADOU', 'Président du Conseil d\'Administration', 'Magistrat de haut rang avec plus de 25 ans d\'expérience dans le système judiciaire nigérien.', NULL, NULL, NULL, 1, 1, '2026-02-24 17:37:39'),
(2, 'Dr. Idé Souleymane', 'Directeur Général', 'Juriste et gestionnaire expérimentée, ancienne conseillère au Ministère de la Justice.', NULL, '', '', 1, 1, '2026-02-24 17:37:39'),
(3, 'M. Moussa SANI', 'Directeur Financier', 'Expert financier spécialisé dans la gestion des fonds publics et privés.', NULL, NULL, NULL, 3, 1, '2026-02-24 17:37:39'),
(4, 'Mme Aïcha OUMAROU', 'Responsable Programmes', 'Spécialiste en développement avec une expertise en droits humains et accès à la justice.', NULL, NULL, NULL, 4, 1, '2026-02-24 17:37:39');

-- --------------------------------------------------------

--
-- Structure de la table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `nom` varchar(150) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `contenu` longtext DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `statut` enum('publie','brouillon') DEFAULT 'publie',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pages`
--

INSERT INTO `pages` (`id`, `titre`, `slug`, `contenu`, `meta_title`, `meta_description`, `statut`, `updated_at`) VALUES
(1, 'Mentions Légales', 'mentions-legales', '<h2>Mentions Légales</h2><p>Le site faj.ne est édité par le Fonds d\'Appui à la Justice du Niger (FAJ), établissement public.</p>', NULL, NULL, 'publie', '2026-02-24 17:37:39'),
(2, 'Politique de Confidentialité', 'politique-confidentialite', '<h2>Politique de Confidentialité</h2><p>Nous nous engageons à protéger vos données personnelles conformément aux lois en vigueur au Niger.</p>', NULL, NULL, 'publie', '2026-02-24 17:37:39'),
(3, 'Conditions Générales', 'conditions-generales', '<h2>Conditions Générales de Don</h2><p>En effectuant un don au FAJ, vous acceptez les présentes conditions générales.</p>', NULL, NULL, 'publie', '2026-02-24 17:37:39');

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

CREATE TABLE `parametres` (
  `id` int(11) NOT NULL,
  `cle` varchar(100) NOT NULL,
  `valeur` text DEFAULT NULL,
  `type` enum('texte','nombre','image','booleen','html') DEFAULT 'texte',
  `groupe` varchar(50) DEFAULT 'general',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `parametres`
--

INSERT INTO `parametres` (`id`, `cle`, `valeur`, `type`, `groupe`, `updated_at`) VALUES
(1, 'site_nom', 'Fonds d\'Appui à la Justice', 'texte', 'general', '2026-02-24 17:37:39'),
(2, 'site_slogan', 'Ensemble pour une Justice accessible à tous', 'texte', 'general', '2026-02-24 17:37:39'),
(3, 'site_description', 'Le FAJ collecte des fonds pour moderniser et améliorer le système judiciaire du Niger', 'texte', 'general', '2026-02-24 17:37:39'),
(4, 'site_email', 'contact@faj.ne', 'texte', 'contact', '2026-02-24 17:37:39'),
(5, 'site_telephone', '+227 20 XX XX XX', 'texte', 'contact', '2026-02-24 17:37:39'),
(6, 'site_adresse', 'Niamey, Niger', 'texte', 'contact', '2026-02-24 17:37:39'),
(7, 'site_facebook', 'https://facebook.com/fajniger', 'texte', 'reseaux', '2026-02-24 17:37:39'),
(8, 'site_twitter', 'https://twitter.com/fajniger', 'texte', 'reseaux', '2026-02-24 17:37:39'),
(9, 'site_linkedin', 'https://linkedin.com/company/fajniger', 'texte', 'reseaux', '2026-02-24 17:37:39'),
(10, 'site_youtube', '', 'texte', 'reseaux', '2026-02-24 17:37:39'),
(11, 'hero_titre', 'Votre don peut changer des vies', 'texte', 'accueil', '2026-02-24 17:37:39'),
(12, 'hero_sous_titre', 'Participez à la modernisation du système judiciaire du Niger', 'texte', 'accueil', '2026-02-24 17:37:39'),
(13, 'a_propos_titre', 'À Propos du FAJ', 'texte', 'a_propos', '2026-02-24 17:37:39'),
(14, 'a_propos_texte', 'Le Fonds d\'Appui à la Justice (FAJ) est un mécanisme de financement créé pour soutenir la modernisation et l\'amélioration du système judiciaire du Niger.', 'html', 'a_propos', '2026-02-24 17:37:39'),
(15, 'objectif_global', '500000000', 'nombre', 'statistiques', '2026-02-24 17:37:39'),
(16, 'total_collecte', '0', 'nombre', 'statistiques', '2026-02-24 17:37:39');

-- --------------------------------------------------------

--
-- Structure de la table `partenaires`
--

CREATE TABLE `partenaires` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `type` enum('institutionnel','financier','technique','media') DEFAULT 'institutionnel',
  `ordre` int(11) DEFAULT 0,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `partenaires`
--

INSERT INTO `partenaires` (`id`, `nom`, `logo`, `site_web`, `type`, `ordre`, `actif`, `created_at`) VALUES
(1, 'Ministère de la Justice du Niger', 'part_1772101745.png', '', 'institutionnel', 1, 1, '2026-02-24 17:37:39'),
(2, 'ANSI', 'part_1772101789.jpg', '', 'institutionnel', 2, 1, '2026-02-24 17:37:39'),
(3, 'Niger TELECOM', 'part_1772101866.png', '', '', 3, 1, '2026-02-24 17:37:39');

-- --------------------------------------------------------

--
-- Structure de la table `projets`
--

CREATE TABLE `projets` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description_courte` text DEFAULT NULL,
  `description_longue` longtext DEFAULT NULL,
  `categorie` enum('infrastructure','formation','humanisation','acces_justice','numerisation','autre') DEFAULT 'autre',
  `objectif_montant` decimal(15,2) DEFAULT 0.00,
  `montant_collecte` decimal(15,2) DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `images_galerie` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images_galerie`)),
  `statut` enum('actif','termine','en_pause','brouillon') DEFAULT 'actif',
  `priorite` int(11) DEFAULT 0,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `projets`
--

INSERT INTO `projets` (`id`, `titre`, `slug`, `description_courte`, `description_longue`, `categorie`, `objectif_montant`, `montant_collecte`, `image`, `images_galerie`, `statut`, `priorite`, `date_debut`, `date_fin`, `created_at`, `updated_at`) VALUES
(1, 'Construction et équipement de Tribunaux', 'construction-equipement-tribunaux', 'Financement de la construction et de l\'équipement des tribunaux dans les régions du Niger', '<p>Ce projet vise à améliorer l\'infrastructure judiciaire du Niger en construisant et équipant des tribunaux modernes dans toutes les régions du pays. L\'accès à la justice est un droit fondamental, et des infrastructures appropriées sont essentielles pour son exercice effectif.</p><p>Le projet comprend la construction de salles d\'audience, de bureaux pour les juges et greffiers, de salles d\'attente pour les justiciables, et l\'équipement en matériel informatique et mobilier de bureau.</p>', 'infrastructure', 150000000.00, 0.00, NULL, NULL, 'actif', 1, NULL, NULL, '2026-02-24 17:37:39', '2026-02-24 17:37:39'),
(2, 'Programme de Formation des Acteurs Judiciaires', 'formation-acteurs-judiciaires', 'Renforcement des capacités des magistrats, avocats et auxiliaires de justice', '<p>La formation continue des acteurs du système judiciaire est cruciale pour garantir une justice de qualité. Ce programme finance des sessions de formation pour les magistrats, avocats, huissiers, notaires et autres auxiliaires de justice.</p><p>Les formations couvrent les nouvelles législations, les procédures modernes, et les meilleures pratiques en matière de justice.</p>', 'formation', 80000000.00, 0.00, NULL, NULL, 'actif', 2, NULL, NULL, '2026-02-24 17:37:39', '2026-02-24 17:37:39'),
(3, 'Humanisation du Milieu Carcéral', 'humanisation-milieu-carceral', 'Amélioration des conditions de détention et réinsertion sociale des détenus', '<p>Ce projet s\'attaque aux conditions de détention dans les établissements pénitentiaires du Niger. Il finance l\'amélioration des infrastructures sanitaires, l\'accès aux soins médicaux, et les programmes de réinsertion sociale et professionnelle pour les détenus.</p>', 'humanisation', 100000000.00, 0.00, NULL, NULL, 'actif', 3, NULL, NULL, '2026-02-24 17:37:39', '2026-02-24 17:37:39'),
(4, 'Accès à la Justice pour les Populations Vulnérables', 'acces-justice-vulnerables', 'Aide juridictionnelle gratuite pour les personnes démunies et les femmes victimes de violence', '<p>Beaucoup de Nigériens n\'ont pas les moyens de se payer les services d\'un avocat. Ce projet finance l\'aide juridictionnelle gratuite pour les personnes sans ressources, notamment les femmes victimes de violence, les enfants en difficulté et les personnes âgées.</p>', 'acces_justice', 60000000.00, 0.00, NULL, NULL, 'actif', 4, NULL, NULL, '2026-02-24 17:37:39', '2026-02-24 17:37:39'),
(5, 'Numérisation du Système Judiciaire', 'numerisation-systeme-judiciaire', 'Modernisation et digitalisation des archives et procédures judiciaires', '<p>La numérisation du système judiciaire permettra d\'améliorer l\'efficacité des tribunaux, de réduire les délais de traitement des dossiers et d\'améliorer la transparence. Ce projet finance l\'acquisition de matériel informatique et le développement de logiciels spécialisés.</p>', 'numerisation', 120000000.00, 0.00, NULL, NULL, 'actif', 5, NULL, NULL, '2026-02-24 17:37:39', '2026-02-24 17:37:39');

-- --------------------------------------------------------

--
-- Structure de la table `statistiques`
--

CREATE TABLE `statistiques` (
  `id` int(11) NOT NULL,
  `cle` varchar(100) NOT NULL,
  `valeur` bigint(20) DEFAULT 0,
  `label` varchar(150) DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `statistiques`
--

INSERT INTO `statistiques` (`id`, `cle`, `valeur`, `label`, `icone`, `updated_at`) VALUES
(1, 'total_donateurs', 0, 'Donateurs', 'fas fa-users', '2026-02-24 17:37:39'),
(2, 'total_dons', 0, 'Dons collectés (FCFA)', 'fas fa-hand-holding-heart', '2026-02-24 17:37:39'),
(3, 'total_projets', 0, 'Projets financés', 'fas fa-project-diagram', '2026-02-24 17:37:39'),
(4, 'total_beneficiaires', 0, 'Bénéficiaires', 'fas fa-balance-scale', '2026-02-24 17:37:39');

-- --------------------------------------------------------

--
-- Structure de la table `temoignages`
--

CREATE TABLE `temoignages` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `poste` varchar(150) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `contenu` text NOT NULL,
  `note` int(11) DEFAULT 5,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `temoignages`
--

INSERT INTO `temoignages` (`id`, `nom`, `poste`, `photo`, `contenu`, `note`, `actif`, `created_at`) VALUES
(1, 'Alhaji Moussa', 'Commerçant, Niamey', NULL, 'Grâce au FAJ, j\'ai pu avoir accès à l\'aide juridictionnelle et résoudre mon litige commercial. Une initiative vraiment importante pour nous.', 5, 1, '2026-02-24 17:37:39'),
(2, 'Mme Mariama', 'Enseignante, Zinder', NULL, 'Le FAJ travaille pour que la justice ne soit plus un luxe réservé aux riches. Je soutiens cette cause de tout cœur.', 5, 1, '2026-02-24 17:37:39'),
(3, 'Dr. Ibrahim', 'Médecin, Agadez', NULL, 'La modernisation du système judiciaire est essentielle pour le développement du Niger. Le FAJ fait un travail remarquable.', 5, 1, '2026-02-24 17:37:39');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `actualites`
--
ALTER TABLE `actualites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Index pour la table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `dons`
--
ALTER TABLE `dons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `projet_id` (`projet_id`);

--
-- Index pour la table `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Index pour la table `parametres`
--
ALTER TABLE `parametres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cle` (`cle`);

--
-- Index pour la table `partenaires`
--
ALTER TABLE `partenaires`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `projets`
--
ALTER TABLE `projets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Index pour la table `statistiques`
--
ALTER TABLE `statistiques`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cle` (`cle`);

--
-- Index pour la table `temoignages`
--
ALTER TABLE `temoignages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `actualites`
--
ALTER TABLE `actualites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `dons`
--
ALTER TABLE `dons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `equipe`
--
ALTER TABLE `equipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `parametres`
--
ALTER TABLE `parametres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `partenaires`
--
ALTER TABLE `partenaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `projets`
--
ALTER TABLE `projets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `statistiques`
--
ALTER TABLE `statistiques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `temoignages`
--
ALTER TABLE `temoignages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `actualites`
--
ALTER TABLE `actualites`
  ADD CONSTRAINT `actualites_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `dons`
--
ALTER TABLE `dons`
  ADD CONSTRAINT `dons_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
