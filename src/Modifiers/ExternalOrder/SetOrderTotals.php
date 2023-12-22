<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalOrder;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderFactory;

class SetOrderTotals extends AbstractModifier
{
    // phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh

    /**
     * @param OrderInterface         $model
     * @param ExternalOrderInterface $result
     *
     * @return ExternalOrderInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setBaseGrandtotal($model->getBaseGrandTotal() ?: $model->getGrandTotal())
            ->setBaseSubtotal($model->getBaseSubtotal() ?: $model->getSubtotal())
            ->setGrandtotal($model->getGrandTotal())
            ->setSubtotal($model->getSubtotal())
            ->setBaseDiscountAmount($model->getBaseDiscountAmount() ?: $model->getDiscountAmount() ?: 0)
            ->setDiscountAmount($model->getDiscountAmount() ?: 0)
            ->setBaseTaxAmount($model->getBaseTaxAmount() ?: $model->getTaxAmount() ?: 0)
            ->setTaxAmount($model->getTaxAmount() ?: 0)
            ->setBaseShippingAmount($model->getBaseShippingAmount() ?: $model->getShippingAmount() ?: 0)
            ->setShippingAmount($model->getShippingAmount() ?: 0);

        return $result;
    }

    // phpcs:enable
}
