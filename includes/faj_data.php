<?php
/**
 * Données institutionnelles officielles du FAJ — SOURCE DE VÉRITÉ.
 * ---------------------------------------------------------------------------
 * Centralise le contenu officiel (domaines d'intervention, valeurs,
 * gouvernance, cadre financier, projets phares) afin d'éviter la duplication
 * et le contenu factice dispersé dans les vues.
 *
 * ⚠️ Ne rien inventer : toute donnée manquante est marquée « TODO ».
 *
 * @package FAJ\Includes
 */

/**
 * Les 6 domaines d'intervention prioritaires.
 * Chaque domaine est rattaché à une catégorie existante du système
 * (infrastructure, formation, humanisation, acces_justice, numerisation)
 * pour rester compatible avec la navigation et les filtres en place.
 */
function fajDomaines(): array
{
    return [
        [
            'icon'      => 'fas fa-building-columns',
            'titre'     => 'Infrastructures judiciaires',
            'desc'      => 'Construction, réhabilitation et équipement des juridictions.',
            'categorie' => 'infrastructure',
            'color'     => '#1B2A4A',
        ],
        [
            'icon'      => 'fas fa-laptop-code',
            'titre'     => 'Justice numérique',
            'desc'      => 'Informatisation, dématérialisation et actes de Justice électroniques sécurisés.',
            'categorie' => 'numerisation',
            'color'     => '#17a2b8',
        ],
        [
            'icon'      => 'fas fa-landmark',
            'titre'     => 'Infrastructures pénitentiaires',
            'desc'      => 'Construction, réhabilitation et équipement des établissements pénitentiaires.',
            'categorie' => 'infrastructure',
            'color'     => '#6f42c1',
        ],
        [
            'icon'      => 'fas fa-seedling',
            'titre'     => 'Réinsertion des détenus',
            'desc'      => 'Appui aux centres pénitentiaires de production pour la réinsertion sociale.',
            'categorie' => 'humanisation',
            'color'     => '#28a745',
        ],
        [
            'icon'      => 'fas fa-users-gear',
            'titre'     => 'Personnel pénitentiaire',
            'desc'      => 'Mise en place et opérationnalisation d\'un personnel pénitentiaire spécialisé.',
            'categorie' => 'formation',
            'color'     => '#E8870A',
        ],
        [
            'icon'      => 'fas fa-truck-ramp-box',
            'titre'     => 'Logistique',
            'desc'      => 'Dotation en moyens logistiques des services judiciaires et pénitentiaires.',
            'categorie' => 'infrastructure',
            'color'     => '#dc3545',
        ],
    ];
}

/**
 * Les 4 valeurs du FAJ.
 */
function fajValeurs(): array
{
    return [
        ['icon' => 'fas fa-shield-halved', 'titre' => 'Intégrité',    'desc' => 'Tolérance zéro pour la corruption et les conflits d\'intérêts.'],
        ['icon' => 'fas fa-universal-access', 'titre' => 'Accessibilité', 'desc' => 'Une Justice à la portée de tous, sans distinction.'],
        ['icon' => 'fas fa-lightbulb',     'titre' => 'Innovation',   'desc' => 'Moderniser la Justice par le numérique et de nouvelles solutions.'],
        ['icon' => 'fas fa-gauge-high',    'titre' => 'Performance',  'desc' => 'Efficacité, redevabilité et résultats mesurables.'],
    ];
}

/**
 * Les 3 axes de la mission.
 */
function fajAxes(): array
{
    return [
        ['icon' => 'fas fa-coins',          'titre' => 'Investir',           'desc' => 'Mobiliser l\'investissement public et privé dans le domaine de la Justice.'],
        ['icon' => 'fas fa-door-open',      'titre' => 'Faciliter l\'accès', 'desc' => 'Renforcer l\'assistance juridique et judiciaire pour un meilleur accès à la Justice.'],
        ['icon' => 'fas fa-arrows-rotate',  'titre' => 'Moderniser',         'desc' => 'Moderniser le système carcéral et les services judiciaires.'],
    ];
}

/**
 * Bénéficiaires de l'impact social / assistance judiciaire.
 */
