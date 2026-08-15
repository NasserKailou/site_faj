<?php
/**
 * Résultat normalisé d'une opération de paiement.
 * ---------------------------------------------------------------------------
 * Objet de transfert (DTO) partagé par tous les adaptateurs de passerelle
 * afin d'uniformiser les retours quelle que soit la passerelle utilisée.
 *
 * @package FAJ\Includes\Payment
 */

declare(strict_types=1);

namespace FAJ\Payment;

final class PaymentResult
{
    public bool $success;
    /** Référence interne du don (ex : FAJ-20240101-ABCD1234). */
    public string $reference;
    /** URL de redirection vers la page de paiement hébergée (le cas échéant). */
    public ?string $redirectUrl;
    /** Identifiant de transaction fourni par la passerelle. */
    public ?string $transactionId;
    /**
     * Statut normalisé : 'en_attente', 'complete', 'echec', 'annule',
     * 'rembourse', 'demo'.
     */
    public string $status;
    /** Message lisible (succès ou erreur). */
    public string $message;
    /** Données brutes renvoyées par la passerelle (pour journalisation/debug). */
    public array $raw;

    public function __construct(
        bool $success,
        string $reference = '',
        ?string $redirectUrl = null,
        ?string $transactionId = null,
        string $status = 'en_attente',
        string $message = '',
        array $raw = []
    ) {
        $this->success       = $success;
        $this->reference     = $reference;
        $this->redirectUrl   = $redirectUrl;
        $this->transactionId = $transactionId;
        $this->status        = $status;
        $this->message       = $message;
        $this->raw           = $raw;
    }

    public static function success(
        string $reference,
        ?string $redirectUrl = null,
        ?string $transactionId = null,
        string $status = 'en_attente',
        string $message = 'OK',
        array $raw = []
    ): self {
        return new self(true, $reference, $redirectUrl, $transactionId, $status, $message, $raw);
    }

    public static function failure(string $message, string $reference = '', array $raw = []): self
    {
        return new self(false, $reference, null, null, 'echec', $message, $raw);
    }

    public function toArray(): array
    {
        return [
            'success'        => $this->success,
            'reference'      => $this->reference,
            'redirect_url'   => $this->redirectUrl,
            'transaction_id' => $this->transactionId,
            'status'         => $this->status,
            'message'        => $this->message,
        ];
    }
}
