<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalOrder;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\ItemInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\ExternalOrder\ItemFactory;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\OrderFactory;

class SetOrderItems extends AbstractModifier
{
    public function __construct(
        private readonly ItemFactory $itemFactory
    ) {
    }

    /**
     * @param OrderInterface         $model
     * @param ExternalOrderInterface $result
     *
     * @return ExternalOrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setItems(
            array_map(
                function (OrderItemInterface $item) {
                    /** @var ItemInterface $orderItem */
                    $orderItem = $this->itemFactory->create();
                    $orderItem->setOrderId($item->getOrderId())
                        ->setOrderitemId($item->getItemId())
                        ->setName($item->getName())
                        ->setSku($item->getSku())
                        ->setQty($item->getQtyOrdered())
                        ->setBasePrice($item->getBasePrice())
                        ->setPrice($item->getPrice())
                        ->setBaseRowTotal($item->getBaseRowTotal())
                        ->setRowTotal($item->getRowTotal())
                        ->setBaseTaxAmount($item->getBaseTaxAmount())
                        ->setTaxAmount($item->getTaxAmount())
                        ->setBaseDiscountAmount($item->getBaseDiscountAmount())
                        ->setDiscountAmount($item->getDiscountAmount());

                    return $orderItem;
                },
                $model->getItems(),
            )
        );

        return $result;
    }
}
