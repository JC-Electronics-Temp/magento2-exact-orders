<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\Sales\Order;

use JcElectronics\ExactOrders\Api\Data\ExtendedOrderInterface;
use JcElectronics\ExactOrders\Api\ExtendedOrderRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExtendedOrderFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class UpdateAdditionalOrderData
{
    public function __construct(
        private readonly ExtendedOrderRepositoryInterface $repository,
        private readonly ExtendedOrderFactory $extendedOrderFactory
    ) {
    }

    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $result
    ): OrderInterface {
        try {
            $extendedOrder       = $this->getExtendedDataByOrder($result);
            $extensionAttributes = $result->getExtensionAttributes();
            $extensionAttributes->setIsExternalOrder($extendedOrder->getIsExternalOrder());

            $result->setExtensionAttributes($extensionAttributes);
        } catch (NoSuchEntityException) {
            return $result;
        }

        return $result;
    }

    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $result
    ): OrderSearchResultInterface {
        foreach ($result->getItems() as $item) {
            $this->afterGet($subject, $item);
        }

        return $result;
    }

    public function afterSave(
        OrderRepositoryInterface $subject,
        OrderInterface $result,
        OrderInterface $order
    ): OrderInterface {
        $isExternalOrder = (bool) $order->getExtensionAttributes()->getIsExternalOrder();

        try {
            $extendedOrder = $this->repository->getByOrderId((int) $order->getEntityId());
        } catch (NoSuchEntityException) {
            /** @var ExtendedOrderInterface $extendedOrder */
            $extendedOrder = $this->extendedOrderFactory->create();
            $extendedOrder->setOrderId((int) $order->getEntityId());
        }

        $extendedOrder->setIsExternalOrder($isExternalOrder);

        $this->repository->save($extendedOrder);

        return $result;
    }

    private function getExtendedDataByOrder(OrderInterface $order): ExtendedOrderInterface
    {
        return $this->repository->getByOrderId((int) $order->getEntityId());
    }
}
