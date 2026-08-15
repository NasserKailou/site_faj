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
│   ├── config.php               # Configuration (DB, sécurité, auto-détection SITE_URL)
│   ├── env.php                  # Chargeur du fichier .env (sans dépendance externe)
│   ├── faj_data.php             # Données institutionnelles officielles (source de vérité)
│   ├── header.php               # En-tête commun (SEO, schema.org)
│   ├── footer.php               # Pied de page commun
│   └── payment/                 # Module de paiement « clé en main »
│       ├── PaymentGatewayInterface.php  # Contrat des passerelles
│       ├── PaymentResult.php            # DTO de résultat
│       ├── AbstractGateway.php          # Base commune (cURL sécurisé, logs)
│       ├── DisabledGateway.php          # Mode démo / désactivé
│       ├── CinetPayGateway.php          # Adaptateur CinetPay (Mobile Money)
│       ├── StripeGateway.php            # Adaptateur Stripe (cartes)
│       ├── PaymentFactory.php           # Sélection de la passerelle
│       └── bootstrap.php                # Chargement des classes
├── pages/
│   ├── a-propos.php             # Page À Propos
│   ├── gouvernance.php          # Gouvernance (Conseil d'Administration)
│   ├── cadre-financier.php      # Cadre financier (ressources, clé de répartition)
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
│   ├── don.php                  # Traitement des dons (via PaymentFactory)
│   ├── webhook-cinetpay.php     # Webhook/IPN CinetPay (signature + idempotence)
│   ├── webhook-stripe.php       # Webhook Stripe (signature + idempotence)
│   ├── contact.php              # Traitement des contacts
│   ├── newsletter.php           # Inscription newsletter
│   └── admin-actions.php        # Actions AJAX admin
├── docs/
│   ├── PAIEMENT.md              # Guide d'activation du module de paiement
│   └── SECURITE.md              # Checklist de sécurité OWASP
├── .env.example                 # Modèle de configuration (à copier en .env)
├── robots.txt                   # Directives d'indexation
├── sitemap.php                  # Plan du site XML dynamique (/sitemap.xml)
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
- MySQL 5.7+ / MariaDB *(optionnel : repli automatique sur SQLite `faj_data.sqlite` si MySQL indisponible)*
- Apache avec mod_rewrite *(ou le serveur PHP intégré pour le développement)*

### Démarrage rapide (développement)
```bash
cp .env.example .env          # puis ajuster si besoin
php -S 0.0.0.0:8000 router.php # URLs propres via router.php
# → http://localhost:8000
```
Sous XAMPP, placez le projet dans `htdocs/site_faj/` et ouvrez
`http://localhost/site_faj/` (le `.htaccess` gère `RewriteBase /site_faj/`).

### Étapes d'installation

1. **Copier les fichiers** sur votre serveur web

2. **Créer la base de données** :
```bash
mysql -u root -p < database.sql
```

   **Appliquer les données officielles du FAJ** (recommandé — sur une base
   existante `faj_niger`) :
```bash
mysql -u root -p faj_niger < patch_faj_officiel.sql
```
   > Ce patch est **idempotent** (réexécutable sans doublon) et applique les
   > informations institutionnelles officielles (slogan, décret, coordonnées,
   > Conseil d'Administration, 6 domaines + projets phares, clé de répartition).
   > Conformément à la règle « ne rien supprimer sans remplacer », le contenu
   > factice n'est pas effacé mais **désactivé** (masqué du site). Les éléments
   > manquants restent marqués `TODO: à fournir par le FAJ` (logo, photos,
   > montants). Peut aussi être importé via **phpMyAdmin** (onglet *Importer*,
   > base `faj_niger` sélectionnée).

3. **Configurer l'environnement via `.env`** (nouveau) :

   La configuration sensible (URL, base de données, clés de paiement, secrets)
   est désormais externalisée dans un fichier `.env` **non versionné**
   (ignoré par Git). Copiez le modèle fourni puis renseignez vos valeurs :
```bash
cp .env.example .env
```
   Extrait des variables principales (voir `.env.example` pour la liste complète) :
```dotenv
APP_ENV=production
DEBUG_MODE=false
# SITE_URL : laisser vide pour l'auto-détection (recommandé),
#            ou forcer une valeur en production.
SITE_URL=

# Base de données
DB_HOST=localhost
DB_NAME=faj_niger
DB_USER=votre_user
DB_PASS=votre_password

# Paiement
PAYMENT_MODE=sandbox            # sandbox | live | disabled
PAYMENT_GATEWAY=cinetpay        # cinetpay | stripe
CURRENCY=XOF
```
   > **Auto-détection de `SITE_URL`** : si la variable est vide, l'application
   > déduit automatiquement le schéma, l'hôte et le sous-dossier d'installation
   > à partir de la requête HTTP. Le site fonctionne ainsi sans configuration
   > sous XAMPP (`/site_faj`), sur un port dédié ou sur un domaine de production.
   > `.env` reste prioritaire si vous souhaitez forcer une valeur.

4. **Configurer les passerelles de paiement** dans `.env` (jamais dans le code) :
```dotenv
# CinetPay (Orange Money, Moov Money)
CINETPAY_API_KEY=votre_cle_api
CINETPAY_SITE_ID=votre_site_id
CINETPAY_SECRET=votre_secret

# Stripe (Visa, Mastercard)
STRIPE_PUBLIC_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
```
   Détails d'activation, webhooks et sécurité : voir **`docs/PAIEMENT.md`**.

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

Le module de paiement est **« clé en main »** : une interface commune
(`PaymentGatewayInterface`) et des adaptateurs interchangeables sélectionnés
par une fabrique (`PaymentFactory`). Tant qu'aucune passerelle n'est
configurée, l'application bascule automatiquement en **mode démo**
(`DisabledGateway`) — aucun débit réel. Toute la configuration sensible
passe par `.env`.

| Méthode | Service | Adaptateur | Public cible |
|---------|---------|------------|--------------|
| Orange Money | CinetPay | `CinetPayGateway` | Niger, Afrique |
| Moov Money | CinetPay | `CinetPayGateway` | Niger, Afrique |
| Visa | Stripe | `StripeGateway` | International |
| Mastercard | Stripe | `StripeGateway` | International |

Sécurité intégrée : aucune donnée de carte stockée (tokenisation PSP),
vérification de signature des webhooks, ré-vérification côté serveur,
idempotence/anti-rejeu, devise `XOF`. **Guide complet : `docs/PAIEMENT.md`.**

### À obtenir
1. **CinetPay** : https://cinetpay.com - Créer un compte marchand
2. **Stripe** : https://stripe.com - Créer un compte (disponible pour les ONGs)

> ⚠️ Les identifiants bancaires/marchands officiels sont à fournir par le FAJ
> (`TODO: à fournir par le FAJ`). En leur absence, le mode démo reste actif.

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
- Requêtes préparées (PDO) contre l'injection SQL
- Mots de passe hachés (`password_hash`)
- Protection CSRF (jeton double-soumission)
- Validation des entrées, échappement des sorties
- En-têtes de sécurité renforcés (CSP, HSTS, X-Frame-Options…)
- Sessions durcies, secrets hors du dépôt (`.env` gitignoré)
- Limitation de débit (rate limiting) sur les endpoints sensibles

Checklist détaillée (en place / à finaliser avant production) : **`docs/SECURITE.md`**.

---
*Développé pour le Fonds d'Appui à la Justice du Niger - FAJ*
