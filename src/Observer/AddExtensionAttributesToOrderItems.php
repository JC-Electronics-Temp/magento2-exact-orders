<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Observer;

use JcElectronics\ExactOrders\Api\Data\ExtendedOrderItemInterface;
use JcElectronics\ExactOrders\Api\ExtendedOrderItemRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Magento\Sales\Api\Data\OrderItemInterface;

class AddExtensionAttributesToOrderItems implements ObserverInterface
{
    public function __construct(
        private readonly OrderItemExtensionFactory $extensionFactory,
        private readonly ExtendedOrderItemRepositoryInterface $repository
    ) {
    }

    public function execute(Observer $observer): void
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $collection */
        $collection = $observer->getData('order_item_collection');

        foreach ($collection as $item) {
            try {
                $extendedOrderItem = $this->getExtendedDataByOrderItem($item);
                $extensionAttributes = $item->getExtensionAttributes() ?: $this->extensionFactory->create();
                $extensionAttributes->setSerialNumber($extendedOrderItem->getSerialNumber())
                    ->setExpectedDeliveryDate($extendedOrderItem->getExpectedDeliveryDate());

                $item->setExtensionAttributes($extensionAttributes);
            } catch (NoSuchEntityException) {
                continue;
            }
        }
    }

    private function getExtendedDataByOrderItem(OrderItemInterface $orderItem): ExtendedOrderItemInterface
    {
        return $this->repository->getByOrderItemId((int) $orderItem->getItemId());
    }
}
