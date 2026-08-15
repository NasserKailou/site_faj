<?php
/**
 * Adaptateur CinetPay — Mobile Money (Orange Money, Moov Money) & cartes.
 * ---------------------------------------------------------------------------
 * Agrégateur largement utilisé en zone UEMOA (devise XOF). Aucune donnée de
 * carte ne transite par nos serveurs : le donateur est redirigé vers la page
 * de paiement hébergée par CinetPay.
 *
 * Configuration requise (.env) :
 *   PAYMENT_MODE=sandbox|live
 *   CINETPAY_API_KEY=...
 *   CINETPAY_SITE_ID=...
 *   CINETPAY_SECRET_KEY=...   (pour la vérification HMAC des webhooks)
 *
 * Réf. API : https://docs.cinetpay.com/
 *
 * @package FAJ\Includes\Payment
 */

declare(strict_types=1);

namespace FAJ\Payment;

final class CinetPayGateway extends AbstractGateway
{
    private string $apiKey;
    private string $siteId;
    private string $secretKey;
    private string $baseUrl;
    private string $verifyUrl;

    public function __construct()
    {
        parent::__construct();
        $this->apiKey    = (string) env('CINETPAY_API_KEY', '');
        $this->siteId    = (string) env('CINETPAY_SITE_ID', '');
        $this->secretKey = (string) env('CINETPAY_SECRET_KEY', '');
        $this->baseUrl   = (string) env('CINETPAY_BASE_URL', 'https://api-checkout.cinetpay.com/v2/payment');
        $this->verifyUrl = (string) env('CINETPAY_VERIFY_URL', 'https://api-checkout.cinetpay.com/v2/payment/check');
    }

    public function getName(): string
    {
        return 'cinetpay';
    }

    public function isEnabled(): bool
    {
        return $this->apiKey !== '' && $this->siteId !== ''
            && stripos($this->apiKey, 'VOTRE_') === false;
    }

    public function initiatePayment(array $payment): PaymentResult
    {
        if (!$this->isEnabled()) {
            return PaymentResult::failure('Passerelle CinetPay non configurée.', (string) ($payment['reference'] ?? ''));
        }

        $reference = (string) $payment['reference'];
        $customer  = $payment['customer'] ?? [];
        $channel   = $payment['channel'] ?? 'ALL'; // ALL | MOBILE_MONEY | CREDIT_CARD

        $data = [
            'apikey'                => $this->apiKey,
            'site_id'               => $this->siteId,
            'transaction_id'        => $reference,
            'amount'                => (int) $payment['amount'],
            'currency'              => (string) ($payment['currency'] ?? 'XOF'),
            'description'           => (string) ($payment['description'] ?? ('Don FAJ Niger - ' . $reference)),
            'notify_url'            => $this->siteUrl() . '/api/webhook-cinetpay',
            'return_url'            => $this->siteUrl() . '/don-succes?ref=' . urlencode($reference),
            'channels'              => $channel,
            'customer_name'         => (string) ($customer['name'] ?? ''),
            'customer_email'        => (string) ($customer['email'] ?? ''),
            'customer_phone_number' => (string) ($customer['phone'] ?? ''),
            'metadata'              => json_encode($payment['metadata'] ?? [], JSON_UNESCAPED_UNICODE),
        ];

        $res = $this->httpRequest($this->baseUrl, [
            'method'  => 'POST',
            'headers' => ['Content-Type: application/json'],
            'body'    => json_encode($data, JSON_UNESCAPED_UNICODE),
        ]);

        $url = $res['data']['data']['payment_url'] ?? null;
        if ($url) {
            $this->log('Paiement initié', ['reference' => $reference, 'status' => 'en_attente']);
            return PaymentResult::success($reference, $url, null, 'en_attente', 'Redirection CinetPay', $res['data']);
        }

        $msg = $res['data']['message'] ?? ($res['error'] ?: 'Réponse invalide de CinetPay');
        return PaymentResult::failure('CinetPay : ' . $msg, $reference, $res['data']);
    }

    public function verifyPayment(string $reference): PaymentResult
    {
        if (!$this->isEnabled()) {
            return PaymentResult::failure('Passerelle CinetPay non configurée.', $reference);
        }

        $res = $this->httpRequest($this->verifyUrl, [
            'method'  => 'POST',
            'headers' => ['Content-Type: application/json'],
            'body'    => json_encode([
                'apikey'         => $this->apiKey,
                'site_id'        => $this->siteId,
                'transaction_id' => $reference,
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $code   = $res['data']['data']['status'] ?? ($res['data']['code'] ?? '');
        $txId   = $res['data']['data']['payment_token'] ?? null;
        $status = $this->mapStatus((string) $code);

        return new PaymentResult(
            $status === 'complete',
            $reference,
            null,
            $txId,
            $status,
            (string) ($res['data']['message'] ?? ''),
            $res['data']
        );
    }

    public function handleCallback(array $payload, array $headers = [], string $rawBody = ''): PaymentResult
    {
        // 1) Vérification de la signature HMAC (anti-usurpation).
        if ($this->secretKey !== '') {
            $received = $headers['x-token'] ?? $headers['X-TOKEN'] ?? '';
            $expected = hash_hmac('sha256', $rawBody, $this->secretKey);
            if ($received === '' || !hash_equals($expected, (string) $received)) {
                $this->log('Signature webhook invalide (rejet)');
                return PaymentResult::failure('Signature webhook invalide.');
            }
        }

        $reference = (string) ($payload['cpm_trans_id'] ?? ($payload['transaction_id'] ?? ''));
        if ($reference === '') {
            return PaymentResult::failure('Référence de transaction absente.');
        }

        // 2) TOUJOURS re-vérifier le statut côté serveur (ne jamais faire
        //    confiance au seul payload du webhook).
        return $this->verifyPayment($reference);
    }

    public function refund(string $transactionId, ?int $amount = null): PaymentResult
    {
        // TODO: à fournir par le FAJ — l'API de remboursement CinetPay nécessite
        // un accord marchand spécifique. Laisser désactivé tant que non validé.
        return PaymentResult::failure('Remboursement CinetPay non activé (à configurer avec la banque).');
    }

    private function mapStatus(string $code): string
    {
        $code = strtoupper($code);
        return match ($code) {
            'ACCEPTED', '00', 'SUCCES', 'SUCCESS' => 'complete',
            'REFUSED', '600', 'ECHEC', 'FAILED'    => 'echec',
            'CANCELLED', 'ANNULE'                  => 'annule',
            default                                 => 'en_attente',
        };
    }
}
