<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\ItemInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterfaceFactory;
use Magento\Sales\Api\Data\ShippingInterfaceFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\ItemFactory;

class SetOrderItems extends AbstractModifier
{
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ItemFactory $itemFactory,
        private readonly OrderItemRepositoryInterface $orderItemRepository,
        private readonly OrderItemExtensionFactory $extensionFactory
    ) {
        parent::__construct(
            $orderRepository,
            $searchCriteriaBuilder
        );
    }

    public function supports(mixed $entity): bool
    {
        return $entity instanceof ExternalOrderInterface && $entity->getItems();
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface&Order   $result
     *
     * @return OrderInterface
     * @throws LocalizedException
     */
    public function process(mixed $model, mixed $result): mixed
    {
        foreach ($model->getItems() as $item) {
            if ((int) $item->getQty() === 0) {
                continue;
            }

            $product = $this->getProductBySku($item->getSku());

            if ($product === null) {
                continue;
            }

            $orderItem = $this->getOrderItemFromExternalOrder($item, (int) $result->getEntityId()) ?? $this->itemFactory->create();

            if (!$orderItem->getItemId()) {
                $this->setBasicOrderItemData($orderItem, $product, $item)
                    ->setOrderItemPriceData($orderItem, $product, $item);
            }

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

        if (count($result->getItems()) === 0) {
            throw new LocalizedException(__('Order has no valid items and can\'t be imported'));
        }

        return $result;
    }

    private function setBasicOrderItemData(
        OrderItemInterface|Order\Item $orderItem,
        ProductInterface $product,
        ItemInterface $item
    ): self {
        $orderItem->setProductId($product->getId())
            ->setName($item->getName())
            ->setSku($item->getSku())
            ->setProductType($product->getTypeId())
            ->setQtyOrdered($item->getQty());

        return $this;
    }

    private function setOrderItemPriceData(
        OrderItemInterface|Order\Item $orderItem,
        ProductInterface $product,
        ItemInterface $item
    ): self {
        $price         = $item->getPrice();
        $basePrice     = $item->getBasePrice() ?: $price;
        $rowTotal      = $item->getRowTotal() ?: $price * $orderItem->getQtyOrdered();
        $baseRowTotal  = $item->getBaseRowTotal() ?: $rowTotal;
        $taxAmount     = $item->getTaxAmount() ?: 0;
        $baseTaxAmount = $item->getBaseTaxAmount() ?: $taxAmount;

        $orderItem->setBaseDiscountAmount($item->getBaseDiscountAmount() ?: $item->getDiscountAmount() ?: 0)
            ->setBasePrice($basePrice)
            ->setBaseOriginalPrice($basePrice)
            ->setBasePriceInclTax($basePrice)
            ->setBaseRowTotal($baseRowTotal)
            ->setBaseRowTotalInclTax($baseRowTotal)
            ->setBaseTaxAmount($baseTaxAmount)
            ->setDiscountAmount(0)
            ->setOriginalPrice($price)
            ->setPrice($price)
            ->setPriceInclTax($price)
            ->setRowTotal($rowTotal)
            ->setRowTotalInclTax($rowTotal)
            ->setTaxAmount($taxAmount);

        return $this;
    }

    private function getOrderItemFromExternalOrder(ItemInterface $item, ?int $orderId): ?OrderItemInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderItemInterface::SKU, $item->getSku())
            ->addFilter(OrderItemInterface::QTY_ORDERED, $item->getQty())
            ->addFilter(OrderItemInterface::ROW_TOTAL, $item->getRowTotal());

        if ($orderId !== null) {
            $searchCriteria->addFilter(OrderItemInterface::ORDER_ID, $orderId);
        }

        return current(
            $this->orderItemRepository
                ->getList($searchCriteria->create())
                ->getItems()
        ) ?: null;
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
