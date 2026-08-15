<?php
/**
 * Bootstrap du module de paiement.
 * ---------------------------------------------------------------------------
 * Charge (require) toutes les classes du module paiement sans autoloader
 * externe, conformément à l'architecture du projet (PHP pur, sans Composer).
 * Inclure ce seul fichier suffit pour utiliser \FAJ\Payment\PaymentFactory.
 *
 * @package FAJ\Includes\Payment
 */

declare(strict_types=1);

require_once __DIR__ . '/PaymentResult.php';
require_once __DIR__ . '/PaymentGatewayInterface.php';
require_once __DIR__ . '/AbstractGateway.php';
require_once __DIR__ . '/DisabledGateway.php';
require_once __DIR__ . '/CinetPayGateway.php';
require_once __DIR__ . '/StripeGateway.php';
require_once __DIR__ . '/PaymentFactory.php';
