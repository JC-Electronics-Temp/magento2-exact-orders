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
    /**
     * @param OrderInterface         $model
     * @param ExternalOrderInterface $result
     *
     * @return ExternalOrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setBaseGrandtotal($model->getBaseGrandTotal())
            ->setBaseSubtotal($model->getBaseSubtotal())
            ->setGrandtotal($model->getGrandTotal())
            ->setSubtotal($model->getSubtotal())
            ->setBaseDiscountAmount($model->getBaseDiscountAmount() ?: 0)
            ->setDiscountAmount($model->getDiscountAmount() ?: 0)
            ->setBaseTaxAmount($model->getBaseTaxAmount() ?: 0)
            ->setTaxAmount($model->getTaxAmount() ?: 0)
            ->setBaseShippingAmount($model->getBaseShippingAmount() ?: 0)
            ->setShippingAmount($model->getShippingAmount() ?: 0);

        return $result;
    }
}
