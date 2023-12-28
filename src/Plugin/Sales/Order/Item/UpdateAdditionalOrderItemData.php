<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\Sales\Order\Item;

use JcElectronics\ExactOrders\Api\Data\ExtendedOrderItemInterface;
use JcElectronics\ExactOrders\Api\ExtendedOrderItemRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExtendedOrderItemFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderItemSearchResultInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;

class UpdateAdditionalOrderItemData
{
    public function __construct(
        private readonly ExtendedOrderItemRepositoryInterface $repository,
        private readonly ExtendedOrderItemFactory $extendedOrderFactory,
        private readonly OrderExtensionFactory $extensionFactory
    ) {
    }

    public function afterGet(
        OrderItemRepositoryInterface $subject,
        OrderItemInterface $result
    ): OrderItemInterface {
        try {
            $extendedOrderItem = $this->getExtendedDataByOrderItem($result);
            $extensionAttributes = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
            $extensionAttributes->setSerialNumber($extendedOrderItem->getSerialNumber())
                ->setExpectedDeliveryDate($extendedOrderItem->getExpectedDeliveryDate());

            $result->setExtensionAttributes($extensionAttributes);
        } catch (NoSuchEntityException) {
            return $result;
        }

        return $result;
    }

    public function afterGetList(
        OrderItemRepositoryInterface $subject,
        OrderItemSearchResultInterface $result
    ): OrderItemSearchResultInterface {
        foreach ($result->getItems() as $item) {
            $this->afterGet($subject, $item);
        }

        return $result;
    }

    public function afterSave(
        OrderItemRepositoryInterface $subject,
        OrderItemInterface $result,
        OrderItemInterface $orderItem
    ): OrderItemInterface {
        $serialNumber = $orderItem->getExtensionAttributes()->getSerialNumber();
        $deliveryDate = $orderItem->getExtensionAttributes()->getExpectedDeliveryDate();

        try {
            $extendedOrderItem = $this->repository->getByOrderItemId((int) $orderItem->getItemId());
        } catch (NoSuchEntityException) {
            /** @var ExtendedOrderItemInterface $extendedOrderItem */
            $extendedOrderItem = $this->extendedOrderFactory->create();
            $extendedOrderItem->setOrderItemId((int) $orderItem->getItemId());
        }

        $extendedOrderItem->setSerialNumber($serialNumber)
            ->setExpectedDeliveryDate($deliveryDate);

        $this->repository->save($extendedOrderItem);

        return $result;
    }

    private function getExtendedDataByOrderItem(OrderItemInterface $orderItem): ExtendedOrderItemInterface
    {
        return $this->repository->getByOrderItemId((int) $orderItem->getItemId());
    }
}
