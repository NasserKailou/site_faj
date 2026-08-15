-- =============================================================================
--  PATCH — Données officielles du FAJ (Fonds d'Appui à la Justice)
--  Source de vérité : présentation institutionnelle (PowerPoint) + décret & arrêtés
--  Cible : base `faj_niger` (structure du dump phpMyAdmin fourni)
--
--  Principes :
--    - Idempotent (réexécutable) : UPSERT sur les clés uniques, UPDATE ciblés.
--    - Règle « ne rien supprimer sans remplacer » : le contenu factice est
--      corrigé ou DÉSACTIVÉ (actif = 0 / statut = brouillon), jamais effacé.
--    - Aucun montant ni donnée inventé : les valeurs inconnues restent à 0
--      ou marquées « à définir » (TODO: à fournir par le FAJ).
--
--  Utilisation :
--    mysql -u root -p faj_niger < patch_faj_officiel.sql
--  (ou via l'onglet Importer de phpMyAdmin, base `faj_niger` sélectionnée)
-- =============================================================================

SET NAMES utf8mb4;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- -----------------------------------------------------------------------------
-- 1) PARAMÈTRES DU SITE (table `parametres`)
--    Mise à jour des valeurs existantes + ajout des paramètres institutionnels.
--    UPSERT sur la clé unique `cle`.
-- -----------------------------------------------------------------------------
INSERT INTO `parametres` (`cle`, `valeur`, `type`, `groupe`) VALUES
  -- Identité
  ('site_nom',          'Fonds d''Appui à la Justice',                                   'texte',   'general'),
  ('site_sigle',        'FAJ',                                                           'texte',   'general'),
  ('site_slogan',       'Le FAJ, l''assurance d''une Justice moderne',                   'texte',   'general'),
  ('site_description',  'Le Fonds d''Appui à la Justice (FAJ) mobilise ressources et partenaires pour moderniser le système judiciaire et pénitentiaire du Niger.', 'texte', 'general'),
  ('site_tutelle',      'Ministère de la Justice et des Droits de l''Homme',             'texte',   'general'),
  ('site_decret',       'Décret N° 2023-113/PRN/MJ du 26 janvier 2023',                  'texte',   'general'),

  -- Contact (coordonnées officielles)
  ('site_email',        'contact@faj.ne',                                               'texte',   'contact'),
  ('site_web',          'www.faj.ne',                                                   'texte',   'contact'),
  ('site_telephone',    '00227 20 37 15 95 / 00227 96 13 28 15',                         'texte',   'contact'),
  ('site_adresse',      'Niamey-Niger, Quartier Koira Kano, Rue KK 46, BP : 11240',     'texte',   'contact'),

  -- Accueil / Hero
  ('hero_titre',        'Le FAJ, l''assurance d''une <span>Justice moderne</span>',      'html',    'accueil'),
  ('hero_sous_titre',   'À l''horizon 2035, investir dans la Justice pour un système judiciaire et pénitentiaire accessible, performant et modernisé au Niger.', 'texte', 'accueil'),

  -- À propos
  ('a_propos_titre',    'Pour une Justice <span>moderne et accessible</span>',           'html',    'a_propos'),
  ('a_propos_texte',    '<p>Le Fonds d''Appui à la Justice (FAJ) est un établissement public créé par le décret N° 2023-113/PRN/MJ du 26 janvier 2023, placé sous la tutelle du Ministère de la Justice et des Droits de l''Homme.</p><p>Sa mission s''articule autour de trois axes : investir dans le domaine de la Justice, faciliter l''accès à la Justice et moderniser le système judiciaire et pénitentiaire. À l''horizon 2035, le FAJ ambitionne un meilleur accès à la Justice et un système carcéral modernisé.</p>', 'html', 'a_propos'),

  -- Vision & mission
  ('vision_2035',       'À l''horizon 2035, assurer un meilleur accès à la Justice et un système carcéral modernisé.', 'texte', 'a_propos'),
  ('mission_axe_1',     'Investir : mobiliser l''investissement public et privé dans le domaine de la Justice.', 'texte', 'a_propos'),
  ('mission_axe_2',     'Faciliter l''accès : renforcer l''assistance juridique et judiciaire pour un meilleur accès à la Justice.', 'texte', 'a_propos'),
  ('mission_axe_3',     'Moderniser : moderniser le système carcéral et les services judiciaires.', 'texte', 'a_propos'),

  -- Cadre financier — clé de répartition (Arrêté n° MF/MJ 00011 du 08 février 2021)
  ('repartition_arrete',  'Arrêté n° MF/MJ 00011 du 08 février 2021',                    'texte',   'finances'),
  ('repartition_amendes', '30',                                                         'nombre',  'finances'),
  ('repartition_penalites','30',                                                        'nombre',  'finances'),
  ('repartition_confiscations','10',                                                    'nombre',  'finances'),

  -- Réseaux sociaux (TODO : à confirmer par le FAJ)
  ('site_facebook',     '',                                                             'texte',   'reseaux'),
  ('site_twitter',      '',                                                             'texte',   'reseaux'),
  ('site_linkedin',     '',                                                             'texte',   'reseaux'),
  ('site_youtube',      '',                                                             'texte',   'reseaux')
ON DUPLICATE KEY UPDATE
  `valeur` = VALUES(`valeur`),
  `type`   = VALUES(`type`),
  `groupe` = VALUES(`groupe`);

-- -----------------------------------------------------------------------------
-- 2) STATISTIQUES (table `statistiques`)
--    Libellés officiels, valeurs à 0 (renseignées au fil de l'activité).
-- -----------------------------------------------------------------------------
UPDATE `statistiques` SET `label` = 'Domaines d''intervention', `icone` = 'fas fa-diagram-project' WHERE `cle` = 'total_projets';
UPDATE `statistiques` SET `label` = 'Bénéficiaires de la Justice', `icone` = 'fas fa-scale-balanced' WHERE `cle` = 'total_beneficiaires';
UPDATE `statistiques` SET `label` = 'Contributions collectées (FCFA)', `icone` = 'fas fa-hand-holding-heart' WHERE `cle` = 'total_dons';
UPDATE `statistiques` SET `label` = 'Contributeurs', `icone` = 'fas fa-users' WHERE `cle` = 'total_donateurs';

-- -----------------------------------------------------------------------------
-- 3) ÉQUIPE → CONSEIL D'ADMINISTRATION (table `equipe`)
--    Remplace les membres nominatifs fictifs par les 5 représentants
--    institutionnels officiels (aucun nom de personne inventé).
--    On DÉSACTIVE l'ancien contenu (actif = 0) puis on insère l'officiel.
-- -----------------------------------------------------------------------------
UPDATE `equipe` SET `actif` = 0 WHERE `actif` = 1;

