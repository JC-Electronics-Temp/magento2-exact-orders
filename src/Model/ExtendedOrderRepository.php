<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExtendedOrderInterface;
use JcElectronics\ExactOrders\Api\ExtendedOrderRepositoryInterface;
use JcElectronics\ExactOrders\Model\ResourceModel\ExtendedOrder as ResourceModel;
use Magento\Framework\Exception\NoSuchEntityException;

class ExtendedOrderRepository implements ExtendedOrderRepositoryInterface
{
    public function __construct(
        private readonly ResourceModel $resourceModel,
        private readonly ExtendedOrderFactory $extendedOrderFactory
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    public function get(int $id): ExtendedOrderInterface
    {
        /** @var ExtendedOrderInterface $extendedOrder */
        $extendedOrder = $this->extendedOrderFactory->create();
        $this->resourceModel->load($extendedOrder, $id);

        if (!$extendedOrder->getId()) {
            throw NoSuchEntityException::singleField(ExtendedOrderInterface::KEY_ID, $id);
        }

        return $extendedOrder;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByOrderId(int $orderId): ExtendedOrderInterface
    {
        /** @var ExtendedOrderInterface $extendedOrder */
        $extendedOrder = $this->extendedOrderFactory->create();
        $this->resourceModel->load($extendedOrder, $orderId, ExtendedOrderInterface::KEY_ORDER_ID);

        if (!$extendedOrder->getId()) {
            throw NoSuchEntityException::singleField(ExtendedOrderInterface::KEY_ORDER_ID, $orderId);
        }

        return $extendedOrder;
    }

    public function save(ExtendedOrderInterface $order): ExtendedOrderInterface
    {
        $this->resourceModel->save($order);

        return $order;
    }

    public function delete(ExtendedOrderInterface $order): void
    {
        $this->resourceModel->delete($order);
    }
}