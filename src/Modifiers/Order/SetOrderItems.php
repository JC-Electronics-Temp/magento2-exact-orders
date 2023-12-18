<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\ItemInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterfaceFactory;
use Magento\Sales\Api\Data\ShippingInterfaceFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\ItemFactory;

class SetOrderItems extends AbstractModifier
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ItemFactory $itemFactory,
        private readonly OrderItemRepositoryInterface $orderItemRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly OrderItemExtensionFactory $extensionFactory
    ) {
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface&Order   $result
     *
     * @return OrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        foreach ($model->getItems() as $item) {
            $product = $this->getProductBySku($item->getSku());

            if ($product === null) {
                continue;
            }

            /** @var OrderItemInterface $orderItem */
            $orderItem = $this->itemFactory->create();
            $orderItem->setItemId($this->getOrderItemId($item, (int) $result->getEntityId()))
                ->setProductId($product->getId())
                ->setName($item->getName())
                ->setSku($item->getSku())
                ->setProductType($product->getTypeId())
                ->setQtyOrdered($item->getQty())
                ->setBaseDiscountAmount($item->getBaseDiscountAmount())
                ->setBaseOriginalPrice($item->getBasePrice())
                ->setBasePrice($item->getBasePrice())
                ->setBasePriceInclTax($item->getBasePrice())
                ->setBaseRowTotal($item->getBaseRowTotal())
                ->setBaseRowTotalInclTax($item->getBaseRowTotal())
                ->setBaseTaxAmount($item->getBaseTaxAmount())
                ->setDiscountAmount($item->getDiscountAmount())
                ->setOriginalPrice($item->getPrice())
                ->setPrice($item->getPrice())
                ->setPriceInclTax($item->getPrice())
                ->setRowTotal($item->getRowTotal())
                ->setRowTotalInclTax($item->getRowTotal())
                ->setTaxAmount($item->getTaxAmount());

            // phpcs:disable Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
            $additionalData = array_reduce(
                $item->getAdditionalData(),
                static fn (array $carry, AdditionalDataInterface $data) => array_merge(
                    $carry,
                    [$data->getKey() => $data->getValue()]
                ),
                []
            );
            // phpcs:enable
            
            $extensionAttributes = $orderItem->getExtensionAttributes() ?: $this->extensionFactory->create();
            $extensionAttributes->setExpectedDeliveryDate($additionalData['expected_delivery_date'] ?? null)
                ->setSerialNumber($additionalData['serial_number'] ?? null);

            $orderItem->setExtensionAttributes($extensionAttributes);

            $result->addItem($orderItem);
        }

        return $result;
    }

    private function getOrderItemId(ItemInterface $item, ?int $orderId): ?int
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderItemInterface::SKU, $item->getSku())
            ->addFilter(OrderItemInterface::QTY_ORDERED, $item->getQty())
            ->addFilter(OrderItemInterface::ROW_TOTAL, $item->getRowTotal());

        if ($orderId !== null) {
            $searchCriteria->addFilter(OrderItemInterface::ORDER_ID, $orderId);
        }

        $orderItem  = current(
            $this->orderItemRepository->getList(
                $searchCriteria->create()
            )->getItems()
        );

        return $orderItem ? (int) $orderItem->getItemId() : null;
    }

    private function getProductBySku(string $sku): ?ProductInterface
    {
        try {
            return $this->productRepository->get($sku);
        } catch (NoSuchEntityException) {
            return null;
        }
    }
}
