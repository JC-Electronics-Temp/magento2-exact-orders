<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalInvoice;

use JcElectronics\ExactOrders\Model\ExternalInvoiceFactory;

class CreateExternalInvoiceModel extends AbstractModifier
{
    public function __construct(
        private readonly ExternalInvoiceFactory $invoiceFactory
    )
    {
    }

    public function process(mixed $model, mixed $result): mixed
    {
        if (!$result instanceof ExternalInvoiceFactory) {
            $result = $this->invoiceFactory->create();
        }

        return $result;
    }
}
