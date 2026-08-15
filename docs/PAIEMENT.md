# Module de paiement FAJ — Guide d'activation

Le module de paiement est **clé-en-main** : il suffit de renseigner les identifiants
réels dans `.env` pour passer du mode démo au mode réel.

## Architecture
- `includes/payment/PaymentGatewayInterface.php` — contrat commun (initiatePayment, verifyPayment, handleCallback, refund).
- `includes/payment/CinetPayGateway.php` — Mobile Money (Orange/Moov) & cartes, zone UEMOA (XOF).
- `includes/payment/StripeGateway.php` — cartes Visa/Mastercard (PCI-DSS SAQ-A).
- `includes/payment/DisabledGateway.php` — mode démo (aucun débit) si rien n'est configuré.
- `includes/payment/PaymentFactory.php` — sélection auto de la passerelle selon la méthode.

## Étapes d'activation
1. Copier `.env.example` → `.env`.
2. Renseigner **selon la banque/PSP** :
   - CinetPay : `CINETPAY_API_KEY`, `CINETPAY_SITE_ID`, `CINETPAY_SECRET_KEY`.
   - Stripe : `STRIPE_SECRET_KEY`, `STRIPE_PUBLIC_KEY`, `STRIPE_WEBHOOK_SECRET`.
3. Passer `PAYMENT_MODE=sandbox` pour tester, puis `PAYMENT_MODE=live` en production.
4. `CURRENCY=XOF` (Franc CFA) par défaut.

## Webhooks à déclarer chez le PSP
- CinetPay : `https://www.faj.ne/api/webhook-cinetpay`
- Stripe :   `https://www.faj.ne/api/webhook-stripe`

## Sécurité
- Aucune donnée de carte stockée (redirection/tokenisation PSP).
- Vérification de signature des webhooks + idempotence + anti-rejeu.
- Statut/montant re-vérifiés côté serveur (jamais confiance au seul payload).

> TODO : identifiants bancaires réels à fournir par le FAJ.
