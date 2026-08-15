# Sécurité applicative FAJ — Checklist de mise en production (OWASP)

## Déjà en place
- [x] Requêtes préparées PDO (aucune concaténation SQL).
- [x] CSRF (jeton par formulaire, double-submit) — `csrfField()` / `verifyCsrfToken()`.
- [x] Sanitisation entrées / échappement sortie (`sanitize`, `htmlspecialchars`).
- [x] En-têtes de sécurité : CSP (sans unsafe-eval), X-Frame-Options, X-Content-Type-Options,
      Referrer-Policy, Permissions-Policy, HSTS (HTTPS).
- [x] Sessions durcies : HttpOnly, SameSite=Strict, Secure auto en HTTPS, régénération, expiration, fingerprint.
- [x] Anti-brute-force login + rate limiting.
- [x] Mots de passe hashés (bcrypt/argon via `password_hash`).
- [x] Secrets hors dépôt (`.env` gitignoré) + `.env.example`.
- [x] Erreurs masquées en prod (DEBUG_MODE=false) + pages 404/500.
- [x] Webhooks : signature + idempotence + anti-rejeu.

## À finaliser avant mise en production
- [ ] `APP_ENV=production`, `DEBUG_MODE=false`, `SESSION_COOKIE_SECURE=true` dans `.env`.
- [ ] Changer `ADMIN_SECRET` et le mot de passe admin par défaut.
- [ ] HTTPS obligatoire (redirection 301 HTTP→HTTPS).
- [ ] `composer audit` (si composer.json ajouté) / veille dépendances.
- [ ] Uploads : validation MIME/extension, hors webroot idéalement.
- [ ] 2FA admin (recommandé).
- [ ] Sauvegardes régulières BD + rotation des secrets.
- [ ] Journalisation de sécurité sans données sensibles.
