<?php
/**
 * Passerelle « désactivée / bac à sable ».
 * ---------------------------------------------------------------------------
 * Adaptateur par défaut lorsqu'AUCUNE passerelle réelle n'est configurée.
 * Il n'appelle aucun service externe : il enregistre l'intention de paiement
 * en statut « démo » afin que la plateforme reste fonctionnelle en attendant
 * les identifiants bancaires réels.
 *
 * ⚠️ En production, PAYMENT_MODE=live avec une passerelle réelle DOIT être
 * utilisé. Ce mode ne débite jamais réellement le donateur.
 *
 * @package FAJ\Includes\Payment
 */

declare(strict_types=1);

namespace FAJ\Payment;

final class DisabledGateway extends AbstractGateway
{
    public function getName(): string
    {
        return 'disabled';
    }

    public function isEnabled(): bool
    {
        // Toujours "disponible" mais ne traite pas de paiement réel.
        return false;
    }

    public function initiatePayment(array $payment): PaymentResult
    {
        $reference = (string) ($payment['reference'] ?? '');
        $this->log('Initiation en mode démo (aucune passerelle configurée)', [
            'reference' => $reference,
            'amount'    => $payment['amount'] ?? 0,
        ]);

        return new PaymentResult(
            true,
            $reference,
            null,   // pas de redirection
            null,
            'demo',
            'Don enregistré en mode démonstration. Configurez une passerelle de paiement (.env) pour activer les paiements réels.',
            ['demo' => true]
        );
    }

    public function verifyPayment(string $reference): PaymentResult
    {
        return new PaymentResult(true, $reference, null, null, 'demo', 'Mode démonstration.');
    }

    public function handleCallback(array $payload, array $headers = [], string $rawBody = ''): PaymentResult
    {
        return PaymentResult::failure('Aucune passerelle configurée pour traiter les callbacks.');
    }
}
