<?php
/**
 * Contrat commun à toutes les passerelles de paiement du FAJ.
 * ---------------------------------------------------------------------------
 * Toute intégration (CinetPay, Stripe, PSP bancaire, Mobile Money…) DOIT
 * implémenter cette interface. Le reste de l'application ne dépend que de ce
 * contrat, ce qui permet d'ajouter/retirer une passerelle sans toucher au
 * code métier.
 *
 * @package FAJ\Includes\Payment
 */

declare(strict_types=1);

namespace FAJ\Payment;

interface PaymentGatewayInterface
{
    /**
     * Identifiant technique de la passerelle (ex : 'cinetpay', 'stripe').
     */
    public function getName(): string;

    /**
     * Indique si la passerelle est configurée et prête à traiter des paiements
     * réels. Retourne false tant que les identifiants ne sont pas renseignés.
     */
    public function isEnabled(): bool;

    /**
     * Initie un paiement.
     *
     * @param array $payment Données du paiement. Clés attendues :
     *   - reference   (string) Référence interne unique du don
     *   - amount      (int)    Montant en plus petite unité de la devise
     *   - currency    (string) Devise ISO (XOF par défaut)
     *   - description (string) Libellé affiché au donateur
     *   - customer    (array)  ['name','email','phone','country']
     *   - metadata    (array)  Données libres (don_id, projet_id…)
     *   - channel     (string) Canal spécifique (ORANGE_MONEY, MOOV_MONEY…)
     */
    public function initiatePayment(array $payment): PaymentResult;

    /**
     * Vérifie l'état d'un paiement auprès de la passerelle (source de vérité).
     *
     * @param string $reference Référence interne du don
     */
    public function verifyPayment(string $reference): PaymentResult;

    /**
     * Traite une notification serveur-à-serveur (webhook/callback).
     * DOIT valider la signature/authenticité avant tout traitement.
     *
     * @param array  $payload  Corps de la requête (déjà décodé)
     * @param array  $headers  En-têtes HTTP (pour vérification de signature)
     * @param string $rawBody  Corps brut (nécessaire à certaines signatures)
     */
    public function handleCallback(array $payload, array $headers = [], string $rawBody = ''): PaymentResult;

    /**
     * Rembourse un paiement (total ou partiel).
     *
     * @param string   $transactionId Identifiant de transaction de la passerelle
     * @param int|null $amount         Montant à rembourser (null = total)
     */
    public function refund(string $transactionId, ?int $amount = null): PaymentResult;
}
