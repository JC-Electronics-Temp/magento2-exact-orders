<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Invoice;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;

class InvoiceOrder extends AbstractModifier
{
    public function __construct(
        private readonly InvoiceOrderInterface $invoiceOrder,
        private readonly InvoiceRepositoryInterface $invoiceRepository
    ) {
    }

    /**
     * @param ExternalInvoiceInterface $model
     * @param InvoiceInterface|null    $result
     *
     * @return mixed
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $invoiceId = $this->invoiceOrder->execute(
            current($model->getOrderIds())
        );

        return $this->invoiceRepository->get($invoiceId);
    }
}
