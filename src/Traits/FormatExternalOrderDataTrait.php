<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Traits;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Sales\Model\Order;

trait FormatExternalOrderDataTrait
{
    use FormatExternalOrderAddressTrait;
    
    private function formatExternalOrderData(Order $order): ExternalOrderInterface
    {
        return $this->externalOrderFactory->create(
            [
                'data' => [
                    'order_id' => $order->getEntityId(),
                    'invoice_ids' => [],
                    'magento_order_id' => $order->getEntityId(),
                    'magento_customer_id' => $order->getCustomerId(),
                    'external_customer_id' => $order->getData('external_customer_id'),
                    'ext_order_id' => $order->getData('ext_order_id'),
                    'base_grandtotal' => $order->getBaseGrandTotal(),
                    'base_subtotal' => $order->getBaseSubtotal(),
                    'grandtotal' => $order->getGrandTotal(),
                    'subtotal' => $order->getSubtotal(),
                    'po_number' => $order->getData('custom_order_reference'),
                    'state' => $order->getState(),
                    'shipping_method' => $order->getShippingMethod(),
                    'shipping_address' => $this->formatExternalOrderAddress($order->getShippingAddres()),
                    'billing_address' => $this->formatExternalOrderAddress($order->getBillingAddress()),
                    'payment_method' => $order->getPayment()->getMethod(),
                    'base_discount_amount' => $order->getBaseDiscountAmount(),
                    'discount_amount' => $order->getDiscountAmount(),
                    'order_date' => $order->getCreatedAt(),
                    'base_tax_amount' => $order->getBaseTaxAmount(),
                    'tax_amount' => $order->getBaseTaxAmount(),
                    'base_shipping_amount' => $order->getBaseShippingAmount(),
                    'shipping_amount' => $order->getShippingAmount(),
                    'items' => $this->formatExternalOrderItems($order->getAllItems()),
                    'magento_increment_id' => $order->getIncrementId(),
                    'updated_at' => $order->getUpdatedAt(),
                    'additional_data' => []
                ]
            ]
        );
    }

    private function formatExternalOrderItems(array $items): array
    {
        return array_reduce(
            $items,
            fn(array $carry, Order\Item $item) => array_merge(
                $carry,
                [
                    $this->externalOrderItemFactory->create(
                        [
                            'data' => [
                                'orderitem_id' => $item->getEntityId(),
                                'order_id' => $item->getOrderId(),
                                'name' => $item->getName(),
                                'sku' => $item->getSku(),
                                'base_price' => $item->getBasePrice(),
                                'price' => $item->getPrice(),
                                'base_row_total' => $item->getBaseRowTotal(),
                                'row_total' => $item->getRowTotal(),
                                'base_tax_amount' => $item->getBaseTaxAmount(),
                                'tax_amount' => $item->getTaxAmount(),
                                'qty' => $item->getQtyOrdered(),
                                'additional_data' => [],
                                'base_discount_amount' => $item->getBaseDiscountAmount(),
                                'discount_amount' => $item->getDiscountAmount()
                            ]
                        ]
                    )
                ]
            ),
            []
        );
    }
}