INSERT INTO `equipe` (`nom`, `poste`, `biographie`, `photo`, `email`, `linkedin`, `ordre`, `actif`) VALUES
  ('Représentant du Ministère en charge de la Justice',               'Membre du Conseil d''Administration', 'Membre représentant le Ministère de la Justice et des Droits de l''Homme au sein du Conseil d''Administration du FAJ.', NULL, NULL, NULL, 1, 1),
  ('Représentant du Ministère en charge des Finances',                'Membre du Conseil d''Administration', 'Membre représentant le Ministère en charge des Finances au sein du Conseil d''Administration du FAJ.', NULL, NULL, NULL, 2, 1),
  ('Représentant du Ministère en charge du Plan',                     'Membre du Conseil d''Administration', 'Membre représentant le Ministère en charge du Plan au sein du Conseil d''Administration du FAJ.', NULL, NULL, NULL, 3, 1),
  ('Représentant du Ministère en charge de l''Intérieur',            'Membre du Conseil d''Administration', 'Membre représentant le Ministère en charge de l''Intérieur au sein du Conseil d''Administration du FAJ.', NULL, NULL, NULL, 4, 1),
  ('Représentant du Ministère en charge des Domaines et de l''Habitat','Membre du Conseil d''Administration', 'Membre représentant le Ministère en charge des Domaines et de l''Habitat au sein du Conseil d''Administration du FAJ.', NULL, NULL, NULL, 5, 1);

-- -----------------------------------------------------------------------------
-- 4) PROJETS (table `projets`)
--    Alignement sur les domaines d'intervention et projets phares officiels.
--    Les montants restent à 0.00 (« à définir ») — aucun chiffre inventé.
--    UPSERT sur la clé unique `slug`.
-- -----------------------------------------------------------------------------

-- 4a. Domaines d'intervention prioritaires (6) — mise à jour du contenu existant
--     et ajout des domaines manquants. Montants = 0 (TODO : à fournir par le FAJ).
INSERT INTO `projets`
  (`titre`, `slug`, `description_courte`, `description_longue`, `categorie`, `objectif_montant`, `montant_collecte`, `statut`, `priorite`)
