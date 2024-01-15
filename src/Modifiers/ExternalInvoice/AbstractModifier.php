<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalInvoice;

use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Sales\Api\Data\InvoiceInterface;

abstract class AbstractModifier implements ModifierInterface
{
    public function supports(mixed $entity): bool
    {
        return $entity instanceof InvoiceInterface;
    }
}
