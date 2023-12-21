<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Invoice;

use Magento\Sales\Api\InvoiceRepositoryInterface;

class StoreUpdatedInvoice extends AbstractModifier
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository
    ) {
    }

    public function process(mixed $model, mixed $result): mixed
    {
        return $this->invoiceRepository->save($result);
    }
}