VALUES
  ('Infrastructures judiciaires', 'infrastructures-judiciaires',
   'Construction, réhabilitation et équipement des juridictions.',
   '<p>Doter le Niger de juridictions modernes et fonctionnelles par la construction, la réhabilitation et l''équipement des tribunaux et cours, afin de rapprocher la Justice des justiciables.</p>',
   'infrastructure', 0.00, 0.00, 'actif', 1),

  ('Justice numérique', 'justice-numerique',
   'Informatisation, dématérialisation et actes de Justice électroniques sécurisés.',
   '<p>Moderniser la Justice par l''informatisation des procédures, la dématérialisation des dossiers et la mise en place d''actes de Justice électroniques sécurisés, pour plus d''efficacité et de transparence.</p>',
   'numerisation', 0.00, 0.00, 'actif', 2),

  ('Infrastructures pénitentiaires', 'infrastructures-penitentiaires',
   'Construction, réhabilitation et équipement des établissements pénitentiaires.',
   '<p>Améliorer les conditions de détention par la construction, la réhabilitation et l''équipement des établissements pénitentiaires, dans le respect de la dignité humaine.</p>',
   'humanisation', 0.00, 0.00, 'actif', 3),

  ('Réinsertion des détenus', 'reinsertion-des-detenus',
   'Appui aux centres pénitentiaires de production pour la réinsertion sociale.',
   '<p>Favoriser la réinsertion sociale et professionnelle des détenus à travers l''appui aux centres pénitentiaires de production et aux programmes de formation.</p>',
   'humanisation', 0.00, 0.00, 'actif', 4),

  ('Personnel pénitentiaire spécialisé', 'personnel-penitentiaire-specialise',
   'Mise en place et opérationnalisation d''un personnel pénitentiaire spécialisé.',
   '<p>Mettre en place et rendre opérationnel un personnel pénitentiaire spécialisé, formé aux exigences d''une administration pénitentiaire moderne.</p>',
   'formation', 0.00, 0.00, 'actif', 5),

  ('Logistique des services judiciaires', 'logistique-services-judiciaires',
   'Dotation en moyens logistiques des services judiciaires et pénitentiaires.',
   '<p>Renforcer les capacités opérationnelles par la dotation en moyens logistiques adaptés aux services judiciaires et pénitentiaires.</p>',
   'infrastructure', 0.00, 0.00, 'actif', 6)
ON DUPLICATE KEY UPDATE
  `titre`             = VALUES(`titre`),
  `description_courte`= VALUES(`description_courte`),
  `description_longue`= VALUES(`description_longue`),
  `categorie`         = VALUES(`categorie`),
  `statut`            = VALUES(`statut`),
  `priorite`          = VALUES(`priorite`);

-- 4b. Projets phares officiels (montants à définir — aucun chiffre inventé).
INSERT INTO `projets`
  (`titre`, `slug`, `description_courte`, `description_longue`, `categorie`, `objectif_montant`, `montant_collecte`, `statut`, `priorite`)
VALUES
  ('Perspectives de Projets Pilotes (PPP-FAJ)', 'ppp-faj',
   'Projets pilotes structurants portés par le FAJ.',
   '<p>Ensemble de projets pilotes structurants destinés à démontrer l''impact du FAJ sur la modernisation de la Justice. <em>TODO : détails et budget à fournir par le FAJ.</em></p>',
   'autre', 0.00, 0.00, 'actif', 10),

  ('Modernisation de la Cour d''État (Kotou)', 'modernisation-cour-etat-kotou',
   'Modernisation de la Cour d''État à Kotou.',
   '<p>Projet phare de modernisation de la Cour d''État (Kotou). <em>TODO : détails et budget à fournir par le FAJ.</em></p>',
   'infrastructure', 0.00, 0.00, 'actif', 11),

  ('Projet Alkali — Cours d''Appel', 'projet-alkali-cours-appel',
   'Modernisation des Cours d''Appel (Projet Alkali).',
   '<p>Projet Alkali dédié à la modernisation des Cours d''Appel. <em>TODO : détails et budget à fournir par le FAJ.</em></p>',
   'infrastructure', 0.00, 0.00, 'actif', 12),

  ('Modernisation du TGI Hors Classe de Niamey', 'modernisation-tgi-hors-classe-niamey',
   'Modernisation du Tribunal de Grande Instance Hors Classe de Niamey.',
   '<p>Modernisation du TGI Hors Classe de Niamey. <em>TODO : détails et budget à fournir par le FAJ.</em></p>',
   'infrastructure', 0.00, 0.00, 'actif', 13),

  ('Tribunaux d''Arrondissements Communaux', 'tribunaux-arrondissements-communaux',
   'Création et équipement des Tribunaux d''Arrondissements Communaux.',
   '<p>Programme de création et d''équipement des Tribunaux d''Arrondissements Communaux. <em>TODO : détails et budget à fournir par le FAJ.</em></p>',
   'infrastructure', 0.00, 0.00, 'actif', 14)
