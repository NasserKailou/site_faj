<?php
/**
 * Classe de base commune aux passerelles de paiement.
 * ---------------------------------------------------------------------------
 * Fournit les utilitaires partagés : lecture de configuration (.env),
 * client HTTP cURL sécurisé, journalisation. Les adaptateurs concrets en
 * héritent pour éviter la duplication.
 *
 * @package FAJ\Includes\Payment
 */

declare(strict_types=1);

namespace FAJ\Payment;

abstract class AbstractGateway implements PaymentGatewayInterface
{
    /** Mode : 'sandbox' | 'live' | 'disabled'. */
    protected string $mode;

    public function __construct()
    {
        $this->mode = strtolower((string) (function_exists('env') ? env('PAYMENT_MODE', 'sandbox') : 'sandbox'));
    }

    public function isSandbox(): bool
    {
        return $this->mode === 'sandbox';
    }

    /**
     * Effectue une requête HTTP sécurisée via cURL.
     *
     * @param array $options  Options : method, headers, body, basic_auth, timeout
     * @return array{status:int, body:string, data:array, error:string}
     */
    protected function httpRequest(string $url, array $options = []): array
    {
        $method  = strtoupper($options['method'] ?? 'POST');
        $headers = $options['headers'] ?? [];
        $body    = $options['body'] ?? null;
        $timeout = (int) ($options['timeout'] ?? 30);

        $ch = curl_init($url);
        $curlOpts = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,   // Sécurité : vérification TLS obligatoire
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CUSTOMREQUEST  => $method,
        ];

        if ($body !== null) {
            $curlOpts[CURLOPT_POSTFIELDS] = $body;
        }
        if (!empty($options['basic_auth'])) {
            $curlOpts[CURLOPT_USERPWD] = $options['basic_auth'];
        }

        curl_setopt_array($ch, $curlOpts);

        $response = curl_exec($ch);
        $status   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        $data = [];
        if (is_string($response) && $response !== '') {
            $decoded = json_decode($response, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        return [
            'status' => $status,
            'body'   => is_string($response) ? $response : '',
            'data'   => $data,
            'error'  => $error,
        ];
    }

    /**
     * Journalise un événement de paiement sans exposer de données sensibles.
     */
    protected function log(string $message, array $context = []): void
    {
        // Ne jamais journaliser de secrets / données de carte.
        $safe = array_intersect_key($context, array_flip(['reference', 'status', 'transaction_id', 'amount']));
        $line = sprintf('[FAJ Payment][%s] %s %s', $this->getName(), $message, $safe ? json_encode($safe) : '');
        error_log($line);
    }

    /**
     * Retourne l'URL absolue du site (depuis constante ou .env).
     */
    protected function siteUrl(): string
    {
        if (defined('SITE_URL')) {
            return rtrim((string) SITE_URL, '/');
        }
        return rtrim((string) (function_exists('env') ? env('SITE_URL', '') : ''), '/');
    }

    /**
     * Par défaut : opération non supportée. Les adaptateurs concrets
     * surchargent selon les capacités de la passerelle.
     */
    public function refund(string $transactionId, ?int $amount = null): PaymentResult
    {
        return PaymentResult::failure('Remboursement non supporté par cette passerelle.');
    }
}
