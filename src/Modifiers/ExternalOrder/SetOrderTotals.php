<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
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
            ->setBaseDiscountAmount($model->getBaseDiscountAmount())
            ->setDiscountAmount($model->getDiscountAmount())
            ->setBaseTaxAmount($model->getBaseTaxAmount())
            ->setTaxAmount($model->getTaxAmount())
            ->setBaseShippingAmount($model->getBaseShippingAmount())
            ->setShippingAmount($model->getShippingAmount());

        return $result;
    }
}
