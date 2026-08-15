<?php
/**
 * Fabrique de passerelles de paiement (Factory).
 * ---------------------------------------------------------------------------
 * Sélectionne l'adaptateur concret selon la configuration (.env) ou selon la
 * méthode de paiement choisie par le donateur. Si aucune passerelle réelle
 * n'est configurée / activée, retombe automatiquement sur DisabledGateway
 * (mode démo), garantissant que la plateforme reste fonctionnelle.
 *
 * @package FAJ\Includes\Payment
 */

declare(strict_types=1);

namespace FAJ\Payment;

final class PaymentFactory
{
    /**
     * Correspondance méthode de paiement (front) → passerelle.
     */
    private const METHOD_MAP = [
        'orange_money'   => 'cinetpay',
        'moov_money'     => 'cinetpay',
        'mobile_money'   => 'cinetpay',
        'carte_bancaire' => 'stripe',
        'card'           => 'stripe',
        'visa'           => 'stripe',
        'mastercard'     => 'stripe',
    ];

    /**
     * Retourne la passerelle adaptée à une méthode de paiement donnée.
     * Bascule sur DisabledGateway si la passerelle cible n'est pas activée.
     */
    public static function forMethod(string $method): PaymentGatewayInterface
    {
        $gatewayName = self::METHOD_MAP[$method] ?? (string) (function_exists('env') ? env('PAYMENT_GATEWAY', '') : '');
        return self::make($gatewayName);
    }

    /**
     * Instancie une passerelle par son nom technique.
     */
    public static function make(string $name): PaymentGatewayInterface
    {
        $gateway = match (strtolower($name)) {
            'cinetpay' => new CinetPayGateway(),
            'stripe'   => new StripeGateway(),
            default    => new DisabledGateway(),
        };

        // Sécurité : si la passerelle réelle n'est pas activée (identifiants
        // manquants), on n'expose jamais un paiement « réel » cassé → démo.
        if (!($gateway instanceof DisabledGateway) && !$gateway->isEnabled()) {
            return new DisabledGateway();
        }

        return $gateway;
    }

    /**
     * Indique quelles passerelles sont réellement activées (pour l'admin/UI).
     *
     * @return array<string,bool>
     */
    public static function status(): array
    {
        return [
            'cinetpay' => (new CinetPayGateway())->isEnabled(),
            'stripe'   => (new StripeGateway())->isEnabled(),
            'mode'     => (string) (function_exists('env') ? env('PAYMENT_MODE', 'sandbox') : 'sandbox'),
        ];
    }
}