ON DUPLICATE KEY UPDATE
  `titre`             = VALUES(`titre`),
  `description_courte`= VALUES(`description_courte`),
  `description_longue`= VALUES(`description_longue`),
  `categorie`         = VALUES(`categorie`),
  `statut`            = VALUES(`statut`),
  `priorite`          = VALUES(`priorite`);

-- 4c. Anciens projets génériques du dump : on les DÉSACTIVE (brouillon) plutôt
--     que de les supprimer, car remplacés par les domaines officiels ci-dessus.
UPDATE `projets` SET `statut` = 'brouillon'
WHERE `slug` IN (
  'construction-equipement-tribunaux',
  'formation-acteurs-judiciaires',
  'humanisation-milieu-carceral',
  'acces-justice-vulnerables',
  'numerisation-systeme-judiciaire'
);

-- -----------------------------------------------------------------------------
-- 5) PARTENAIRES (table `partenaires`)
--    Conserver la tutelle (Ministère). Les partenaires non confirmés sont
--    DÉSACTIVÉS (actif = 0) en attendant confirmation officielle.
-- -----------------------------------------------------------------------------
UPDATE `partenaires`
  SET `nom` = 'Ministère de la Justice et des Droits de l''Homme',
      `type` = 'institutionnel',
      `actif` = 1
  WHERE `nom` LIKE 'Minist%Justice%';

UPDATE `partenaires` SET `actif` = 0 WHERE `nom` IN ('ANSI', 'Niger TELECOM');

-- -----------------------------------------------------------------------------
-- 6) TÉMOIGNAGES (table `temoignages`)
--    Témoignages fictifs → DÉSACTIVÉS (pas de source officielle).
--    (Aucune suppression : ils restent en base, masqués du site.)
-- -----------------------------------------------------------------------------
UPDATE `temoignages` SET `actif` = 0
WHERE `nom` IN ('Alhaji Moussa', 'Mme Mariama', 'Dr. Ibrahim');

-- -----------------------------------------------------------------------------
-- 7) ACTUALITÉS (table `actualites`)
--    Actualité de test → repassée en brouillon (masquée), non supprimée.
-- -----------------------------------------------------------------------------
UPDATE `actualites` SET `statut` = 'brouillon'
WHERE `slug` = 'test-actu-faj' OR `titre` = 'TEST ACTU FAJ';

-- -----------------------------------------------------------------------------
-- 8) PAGES LÉGALES (table `pages`)
--    Enrichissement des mentions légales avec le cadre institutionnel officiel.
-- -----------------------------------------------------------------------------
UPDATE `pages`
  SET `contenu` = '<h2>Mentions Légales</h2><p>Le site <strong>www.faj.ne</strong> est édité par le <strong>Fonds d''Appui à la Justice (FAJ)</strong>, établissement public créé par le décret <strong>N° 2023-113/PRN/MJ du 26 janvier 2023</strong>, placé sous la tutelle du <strong>Ministère de la Justice et des Droits de l''Homme</strong>.</p><p><strong>Adresse :</strong> Niamey-Niger, Quartier Koira Kano, Rue KK 46, BP : 11240<br><strong>Téléphone :</strong> 00227 20 37 15 95 / 00227 96 13 28 15<br><strong>Email :</strong> contact@faj.ne</p>'
  WHERE `slug` = 'mentions-legales';

COMMIT;

-- =============================================================================
--  FIN DU PATCH
--
--  Éléments volontairement laissés en attente (TODO: à fournir par le FAJ) :
--    - Logo officiel et photos réelles des projets (colonnes `image`).
--    - Montants / objectifs budgétaires des projets (restent à 0.00).
--    - Liens réseaux sociaux officiels.
--    - Identifiants marchands de paiement (configurés dans .env, hors base).
-- =============================================================================
