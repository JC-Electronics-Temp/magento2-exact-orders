<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Traits;

use JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalInvoice\ItemInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;

trait FormatInvoiceDataTrait
{
    public function formatInvoiceData(
        array $invoiceData,
        Order $order
    ): InvoiceInterface {
        return $this->serviceInputProcessor->convertValue(
            [
                'order_id' => current($invoiceData['order_ids']),
                'base_discount_amount' => (float) $invoiceData['base_discount_amount'],
                'base_grand_total' => (float) $invoiceData['base_grandtotal'],
                'base_shipping_amount' => (float) $invoiceData['base_shipping_amount'],
                'base_shipping_incl_tax' => (float) $invoiceData['base_shipping_amount'],
                'base_shipping_tax_amount' => 0,
                'base_subtotal' => (float) $invoiceData['base_subtotal'],
                'base_subtotal_incl_tax' => (float) $invoiceData['base_subtotal'],
                'base_tax_amount' => (float) $invoiceData['base_tax_amount'],
                'created_at' => $invoiceData['invoice_date'],
                'discount_amount' => (float) $invoiceData['discount_amount'],
                'grand_total' => (float) $invoiceData['grandtotal'],
                'increment_id' => $invoiceData['magento_increment_id'],
                'shipping_amount' => (float) $invoiceData['shipping_amount'],
                'shipping_incl_tax' => (float) $invoiceData['shipping_amount'],
                'shipping_tax_amount' => 0,
                'total_qty' => array_sum(
                    array_column($invoiceData['items'], 'qty')
                ),
                'state' => strtolower($invoiceData['state']),
                'store_id' => $order->getStoreId(),
                'subtotal' => (float) $invoiceData['subtotal'],
                'subtotal_incl_tax' => (float) $invoiceData['subtotal'],
                'tax_amount' => (float) $invoiceData['tax_amount'],
                'updated_at' => $invoiceData['updated_at'],
                'items' => $this->formatInvoiceItems($invoiceData['items'], $order->getAllItems()),
                'billing_address_id' => $order->getBillingAddressId(),
                'shipping_address_id' => $order->getShippingAddressId(),
                'order_currency_code' => $order->getOrderCurrencyCode(),
                'store_currency_code' => $order->getStoreCurrencyCode(),
                'base_currency_code' => $order->getBaseCurrencyCode(),
                'global_currency_code' => $order->getGlobalCurrencyCode(),
                'store_to_order_rate' => $order->getStoreToOrderRate(),
                'store_to_base_rate' => $order->getStoreToBaseRate(),
                'base_to_global_rate' => $order->getBaseToGlobalRate(),
                'extension_attributes' => [
                    'ext_invoice_id' => $invoiceData['ext_invoice_id']
                ]
            ],
            InvoiceInterface::class
        );
    }

    private function formatInvoiceItems(
        array $items,
        array $orderItems
    ): array {
        return array_reduce(
            $items,
            function (array $carry, ItemInterface $item) use ($orderItems) {
                $orderItem = $this->getOrderItemByInvoice($item, $orderItems);

                if (!$orderItem instanceof Order\Item) {
                    return $carry;
                }

                $carry[] = [
                    'base_discount_amount' => (float) $item->getBaseDiscountAmount(),
                    'base_price' => (float) $item->getBasePrice(),
                    'base_price_incl_tax' => (float) $item->getBasePrice(),
                    'base_row_total' => (float) $item->getBaseRowTotal(),
                    'base_row_total_incl_tax' => (float) $item->getBaseRowTotal(),
                    'base_tax_amount' => (float) $item->getBaseTaxAmount(),
                    'discount_amount' => (float) $item->getDiscountAmount(),
                    'name' => $item->getName(),
                    'price' => (float) $item->getPrice(),
                    'price_incl_tax' => (float)$item->getPrice(),
                    'qty' => (float) $item->getQty(),
                    'row_total' => (float) $item->getRowTotal(),
                    'row_total_incl_tax' => (float) $item->getRowTotal(),
                    'order_item_id' => $orderItem->getItemId(),
                    'product_id' => $orderItem->getProductId() ?? null,
                    'sku' => $item->getSku(),
                    'tax_amount' => (float) $item->getTaxAmount(),
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

    private function getOrderItemByInvoice(
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
