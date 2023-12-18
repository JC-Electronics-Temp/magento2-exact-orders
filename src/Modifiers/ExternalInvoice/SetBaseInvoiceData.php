<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalInvoice;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order\Invoice;

class SetBaseInvoiceData extends AbstractModifier
{
    /**
     * @param InvoiceInterface&Invoice $model
     * @param ExternalInvoiceInterface $result
     *
     * @return ExternalInvoiceInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setInvoiceId($model->getEntityId())
            ->setOrderIds([$model->getOrderId()])
            ->setMagentoInvoiceId($model->getEntityId())
            ->setExtInvoiceId($model->getData('ext_invoice_id'))
            ->setState((string) $model->getState())
            ->setPoNumber($model->getOrder()->getData('customer_order_reference'))
            ->setInvoiceDate($model->getCreatedAt())
            ->setMagentoIncrementId($model->getIncrementId());

        return $result;
    }
}
