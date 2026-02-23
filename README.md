# FAJ Niger - Fonds d'Appui à la Justice du Niger

## Vue d'Ensemble
Site web officiel du FAJ Niger pour la collecte de fonds en faveur de la modernisation du système judiciaire du Niger.

## 🎨 Charte Graphique
- **Couleur principale** : Bleu marine `#1B2A4A`
- **Couleur secondaire** : Orange `#E8870A`
- **Police** : Poppins (principale), Playfair Display (titres)

## 📁 Structure du Projet
```
faj-niger/
├── index.php                    # Page d'accueil
├── assets/
│   ├── css/style.css            # CSS principal
│   ├── js/main.js               # JavaScript principal
│   └── images/                  # Images (logo, hero)
├── includes/
│   ├── config.php               # Configuration (DB, paiements, constantes)
│   ├── header.php               # En-tête commun
│   └── footer.php               # Pied de page commun
├── pages/
│   ├── a-propos.php             # Page À Propos
│   ├── projets.php              # Liste des projets
│   ├── don.php                  # Formulaire de don
│   ├── don-succes.php           # Page de confirmation de don
│   ├── actualites.php           # Actualités
│   └── contact.php              # Formulaire de contact
├── admin/
│   ├── login.php                # Connexion admin
│   ├── logout.php               # Déconnexion
│   ├── dashboard.php            # Tableau de bord
│   ├── parametres.php           # Paramètres du site
│   ├── dons/liste.php           # Gestion des dons
│   ├── projets/liste.php        # Gestion des projets
│   ├── actualites/liste.php     # Gestion des actualités
│   ├── contacts/liste.php       # Messages de contact
│   └── assets/css/admin.css    # CSS admin
├── api/
│   ├── don.php                  # Traitement des dons
│   ├── contact.php              # Traitement des contacts
│   ├── newsletter.php           # Inscription newsletter
│   └── admin-actions.php        # Actions AJAX admin
├── uploads/                     # Fichiers uploadés
│   ├── projets/
│   ├── team/
│   └── partenaires/
├── database.sql                 # Script SQL de création de la BDD
└── .htaccess                    # Configuration Apache

```

## 🚀 Installation

### Prérequis
- PHP 8.0+
- MySQL 5.7+ / MariaDB
- Apache avec mod_rewrite

### Étapes d'installation

1. **Copier les fichiers** sur votre serveur web

2. **Créer la base de données** :
```bash
mysql -u root -p < database.sql
```

3. **Configurer `includes/config.php`** :
```php
define('SITE_URL', 'https://faj.ne');  // Votre domaine
define('DB_HOST', 'localhost');
define('DB_NAME', 'faj_db');
define('DB_USER', 'votre_user');
define('DB_PASS', 'votre_password');
```

4. **Configurer les passerelles de paiement** dans `includes/config.php` :
```php
// CinetPay (Orange Money, Moov Money)
define('CINETPAY_APIKEY', 'votre_cle_api');
define('CINETPAY_SITE_ID', 'votre_site_id');

// Stripe (Visa, Mastercard)
define('STRIPE_PUBLIC_KEY', 'pk_live_...');
define('STRIPE_SECRET_KEY', 'sk_live_...');
```

5. **Permissions des dossiers** :
```bash
chmod 755 uploads/
chmod 755 uploads/projets/
chmod 755 uploads/team/
chmod 755 uploads/partenaires/
```

## 🔐 Accès Administration

- **URL** : `https://faj.ne/admin/login.php`
- **Email** : `admin@faj.ne`
- **Mot de passe par défaut** : `Admin@FAJ2024`

⚠️ **Changez immédiatement le mot de passe après la première connexion !**

## 💳 Passerelles de Paiement

### Configurées et intégrées
| Méthode | Service | Public cible |
|---------|---------|--------------|
| Orange Money | CinetPay | Niger, Afrique |
| Moov Money | CinetPay | Niger, Afrique |
| Visa | Stripe | International |
| Mastercard | Stripe | International |

### À obtenir
1. **CinetPay** : https://cinetpay.com - Créer un compte marchand
2. **Stripe** : https://stripe.com - Créer un compte (disponible pour les ONGs)

## 🎯 Fonctionnalités

### Site Public
- ✅ Page d'accueil avec slider hero
- ✅ Section statistiques animées
- ✅ Présentation des projets avec barres de progression
- ✅ Formulaire de don multi-méthodes
- ✅ Page À Propos avec équipe
- ✅ Page Actualités
- ✅ Page Contact
- ✅ Newsletter
- ✅ Design responsive mobile
- ✅ Animations AOS
- ✅ Charte graphique FAJ (bleu #1B2A4A + orange #E8870A)

### Panel d'Administration
- ✅ Tableau de bord avec statistiques
- ✅ Gestion des dons (confirmation, filtres, export)
- ✅ Gestion des projets (CRUD complet)
- ✅ Gestion des actualités (CRUD)
- ✅ Gestion de l'équipe
- ✅ Gestion des partenaires
- ✅ Gestion des témoignages
- ✅ Messages de contact
- ✅ Paramètres du site (modifiables sans code)
- ✅ Gestion des administrateurs

## 📧 Contact
- **Email** : contact@faj.ne
- **Site** : https://faj.ne

## 🛡️ Sécurité
- Mots de passe hashés (bcrypt)
- Protection CSRF
- Validation des entrées
- Headers de sécurité
- Accès admin protégé par session

---
*Développé pour le Fonds d'Appui à la Justice du Niger - FAJ*
