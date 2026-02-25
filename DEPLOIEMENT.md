# Guide de déploiement — Site FAJ Niger

## ✅ Checklist avant déploiement

### 1. Modifier `includes/config.php`

```php
// LOCAL XAMPP
define('SITE_URL', 'http://localhost:8085/site_faj');

// PRODUCTION (remplacer par votre vrai domaine)
define('SITE_URL', 'https://www.faj.ne');
```

### 2. Modifier `.htaccess`

```apache
# LOCAL XAMPP (sous-dossier)
RewriteBase /site_faj/

# PRODUCTION (domaine racine)
RewriteBase /
```

Et adapter les pages d'erreur :
```apache
# LOCAL
ErrorDocument 404 /site_faj/pages/404.php

# PRODUCTION
ErrorDocument 404 /pages/404.php
```

### 3. Base de données MySQL

Importer `database.sql` dans votre serveur MySQL de production.

Puis exécuter le patch mot de passe si nécessaire :
```sql
UPDATE admins
SET mot_de_passe = '$2y$12$LjpqZHL7iq1U7SdycSbUXO7FwI4ATM3gWCp29eNJC9pLRrkSbJoQy'
WHERE email = 'admin@faj.ne';
```

Identifiants admin : `admin@faj.ne` / `Admin@FAJ2024!`

### 4. Paramètres du serveur Apache (hébergeur mutualisé / VPS)

Vérifier dans le panneau de contrôle (cPanel, Plesk, etc.) que :
- `mod_rewrite` est **activé**
- `AllowOverride All` est configuré pour votre répertoire
- PHP 8.x est actif

### 5. Permissions des fichiers

```bash
chmod 755 uploads/
chmod 755 uploads/projets/
chmod 755 uploads/actualites/
chmod 755 uploads/equipe/
chmod 644 .htaccess
chmod 600 includes/config.php
```

### 6. HTTPS en production

Dans `.htaccess`, décommenter :
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Et dans `includes/config.php` :
```php
ini_set('session.cookie_secure', 1);  // Mettre 1 en production HTTPS
```

Et décommenter dans `.htaccess` :
```apache
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

---

## Structure des URLs

| URL propre | Fichier PHP réel |
|---|---|
| `/` | `index.php` |
| `/a-propos` | `pages/a-propos.php` |
| `/projets` | `pages/projets.php` |
| `/projets/{slug}` | `pages/projet-detail.php?slug={slug}` |
| `/actualites` | `pages/actualites.php` |
| `/actualites/{slug}` | `pages/actualite-detail.php?slug={slug}` |
| `/contact` | `pages/contact.php` |
| `/don` | `pages/don.php` |
| `/equipe` | `pages/equipe.php` |
| `/faq` | `pages/faq.php` |
| `/admin/login` | `admin/login.php` |
| `/admin/dashboard` | `admin/dashboard.php` |
| `/admin/dons` | `admin/dons/liste.php` |
| `/admin/projets` | `admin/projets/liste.php` |
| `/admin/actualites` | `admin/actualites/liste.php` |
| `/admin/equipe` | `admin/equipe/liste.php` |
| `/admin/temoignages` | `admin/temoignages/liste.php` |
| `/admin/contacts` | `admin/contacts/liste.php` |
| `/admin/parametres` | `admin/parametres.php` |

---

## Récapitulatif des différences LOCAL vs PRODUCTION

| Paramètre | Local XAMPP | Production |
|---|---|---|
| `SITE_URL` | `http://localhost:8085/site_faj` | `https://www.faj.ne` |
| `RewriteBase` | `/site_faj/` | `/` |
| `ErrorDocument` | `/site_faj/pages/xxx.php` | `/pages/xxx.php` |
| HTTPS redirect | Désactivé | Activé |
| HSTS header | Désactivé | Activé |
| `session.cookie_secure` | `0` | `1` |
| `DEBUG_MODE` | `false` (conseillé `true` en dev) | `false` |
