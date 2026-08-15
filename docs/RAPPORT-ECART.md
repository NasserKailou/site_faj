# Rapport d'écart — Site FAJ (Fonds d'Appui à la Justice)

**Projet :** Site institutionnel du Fonds d'Appui à la Justice (FAJ)
**Branche de travail :** `ak_main` (dérivée de `main`, non fusionnée — laissée pour revue)
**Cadre :** amélioration sur l'existant, sans refonte d'architecture
**Date :** 2026-08-15

> Ce document recense l'état initial du dépôt, l'inventaire du contenu factice,
> les écarts par rapport aux exigences, les corrections apportées et ce qui
> reste à faire. Règle appliquée : **aucun contenu supprimé sans remplacement**.

---

## 1. Audit de l'existant

### 1.1 Pile technique et architecture (inchangées)
- **Langage :** PHP 8.x « pur » (sans framework).
- **Accès données :** PDO — MySQL en primaire, **repli automatique sur SQLite**
  (`faj_data.sqlite`) avec initialisation/seed au premier lancement.
- **Routage :** `router.php` (URLs propres avec le serveur PHP intégré) +
  `.htaccess` (Apache, `RewriteBase /site_faj/`).
- **Gabarits :** `includes/config.php` (config + sécurité + helpers DB),
  `includes/header.php`, `includes/footer.php`.
- **Front :** `assets/css/style.css`, `assets/js/main.js`, CDN (Font Awesome,
  AOS, Swiper).
- **Back-office :** `admin/` (auth session, CRUD dons/projets/actualités…).

**Conclusion d'audit :** architecture cohérente et maintenable. Toutes les
améliorations ont été faites *par-dessus* (aucune modification de la structure
des dossiers, du système de gabarits, de la base ni des conventions de nommage).

### 1.2 Points faibles identifiés à l'entrée
| # | Constat | Sévérité |
|---|---------|----------|
| A | Secrets de paiement **en dur** dans `config.php` (clés API CinetPay/Stripe) | Critique |
| B | `SITE_URL` figée (`http://localhost:3000`) → CSS/JS cassés hors de ce contexte | Élevée |
| C | Contenu **factice** (partenaires, montants, équipe fictive) | Élevée |
| D | Intégration paiement procédurale, non abstraite, difficile à activer/tester | Moyenne |
| E | En-têtes de sécurité partiels (CSP permissive), pas de HSTS | Moyenne |
| F | SEO incomplet (pas de schema.org, sitemap, robots.txt) | Moyenne |
| G | Absence de vues Gouvernance / Cadre financier | Moyenne |

---

## 2. Inventaire du contenu factice (placeholder)

| Emplacement | Contenu factice initial | Traitement |
|-------------|-------------------------|------------|
| `index.php` | Partenaires PNUD / UE / Banque Mondiale (non confirmés) | Remplacé par « Pourquoi s'engager » + CTA partenaires |
| `index.php` | Montants de dons chiffrés fictifs sur les projets | Remplacés par les **projets phares officiels** (sans montants inventés) |
| `index.php` / hero | « 8 régions » | Remplacé par « 6 Domaines prioritaires » |
| `pages/a-propos.php` | Équipe nominative fictive | Remplacée par le **Conseil d'Administration** (5 membres institutionnels) |
| `pages/a-propos.php` | 6 axes génériques | Remplacés par les **6 domaines d'intervention officiels** |
| seed SQLite (`config.php`) | Témoignages / paramètres génériques | Remplacés par les données officielles FAJ |
| Logo, photos projets, coordonnées bancaires | — | **Placeholders explicites** `TODO: à fournir par le FAJ` |

> Les éléments réellement manquants (logo officiel, photos, identifiants
> marchands) restent marqués `TODO: à fournir par le FAJ` — jamais inventés.

---

## 3. Matrice des écarts (exigences ↔ état)

Légende : ✅ Réalisé · 🟡 Partiel · ⏳ À faire · 🔒 Bloqué (données FAJ requises)

