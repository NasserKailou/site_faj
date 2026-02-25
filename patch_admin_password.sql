-- ============================================================
-- PATCH : Correction du mot de passe administrateur FAJ
-- ============================================================
-- Mot de passe : Admin@FAJ2024!
-- Email        : admin@faj.ne
--
-- UTILISATION :
--   MySQL  → Importer ce fichier dans phpMyAdmin (base: faj_niger ou faj_db)
--   SQLite → Le mot de passe sera corrigé automatiquement via config.php
--            (supprimer faj_data.sqlite pour forcer la ré-initialisation)
-- ============================================================

-- Mise à jour du hash du mot de passe (Admin@FAJ2024!)
UPDATE `admins`
SET `mot_de_passe` = '$2y$12$LjpqZHL7iq1U7SdycSbUXO7FwI4ATM3gWCp29eNJC9pLRrkSbJoQy',
    `actif`        = 1,
    `tentatives_connexion` = 0,
    `bloque_jusqu` = 0
WHERE `email` = 'admin@faj.ne';

-- Si la ligne n'existe pas encore, l'insérer
INSERT INTO `admins` (`nom`, `email`, `mot_de_passe`, `role`, `actif`)
SELECT 'Super Administrateur', 'admin@faj.ne',
       '$2y$12$LjpqZHL7iq1U7SdycSbUXO7FwI4ATM3gWCp29eNJC9pLRrkSbJoQy',
       'super_admin', 1
WHERE NOT EXISTS (
    SELECT 1 FROM `admins` WHERE `email` = 'admin@faj.ne'
);

-- Vérification
SELECT id, nom, email, role, actif FROM `admins` WHERE email = 'admin@faj.ne';
