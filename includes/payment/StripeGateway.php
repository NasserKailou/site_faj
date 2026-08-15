<?php
/**
 * Adaptateur Stripe — Cartes Visa / Mastercard (international).
 * ---------------------------------------------------------------------------
 * Utilise Stripe Checkout (page hébergée par Stripe). Aucune donnée de carte
 * ne transite par nos serveurs → conformité PCI-DSS SAQ-A.
 *
 * Configuration requise (.env) :
 *   PAYMENT_MODE=sandbox|live
 *   STRIPE_SECRET_KEY=sk_live_... | sk_test_...
 *   STRIPE_PUBLIC_KEY=pk_live_... | pk_test_...
 *   STRIPE_WEBHOOK_SECRET=whsec_...   (vérification de signature)
 *
 * Réf. API : https://stripe.com/docs/api
 *
 * @package FAJ\Includes\Payment
 */

declare(strict_types=1);

namespace FAJ\Payment;

final class StripeGateway extends AbstractGateway
{
    private string $secretKey;
    private string $webhookSecret;
    private const API = 'https://api.stripe.com/v1';

    /** Devises « zéro décimale » selon Stripe (le montant est déjà l'unité). */
    private const ZERO_DECIMAL = ['xof', 'xaf', 'bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xpf'];

    public function __construct()
    {
        parent::__construct();
        $this->secretKey     = (string) env('STRIPE_SECRET_KEY', '');
        $this->webhookSecret = (string) env('STRIPE_WEBHOOK_SECRET', '');
    }

    public function getName(): string
    {
        return 'stripe';
    }

    public function isEnabled(): bool
    {
        return $this->secretKey !== ''
            && (str_starts_with($this->secretKey, 'sk_live_') || str_starts_with($this->secretKey, 'sk_test_'));
    }

    public function initiatePayment(array $payment): PaymentResult
    {
        if (!$this->isEnabled()) {
            return PaymentResult::failure('Passerelle Stripe non configurée.', (string) ($payment['reference'] ?? ''));
        }

        $reference = (string) $payment['reference'];
        $currency  = strtolower((string) ($payment['currency'] ?? 'XOF'));
        $amount    = (int) $payment['amount'];
        // Stripe attend les décimales pour les devises non « zéro décimale ».
        if (!in_array($currency, self::ZERO_DECIMAL, true)) {
            $amount *= 100;
        }

        $fields = [
            'mode'                                                 => 'payment',
            'success_url'                                          => $this->siteUrl() . '/don-succes?ref=' . urlencode($reference),
            'cancel_url'                                           => $this->siteUrl() . '/don-annule?ref=' . urlencode($reference),
            'customer_email'                                       => (string) ($payment['customer']['email'] ?? ''),
            'client_reference_id'                                  => $reference,
            'metadata[reference]'                                  => $reference,
            'payment_method_types[0]'                             => 'card',
            'line_items[0][quantity]'                             => 1,
            'line_items[0][price_data][currency]'                => $currency,
            'line_items[0][price_data][unit_amount]'             => $amount,
            'line_items[0][price_data][product_data][name]'      => (string) ($payment['description'] ?? 'Don FAJ Niger'),
        ];
        foreach (($payment['metadata'] ?? []) as $k => $v) {
            $fields["metadata[$k]"] = (string) $v;
        }

        $res = $this->httpRequest(self::API . '/checkout/sessions', [
            'method'     => 'POST',
            'body'       => http_build_query($fields),
            'basic_auth' => $this->secretKey . ':',
        ]);

        $url = $res['data']['url'] ?? null;
        $id  = $res['data']['id'] ?? null;
        if ($url) {
            $this->log('Session Checkout créée', ['reference' => $reference, 'status' => 'en_attente']);
            return PaymentResult::success($reference, $url, $id, 'en_attente', 'Redirection Stripe', $res['data']);
        }

        $msg = $res['data']['error']['message'] ?? ($res['error'] ?: 'Réponse invalide de Stripe');
        return PaymentResult::failure('Stripe : ' . $msg, $reference, $res['data']);
    }

