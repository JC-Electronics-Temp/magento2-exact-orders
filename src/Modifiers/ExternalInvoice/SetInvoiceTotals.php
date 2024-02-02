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

class SetInvoiceTotals extends AbstractModifier
{
    // phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh

    /**
     * @param InvoiceInterface&Invoice $model
     * @param ExternalInvoiceInterface $result
     *
     * @return ExternalInvoiceInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setBaseTaxAmount($model->getBaseTaxAmount() ?: $model->getTaxAmount() ?: 0)
            ->setBaseDiscountAmount($model->getBaseDiscountAmount() ?: $model->getDiscountAmount() ?: 0)
            ->setBaseShippingAmount($model->getBaseShippingAmount() ?: $model->getShippingAmount() ?: 0)
            ->setBaseSubtotal($model->getBaseSubtotal() ?: $model->getSubtotal())
            ->setBaseGrandtotal($model->getBaseGrandTotal() ?: $model->getGrandTotal())
            ->setTaxAmount($model->getTaxAmount() ?: 0)
            ->setDiscountAmount(0)
            ->setShippingAmount($model->getShippingAmount() ?: 0)
            ->setSubtotal($model->getSubtotal())
            ->setGrandtotal($model->getGrandTotal());

        return $result;
    }

    // phpcs:enable
}