### 3.1 Contenu institutionnel
| Exigence | État |
|----------|------|
| Identité (nom, sigle, slogan, décret 2023-113/PRN/MJ) | ✅ |
| Coordonnées officielles (adresse, email, téléphones) | ✅ |
| Mission (3 axes), Vision Horizon 2035 | ✅ |
| 4 valeurs, 6 domaines d'intervention | ✅ |
| Gouvernance (Conseil d'Administration, 5 membres) | ✅ |
| Cadre financier + clé de répartition (Arrêté MF/MJ 00011, 30/30/10) | ✅ |
| Projets phares (PPP-FAJ, Kotou, Alkali, TGI Niamey, TA) | ✅ (sans montants inventés) |
| Logo officiel & photos réelles des projets | 🔒 `TODO FAJ` |

### 3.2 Design / UX / Accessibilité
| Exigence | État |
|----------|------|
| Palette institutionnelle (bleu/vert justice + or/sable) | ✅ |
| En-tête avec logo + slogan, pied de page complet | ✅ |
| Hero avec Vision 2035, cartes domaines & valeurs | ✅ |
| Sections projets phares & partenaires (CTA) | ✅ |
| Responsive mobile-first | ✅ |
| WCAG 2.1 AA (contrastes, sémantique, focus) | 🟡 (audit final recommandé) |

### 3.3 SEO
| Exigence | État |
|----------|------|
| Meta (title/description), Open Graph, canonical | ✅ |
| schema.org `GovernmentOrganization` (JSON-LD) | ✅ |
| `sitemap.xml` (dynamique) + `robots.txt` | ✅ |
| Mentions légales | ✅ (page existante) |

### 3.4 Module de paiement « clé en main »
| Exigence | État |
|----------|------|
| `PaymentGatewayInterface` (initiate/verify/callback/refund) | ✅ |
| Adaptateurs concrets + mode démo/désactivé | ✅ (CinetPay, Stripe, Disabled) |
| Config sensible en `.env` + `.env.example` + `.env` gitignoré | ✅ |
| Devise `XOF`, pas de stockage de carte (tokenisation PSP) | ✅ |
| Vérification signature webhook, idempotence/anti-rejeu | ✅ |
| PCI-DSS SAQ-A, `docs/PAIEMENT.md` | ✅ |
| Identifiants marchands réels (CinetPay/Stripe) | 🔒 `TODO FAJ` |

### 3.5 Sécurité (OWASP)
| Exigence | État |
|----------|------|
| Requêtes préparées, validation entrées, échappement sorties | ✅ |
| CSP + en-têtes de sécurité, HSTS | ✅ |
| Jetons CSRF, `password_hash`, sessions durcies | ✅ |
| Rate limiting | ✅ (endpoints sensibles) |
| Secrets hors dépôt | ✅ |
| RBAC, 2FA admin | 🟡 (RBAC de base ; 2FA à finaliser) |
| HTTPS obligatoire en production, `composer audit` | ⏳ (à activer au déploiement ; pas de Composer) |
| `docs/SECURITE.md` | ✅ |

---

## 4. Corrections & apports de cette itération

1. **Correctif chargement CSS/JS** — auto-détection de `SITE_URL`
   (`fajDetectBaseUrl()`) : schéma, hôte:port et sous-dossier via `SCRIPT_NAME`
   (repli `DOCUMENT_ROOT`). Le site se style correctement sous XAMPP
   (`/site_faj`), en port dédié et en production, `.env` restant prioritaire.
2. **Vues institutionnelles** — `pages/gouvernance.php` et
   `pages/cadre-financier.php` (routes, menu déroulant « À Propos », footer,
   sitemap, CSS dédié).
3. **Documentation** — README aligné sur `.env` et le module de paiement ;
   `docs/PAIEMENT.md`, `docs/SECURITE.md`, et le présent rapport.

---

## 5. Reste à faire (non bloquant)

| Priorité | Tâche |
|----------|-------|
| Moyenne | Enrichir le design des pages secondaires (`projets.php`, `contact.php`, `don.php`) |
| Moyenne | Finaliser 2FA admin et affiner le RBAC |
| Moyenne | Audit d'accessibilité WCAG 2.1 AA complet |
| Faible | Activer HSTS/HTTPS et durcissement en production |
| 🔒 FAJ | Fournir : logo officiel, photos projets, identifiants marchands, montants |

---

## 6. Recommandations de revue avant fusion

- Vérifier `ak_main` en environnement cible (XAMPP `/site_faj`) : styles + liens.
- Renseigner `.env` (copie de `.env.example`) avec les valeurs réelles du FAJ.
- Conserver `PAYMENT_MODE=sandbox` tant que les identifiants marchands ne sont
  pas fournis (le mode démo empêche tout débit réel).
- **Ne pas fusionner `ak_main` vers `main`** sans revue — conformément à la
  consigne du projet.
