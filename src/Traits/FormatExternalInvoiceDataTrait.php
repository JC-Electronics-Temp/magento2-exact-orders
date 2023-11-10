<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Traits;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use Magento\Sales\Model\Order\Invoice;

trait FormatExternalInvoiceDataTrait
{
    use FormatExternalOrderAddressTrait;

    private function formatExternalInvoiceData(Invoice $invoice): ExternalInvoiceInterface
    {
        return $this->externalInvoiceFactory->create(
            [
                'data' => [
                    'invoice_id' => $invoice->getEntityId(),
                    'order_ids' => [$invoice->getOrderId()],
                    'magento_invoice_id' => $invoice->getEntityId(),
                    'ext_invoice_id' => $invoice->getData('ext_invoice_id'),
                    'po_number' => $invoice->getOrder()->getData('custom_order_reference'),
                    'magento_customer_id' => $invoice->getOrder()->getCustomerId(),
                    'base_tax_amount' => $invoice->getBaseTaxAmount(),
                    'base_discount_amount' => $invoice->getBaseDiscountAmount(),
                    'base_shipping_amount' => $invoice->getBaseShippingAmount(),
                    'base_subtotal' => $invoice->getBaseSubtotal(),
                    'base_grandtotal' => $invoice->getBaseGrandTotal(),
                    'tax_amount' => $invoice->getBaseTaxAmount(),
                    'discount_amount' => $invoice->getDiscountAmount(),
                    'shipping_amount' => $invoice->getShippingAmount(),
                    'subtotal' => $invoice->getSubtotal(),
                    'grandtotal' => $invoice->getGrandTotal(),
                    'invoice_date' => $invoice->getCreatedAt(),
                    'state' => $invoice->getState(),
                    'magento_increment_id' => $invoice->getIncrementId(),
                    'additional_data' => [],
                    'items' => $this->formatExternalInvoiceItems($invoice->getAllItems()),
                    'shipping_address' => $this->formatExternalOrderAddress($invoice->getOrder()->getShippingAddres()),
                    'billing_address' => $this->formatExternalOrderAddress($invoice->getOrder()->getBillingAddress())
                ]
            ]
        );
    }

    private function formatExternalInvoiceItems(array $items): array
    {
        return array_reduce(
            $items,
            fn(array $carry, Invoice\Item $item) => array_merge(
                $carry,
                [
                    $this->externalInvoiceItemFactory->create(
                        [
                            'data' => [
                                'invoiceitem_id' => $item->getEntityId(),
                                'invoice_id' => $item->getInvoice()->getEntityId(),
                                'order_id' => $item->getInvoice()->getOrderId(),
                                'name' => $item->getName(),
                                'sku' => $item->getSku(),
                                'base_price' => $item->getBasePrice(),
                                'price' => $item->getPrice(),
                                'base_row_total' => $item->getBaseRowTotal(),
                                'row_total' => $item->getRowTotal(),
                                'base_tax_amount' => $item->getBaseTaxAmount(),
                                'tax_amount' => $item->getTaxAmount(),
                                'qty' => $item->getQty(),
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
