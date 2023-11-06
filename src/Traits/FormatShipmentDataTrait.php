<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Traits;

use JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalShipment\ItemInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order;

trait FormatShipmentDataTrait
{
    public function formatShipmentData(
        array $shipmentData,
        Order $order
    ): ShipmentInterface {
        return $this->serviceInputProcessor->convertValue(
            [
                'order_id' => $shipmentData['order_id'],
                'store_id' => $order->getStoreId(),
                'customer_id' => $shipmentData['customer_id'],
                'created_at' => $shipmentData['created_at'],
                'increment_id' => $shipmentData['increment_id'],
                'updated_at' => $shipmentData['updated_at'],
                'items' => $this->formatShipmentItems($shipmentData['items'], $order->getAllItems()),
                'tracks' => $this->formatShipmentTracks($shipmentData),
                'billing_address_id' => $order->getBillingAddressId(),
                'shipping_address_id' => $order->getShippingAddressId()
            ],
            ShipmentInterface::class
        );
    }

    private function formatShipmentItems(
        array $items,
        array $orderItems
    ): array {
        return array_reduce(
            $items,
            function (array $carry, ItemInterface $item) use ($orderItems) {
                $orderItem = $this->getOrderItemByShipment($item, $orderItems);

                if (!$orderItem instanceof Order\Item) {
                    return $carry;
                }

                $carry[] = [
                    'name' => $item->getName(),
                    'price' => (float) $item->getPrice(),
                    'qty' => (float) $item->getQty(),
                    'order_item_id' => $orderItem->getId(),
                    'product_id' => $orderItem->getProductId() ?? null,
                    'sku' => $item->getSku(),
                    'weight' => $item->getWeight(),
                    'extension_attributes' => array_reduce(
                        $item->getAdditionalData(),
                        static fn (array $carry, AdditionalDataInterface $attribute) => array_replace(
                            $carry,
                            [$attribute->getKey() => $attribute->getValue()]
                        ),
                        []
                    )
                ];

                return $carry;
            },
            []
        );
    }

    private function formatShipmentTracks(array $shipment): array
    {
        return array_map(
            fn (array $track) => [
                'weight' => $shipment['weight'] ?? 0,
                'track_number' => $track['code'],
                'carrier_code' => $track['name']
            ],
            $this->serializer->unserialize($shipment['tracking'] ?? '[]')
        );
    }

    private function getOrderItemByShipment(
        ItemInterface $item,
        array $orderItems
    ): ?OrderItemInterface {
        /** @var OrderItemInterface $orderItem */
        foreach ($orderItems as $orderItem) {
            if (
                strtolower($orderItem->getSku()) === strtolower($item->getSku()) &&
                (float) $orderItem->getQtyOrdered() === (float) $item->getQty() &&
                (float)$orderItem->getPrice() === (float)$item->getPrice()
            ) {
                return $orderItem;
            }
        }

        return null;
    }
}
