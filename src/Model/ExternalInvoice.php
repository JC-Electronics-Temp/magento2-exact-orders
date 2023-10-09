<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Invoice;

class ExternalInvoice extends DataObject implements ExternalInvoiceInterface
{
    public function normalize(Invoice $invoice): self
    {
        $this->setData($invoice->getData());

        return $this;
    }

    public function getItems(): array
    {
        return array_map(
            static fn (array $item) => new DataObject($item),
            $this->getData('items') ?? []
        );
    }
}
