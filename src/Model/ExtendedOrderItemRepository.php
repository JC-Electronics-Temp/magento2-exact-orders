<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExtendedOrderItemInterface;
use JcElectronics\ExactOrders\Api\ExtendedOrderItemRepositoryInterface;
use JcElectronics\ExactOrders\Model\ResourceModel\ExtendedOrderItem as ResourceModel;
use Magento\Framework\Exception\NoSuchEntityException;

class ExtendedOrderItemRepository implements ExtendedOrderItemRepositoryInterface
{
    public function __construct(
        private readonly ResourceModel $resourceModel,
        private readonly ExtendedOrderItemFactory $extendedOrderItemFactory
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    public function get(int $id): ExtendedOrderItemInterface
    {
        /** @var ExtendedOrderItemInterface $extendedOrderItem */
        $extendedOrderItem = $this->extendedOrderItemFactory->create();
        $this->resourceModel->load($extendedOrderItem, $id);

        if (!$extendedOrderItem->getId()) {
            throw NoSuchEntityException::singleField(ExtendedOrderItemInterface::KEY_ID, $id);
        }

        return $extendedOrderItem;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByOrderItemId(int $orderItemId): ExtendedOrderItemInterface
    {
        /** @var ExtendedOrderItemInterface $extendedOrderItem */
        $extendedOrderItem = $this->extendedOrderItemFactory->create();
        $this->resourceModel->load($extendedOrderItem, $orderItemId, ExtendedOrderItemInterface::KEY_ORDER_ITEM_ID);

        if (!$extendedOrderItem->getId()) {
            throw NoSuchEntityException::singleField(ExtendedOrderItemInterface::KEY_ORDER_ITEM_ID, $orderItemId);
        }

        return $extendedOrderItem;
    }

    public function save(ExtendedOrderItemInterface $orderItem): ExtendedOrderItemInterface
    {
        $this->resourceModel->save($orderItem);

        return $orderItem;
    }

    public function delete(ExtendedOrderItemInterface $orderItem): void
    {
        $this->resourceModel->delete($orderItem);
    }
}