    public function verifyPayment(string $reference): PaymentResult
    {
        if (!$this->isEnabled()) {
            return PaymentResult::failure('Passerelle Stripe non configurée.', $reference);
        }

        // Rechercher la session via client_reference_id.
        $res = $this->httpRequest(
            self::API . '/checkout/sessions?limit=1&client_reference_id=' . urlencode($reference),
            ['method' => 'GET', 'basic_auth' => $this->secretKey . ':']
        );

        $session = $res['data']['data'][0] ?? null;
        if (!$session) {
            return new PaymentResult(false, $reference, null, null, 'en_attente', 'Session introuvable', $res['data']);
        }

        $status = ($session['payment_status'] ?? '') === 'paid' ? 'complete' : 'en_attente';
        $txId   = $session['payment_intent'] ?? ($session['id'] ?? null);

        return new PaymentResult($status === 'complete', $reference, null, $txId, $status, 'OK', $session);
    }

    public function handleCallback(array $payload, array $headers = [], string $rawBody = ''): PaymentResult
    {
        // Vérification de la signature Stripe (t=..,v1=..).
        if ($this->webhookSecret !== '') {
            $sigHeader = $headers['stripe-signature'] ?? $headers['Stripe-Signature'] ?? '';
            if (!$this->verifySignature($rawBody, (string) $sigHeader)) {
                $this->log('Signature webhook Stripe invalide (rejet)');
                return PaymentResult::failure('Signature webhook invalide.');
            }
        }

        $type      = $payload['type'] ?? '';
        $object    = $payload['data']['object'] ?? [];
        $reference = $object['client_reference_id'] ?? ($object['metadata']['reference'] ?? '');

        if ($type === 'checkout.session.completed' && ($object['payment_status'] ?? '') === 'paid') {
            $txId = $object['payment_intent'] ?? ($object['id'] ?? null);
            return new PaymentResult(true, (string) $reference, null, $txId, 'complete', 'Paiement confirmé', $object);
        }

        return new PaymentResult(false, (string) $reference, null, null, 'en_attente', 'Événement ignoré', $object);
    }

    public function refund(string $transactionId, ?int $amount = null): PaymentResult
    {
        if (!$this->isEnabled()) {
            return PaymentResult::failure('Passerelle Stripe non configurée.');
        }
        $fields = ['payment_intent' => $transactionId];
        if ($amount !== null) {
            $fields['amount'] = $amount;
        }
        $res = $this->httpRequest(self::API . '/refunds', [
            'method'     => 'POST',
            'body'       => http_build_query($fields),
            'basic_auth' => $this->secretKey . ':',
        ]);
        if (($res['data']['status'] ?? '') === 'succeeded') {
            return new PaymentResult(true, '', null, $res['data']['id'] ?? null, 'rembourse', 'Remboursement effectué', $res['data']);
        }
        return PaymentResult::failure('Stripe refund : ' . ($res['data']['error']['message'] ?? 'échec'), '', $res['data']);
    }

    /**
     * Vérifie la signature d'un webhook Stripe (schéma t=timestamp,v1=hmac).
     */
    private function verifySignature(string $payload, string $sigHeader): bool
    {
        $parts = [];
        foreach (explode(',', $sigHeader) as $pair) {
            $kv = explode('=', $pair, 2);
            if (count($kv) === 2) {
                $parts[trim($kv[0])] = trim($kv[1]);
            }
        }
        if (empty($parts['t']) || empty($parts['v1'])) {
            return false;
        }
        // Tolérance anti-rejeu : 5 minutes.
        if (abs(time() - (int) $parts['t']) > 300) {
            return false;
        }
        $signedPayload = $parts['t'] . '.' . $payload;
        $expected      = hash_hmac('sha256', $signedPayload, $this->webhookSecret);
        return hash_equals($expected, $parts['v1']);
    }
}
