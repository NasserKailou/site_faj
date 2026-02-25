# GUIDE DÉPLOIEMENT — Site FAJ Niger
## Procédure complète : XAMPP local → Production

---

## ✅ CHECKLIST AVANT DÉPLOIEMENT

### 1. Fichiers à modifier pour la production

#### `includes/config.php`
```php
// XAMPP local
define('SITE_URL', 'http://localhost:8085/site_faj');

// ↓ Production (remplacer par votre vrai domaine)
define('SITE_URL', 'https://www.faj.ne');
```

#### `.htaccess`
```apache
# XAMPP local → sous-dossier
RewriteBase /site_faj/

# ↓ Production → domaine racine
RewriteBase /
```
> Si le site est dans un sous-dossier en prod (ex: `/faj/`), mettre `RewriteBase /faj/`

---

### 2. Prérequis serveur de production

| Requis | Détail |
|--------|--------|
| PHP 8.0+ | Avec extensions : `pdo`, `pdo_mysql`, `mbstring`, `openssl` |
| Apache 2.4+ | Modules activés : `mod_rewrite`, `mod_headers`, `mod_deflate` |
| MySQL 5.7+ | Ou MariaDB 10.4+ |
| HTTPS | Certificat SSL (Let's Encrypt gratuit) |
| `.htaccess` | `AllowOverride All` dans la config Apache |

---

### 3. Étapes de déploiement

#### ÉTAPE 1 — Préparer les fichiers
```bash
# Sur votre machine, modifier config.php :
define('SITE_URL', 'https://www.faj.ne');
define('DB_HOST',  'localhost');
define('DB_NAME',  'faj_niger');
define('DB_USER',  'votre_user_mysql');
define('DB_PASS',  'votre_mot_de_passe');

# Modifier .htaccess :
RewriteBase /
```

#### ÉTAPE 2 — Créer la base de données
```sql
-- Dans phpMyAdmin ou en ligne de commande :
CREATE DATABASE faj_niger CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Puis importer database.sql
```

#### ÉTAPE 3 — Uploader les fichiers
Via FTP (FileZilla) ou panel d'hébergement :
```
Uploader TOUT le contenu du dossier site_faj/
vers le dossier public_html/ (ou www/ ou httpdocs/)
```

#### ÉTAPE 4 — Vérifier les permissions
```bash
chmod 755 uploads/
chmod 755 uploads/actualites/
chmod 755 uploads/projets/
chmod 755 uploads/equipe/
chmod 644 includes/config.php
chmod 644 .htaccess
```

#### ÉTAPE 5 — Activer les erreurs (temporairement pour le debug)
Dans `includes/config.php` :
```php
define('DEBUG_MODE', true); // Remettre false après vérification !
```

#### ÉTAPE 6 — Tester toutes les URLs
```
https://www.faj.ne/                          ✓ Accueil
https://www.faj.ne/a-propos                  ✓ À propos
https://www.faj.ne/projets                   ✓ Projets
https://www.faj.ne/actualites                ✓ Actualités
https://www.faj.ne/contact                   ✓ Contact
https://www.faj.ne/don                       ✓ Don
https://www.faj.ne/equipe                    ✓ Équipe
https://www.faj.ne/faq                       ✓ FAQ
https://www.faj.ne/admin/login               ✓ Admin login
https://www.faj.ne/admin/dashboard           ✓ Admin tableau de bord
```

#### ÉTAPE 7 — Désactiver le mode debug
```php
define('DEBUG_MODE', false);
```

---

## 🔐 Sécurité en production

### Dans `.htaccess`, décommenter HTTPS :
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Dans `includes/config.php`, activer le cookie sécurisé :
```php
ini_set('session.cookie_secure', 1);  // Mettre 1 en production HTTPS
```

### Dans `.htaccess`, activer HSTS :
```apache
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

---

## 🔧 Résolution des problèmes fréquents

### Problème : 404 sur toutes les pages sauf l'accueil
**Cause :** `mod_rewrite` désactivé ou `AllowOverride` mal configuré  
**Solution :**
1. Activer `mod_rewrite` dans `httpd.conf` : `LoadModule rewrite_module modules/mod_rewrite.so`
2. Vérifier `AllowOverride All` dans le bloc `<Directory>` de votre vhost
3. Vérifier que `RewriteBase` correspond à l'emplacement du site

### Problème : RewriteBase à adapter selon l'emplacement
| Situation | RewriteBase |
|-----------|-------------|
| XAMPP local dans `/site_faj/` | `RewriteBase /site_faj/` |
| Production domaine racine | `RewriteBase /` |
| Production sous-dossier `/faj/` | `RewriteBase /faj/` |

### Problème : Erreur de connexion BDD
- Vérifier `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` dans `config.php`
- Si pas de MySQL → SQLite s'active automatiquement (fallback)

### Problème : Images non affichées
- Vérifier que le dossier `uploads/` a les permissions 755
- Vérifier que `UPLOADS_URL` pointe vers la bonne URL

---

## 📋 Identifiants par défaut
| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Super Admin | `admin@faj.ne` | `Admin@FAJ2024!` |

> ⚠️ **Changer le mot de passe en production via l'interface admin > Paramètres**
