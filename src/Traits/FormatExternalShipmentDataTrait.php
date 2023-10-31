<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Traits;

use JcElectronics\ExactOrders\Api\Data\ExternalShipmentInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Api\Data\TrackInterface;
use Magento\Sales\Model\Order\Shipment;

trait FormatExternalShipmentDataTrait
{
    use FormatExternalOrderAddressTrait;

    private function formatExternalShipmentData(Shipment $shipment): ExternalShipmentInterface
    {
        return $this->externalShipmentFactory->create(
            [
                'data' => [
                    'shipment_id' => $shipment->getEntityId(),
                    'ext_shipment_id' => $shipment->getData('ext_order_id'),
                    'customer_id' => $shipment->getCustomerId(),
                    'order_id' => $shipment->getEntityId(),
                    'invoice_id' => $shipment->getEntityId(),
                    'shipment_status' => $shipment->getData('external_customer_id'),
                    'increment_id' => $shipment->getIncrementId(),
                    'created_at' => $shipment->getCreatedAt(),
                    'updated_at' => $shipment->getUpdatedAt(),
                    'name' => $shipment->getData('name'),
                    'tracking' => $this->formatExternalShipmentTracking($shipment->getTracks()),
                    'additional_data' => [],
                    'items' => $this->formatExternalShipmentItems($shipment->getAllItems()),
                    'shipping_address' => $this->formatExternalOrderAddress($shipment->getShippingAddres()),
                    'billing_address' => $this->formatExternalOrderAddress($shipment->getBillingAddress())
                ]
            ]
        );
    }

    private function formatExternalShipmentItems(array $items): array
    {
        return array_reduce(
            $items,
            fn(array $carry, Shipment\Item $item) => array_merge(
                $carry,
                [
                    $this->externalShipmentItemFactory->create(
                        [
                            'data' => [
                                'shipmentitem_id' => $item->getEntityId(),
                                'shipment_id' => $item->getShipment()->getId(),
                                'row_total' => $item->getRowTotal(),
                                'price' => $item->getPrice(),
                                'weight' => $item->getWeight(),
                                'qty' => $item->getQty(),
                                'sku' => $item->getSku(),
                                'name' => $item->getName(),
                                'description' => $item->getDescription(),
                                'additional_data' => [],
                            ]
                        ]
                    )
                ]
            ),
            []
        );
    }

    private function formatExternalShipmentTracking(?array $tracks): string
    {
        return $this->serializer->serialize(
            array_map(
                fn (TrackInterface $item) => [
                    'carrier_name' => $item->getCarrierCode(),
                    'code' => $item->getTrackNumber(),
                    'url' => ''
                ],
                $tracks ?? []
            )
        );
    }
}
