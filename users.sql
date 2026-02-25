UPDATE `admins`
SET `mot_de_passe` = '$2y$12$LjpqZHL7iq1U7SdycSbUXO7FwI4ATM3gWCp29eNJC9pLRrkSbJoQy',
    `actif`        = 1,
    `tentatives_connexion` = 0,
    `bloque_jusqu` = 0
WHERE `email` = 'admin@faj.ne';



INSERT INTO `admins` (`nom`, `email`, `mot_de_passe`, `role`, `actif`)
VALUES (
    'Super Administrateur',
    'admin@faj.ne',
    '$2y$12$LjpqZHL7iq1U7SdycSbUXO7FwI4ATM3gWCp29eNJC9pLRrkSbJoQy',
    'super_admin',
    1
);