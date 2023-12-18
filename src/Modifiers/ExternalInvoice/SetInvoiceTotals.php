<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalInvoice;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order\Invoice;

class SetInvoiceTotals extends AbstractModifier
{
    /**
     * @param InvoiceInterface&Invoice $model
     * @param ExternalInvoiceInterface $result
     *
     * @return ExternalInvoiceInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setBaseTaxAmount($model->getBaseTaxAmount())
            ->setBaseDiscountAmount($model->getBaseDiscountAmount())
            ->setBaseShippingAmount($model->getBaseShippingAmount())
            ->setBaseSubtotal($model->getBaseSubtotal())
            ->setBaseGrandtotal($model->getBaseGrandTotal())
            ->setTaxAmount($model->getTaxAmount())
            ->setDiscountAmount($model->getDiscountAmount())
            ->setShippingAmount($model->getShippingAmount())
            ->setSubtotal($model->getSubtotal())
            ->setGrandtotal($model->getGrandTotal());

        return $result;
    }
}