function fajBeneficiaires(): array
{
    return [
        ['icon' => 'fas fa-person-dress', 'label' => 'Les femmes'],
        ['icon' => 'fas fa-child',        'label' => 'Les enfants'],
        ['icon' => 'fas fa-wheelchair',   'label' => 'Les personnes en situation de handicap'],
        ['icon' => 'fas fa-hand-holding-heart', 'label' => 'Les personnes indigentes'],
    ];
}

/**
 * Conseil d'Administration (5 membres, mandat 3 ans renouvelable une fois).
 */
function fajConseilAdministration(): array
{
    return [
        'Représentant du Ministère en charge de la Justice',
        'Représentant du Ministère en charge des Finances',
        'Représentant du Ministère en charge du Plan',
        'Représentant du Ministère en charge de l\'Intérieur',
        'Représentant du Ministère en charge des Domaines et de l\'Habitat',
    ];
}

/**
 * Attributions du Conseil d'Administration.
 */
function fajAttributionsConseil(): array
{
    return [
        'Adopter le budget et le programme d\'investissement',
        'Approuver les comptes et les conventions avec les partenaires',
        'Approuver les emprunts, dons, legs et subventions',
        'Adopter l\'organigramme et le règlement intérieur',
    ];
}

/**
 * Sources de ressources du cadre financier.
 */
function fajSourcesRessources(): array
{
    return [
        'Dotation initiale de l\'État',
        'Subvention annuelle de l\'État',
        'Prélèvement sur les recettes de l\'ACGSCGRA',
        'Prélèvement sur les recettes des actes de Justice, amendes et condamnations pécuniaires',
        'Produits de taxes et redevances',
        'Revenus des biens et cessions autorisées',
        'Contributions des partenaires techniques et financiers',
        'Subventions d\'autres personnes morales, emprunts, dons et legs autorisés',
    ];
}

/**
 * Clé de répartition réglementée (Arrêté n° MF/MJ 00011 du 08 février 2021).
 */
function fajCleRepartition(): array
{
    return [
        ['pct' => 30, 'label' => 'Amendes et frais de Justice', 'detail' => 'Recouvrés au niveau des juridictions'],
        ['pct' => 30, 'label' => 'Pénalités de retard sur prestations de service', 'detail' => 'Recouvrées au niveau des juridictions'],
        ['pct' => 10, 'label' => 'Confiscations en numéraire & ventes aux enchères', 'detail' => 'Produits de l\'ACGSCGRA'],
    ];
}

/**
 * Projets phares officiels (montants : TODO à fournir par le FAJ).
 */
function fajProjetsPhares(): array
{
    return [
        ['titre' => 'Perspectives de Projets Pilotes (PPP-FAJ)', 'slug' => 'ppp-faj', 'icon' => 'fas fa-diagram-project'],
        ['titre' => 'Modernisation de la Cour d\'État (Kotou)', 'slug' => 'modernisation-cour-etat-kotou', 'icon' => 'fas fa-landmark'],
        ['titre' => 'Projet Alkali — Cours d\'Appel', 'slug' => 'projet-alkali-cours-appel', 'icon' => 'fas fa-scale-balanced'],
        ['titre' => 'Modernisation du TGI Hors Classe de Niamey', 'slug' => 'modernisation-tgi-hors-classe-niamey', 'icon' => 'fas fa-gavel'],
        ['titre' => 'Tribunaux d\'Arrondissements Communaux', 'slug' => 'tribunaux-arrondissements-communaux', 'icon' => 'fas fa-building-shield'],
    ];
}

/**
 * Arguments « Pourquoi s'engager » (partenaires).
 */
function fajPourquoiSengager(): array
{
    return [
        ['icon' => 'fas fa-building-columns', 'titre' => 'Cadre institutionnel solide', 'desc' => 'Un Fonds d\'État créé par décret, sous tutelle du Ministère de la Justice.'],
        ['icon' => 'fas fa-magnifying-glass-chart', 'titre' => 'Gestion transparente', 'desc' => 'Comptabilité publique, audits et redevabilité.'],
        ['icon' => 'fas fa-hand-holding-heart', 'titre' => 'Impact social direct', 'desc' => 'Au bénéfice des femmes, enfants, personnes handicapées et indigentes.'],
        ['icon' => 'fas fa-flag', 'titre' => 'Vision de long terme', 'desc' => 'Une ambition claire à l\'horizon 2035.'],
    ];
}
