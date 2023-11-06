<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Traits;

use JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\ItemInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

trait FormatOrderDataTrait
{
    use CustomerInformationTrait;
    use StoreInformationTrait;

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    // phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
    // phpcs:disable Generic.Metrics.CyclomaticComplexity.MaxExceeded
    public function formatOrderData(array $orderData): OrderInterface
    {
        $customer         = $this->getCustomerById((int) $orderData['magento_customer_id']);
        $company          = $this->getCompanyByCustomerId((int) $customer->getId());
        $store            = $this->getStoreById((int) $customer->getStoreId());
        $orderInclTax     = ((float) $orderData['tax_amount']) > 0;
        $orderIncrementId = $orderData['magento_increment_id']
            ?? (
                $this->config->useExternalIncrementId()
                    ? $orderData['external_order_id']
                    : null
            );

        $orderInvoiced = !in_array(
            strtolower($orderData['state']),
            [Order::STATE_PENDING_PAYMENT, Order::STATE_NEW]
        );

        return $this->serviceInputProcessor->convertValue(
            [
                'base_discount_amount' => (float) ($orderData['base_discount_amount'] ?? $orderData['discount_amount'] ?? 0),
                'base_discount_invoiced' => $orderInvoiced ? (float) ($orderData['base_discount_amount'] ?? $orderData['discount_amount'] ?? 0) : 0,
                'base_grand_total' => (float) ($orderData['base_grandtotal'] ?? $orderData['grandtotal']),
                'base_shipping_amount' => (float) ($orderData['base_shipping_amount'] ?? $orderData['shipping_amount'] ?? 0),
                'base_shipping_incl_tax' => $orderInclTax ? (float) ($orderData['base_shipping_amount'] ?? $orderData['shipping_amount'] ?? 0) : 0,
                'base_shipping_invoiced' => $orderInvoiced ? (float) ($orderData['base_shipping_amount'] ?? $orderData['shipping_amount'] ?? 0) : 0,
                'base_shipping_tax_amount' => 0,
                'base_subtotal' => (float) ($orderData['base_subtotal'] ?? $orderData['subtotal']),
                'base_subtotal_incl_tax' => $orderInclTax ? (float) ($orderData['base_subtotal'] ?? $orderData['subtotal']) : 0,
                'base_subtotal_invoiced' => $orderInvoiced ? (float) ($orderData['base_subtotal'] ?? $orderData['subtotal']) : 0,
                'base_tax_amount' => (float) ($orderData['base_tax_amount'] ?? $orderData['tax_amount']),
                'base_tax_invoiced' => $orderInvoiced ? (float) ($orderData['base_tax_amount'] ?? $orderData['tax_amount']) : 0,
                'base_total_due' => 0,
                'base_total_invoiced' => $orderInvoiced ? (float) ($orderData['base_grandtotal'] ?? $orderData['grandtotal']) : 0,
                'base_total_paid' => (float) ($orderData['base_grandtotal'] ?? $orderData['grandtotal']),
                'base_total_qty_ordered' => array_sum(
                    array_column($orderData['items'], 'qty')
                ),
                'created_at' => $orderData['order_date'],
                'customer_email' => $customer->getEmail(),
                'customer_firstname' => $customer->getFirstname(),
                'customer_group_id' => $customer->getGroupId(),
                'customer_is_guest' => 0,
                'customer_lastname' => $customer->getLastname(),
                'customer_middlename' => $customer->getMiddlename(),
                'customer_prefix' => $customer->getPrefix(),
                'customer_suffix' => $customer->getSuffix(),
                'discount_amount' => (float) ($orderData['discount_amount'] ?? 0),
                'discount_invoiced' => $orderInvoiced ? (float) ($orderData['discount_amount'] ?? 0) : 0,
                'ext_customer_id' => $orderData['external_customer_id'] ?? null,
                'ext_order_id' => $orderData['external_order_id'] ?? null,
                'grand_total' => (float) $orderData['grandtotal'],
                'increment_id' => $orderIncrementId,
                'shipping_amount' => (float) ($orderData['shipping_amount'] ?? 0),
                'shipping_incl_tax' => (float) ($orderData['shipping_amount'] ?? 0),
                'shipping_invoiced' => $orderInvoiced ? (float) ($orderData['shipping_amount'] ?? 0) : 0,
                'state' => strtolower($orderData['state']),
                'status' => $this->getOrderStatusByState($orderData['state']),
                'store_id' => $customer->getStoreId(),
                'subtotal' => (float) $orderData['subtotal'],
                'subtotal_incl_tax' => (float) $orderData['subtotal'],
                'subtotal_invoiced' => $orderInvoiced ? (float) $orderData['subtotal'] : 0,
                'tax_amount' => (float) $orderData['tax_amount'],
                'tax_invoiced' => $orderInvoiced ? (float) $orderData['tax_amount'] : 0,
                'total_invoiced' => $orderInvoiced ? (float) $orderData['grandtotal'] : 0,
                'total_item_count' => count($orderData['items']),
                'total_paid' => (float) $orderData['grandtotal'],
                'total_qty_ordered' => array_sum(
                    array_column($orderData['items'], 'qty')
                ),
                'base_currency_code' => $this->config->getBaseCurrencyCode($store),
                'global_currency_code' => $this->config->getGlobalCurrencyCode(),
                'order_currency_code' => $this->config->getBaseCurrencyCode($store),
                'updated_at' => $orderData['updated_at'],
                'items' => $this->formatOrderItems($orderData['items']),
                'billing_address' => $this->formatOrderAddress(
                    $orderData['billing_address'],
                    AbstractAddress::TYPE_BILLING
                ),
                'payment' => [
                    'amount_ordered' => $orderData['grandtotal'],
                    'amount_paid' => $orderData['grandtotal'],
                    'method' => $orderData['payment_method'] ?? 'unknown',
                    'base_amount_ordered' => (float) ($orderData['base_grandtotal'] ?? $orderData['grandtotal']),
                    'base_amount_paid' => (float) ($orderData['base_grandtotal'] ?? $orderData['grandtotal']),
                    'base_shipping_amount' => (float)($orderData['base_shipping_amount'] ?? $orderData['shipping_amount'] ?? 0),
                    'shipping_amount' => (float)($orderData['shipping_amount'] ?? $orderData['shipping_amount'] ?? 0),
                ],
                'extension_attributes' => [
                    'shipping_assignments' => [
                        [
                            'shipping' => [
                                'address' => $this->formatOrderAddress(
                                    $orderData['shipping_address'],
                                    AbstractAddress::TYPE_SHIPPING
                                ),
                                'method' => $orderData['shipping_method'] ?? 'unknown'
                            ]
                        ]
                    ],
                    'company_order_attributes' => [
                        'company_id' => $company->getId(),
                        'company_name' => $company->getCompanyName()
                    ]
                ]
            ],
            OrderInterface::class
        );
    }

    //phpcs:enable

    private function formatOrderItems(array $items): array
    {
        return array_reduce(
            $items,
            fn (array $carry, ItemInterface $item) => array_merge(
                $carry,
                [
                    [
                        'base_discount_amount' => (float) $item->getBaseDiscountAmount(),
                        'base_original_price' => (float) $item->getBasePrice(),
                        'base_price' => (float) $item->getBasePrice(),
                        'base_price_incl_tax' => (float) $item->getBasePrice(),
                        'base_row_total' => (float) $item->getRowTotal(),
                        'base_tax_amount' => (float) $item->getBaseTaxAmount(),
                        'discount_amount' => (float) $item->getDiscountAmount(),
                        'name' => $item->getName(),
                        'original_price' => (float) $item->getPrice(),
                        'price' => (float) $item->getPrice(),
                        'product_id' => $this->getProductIdBySku($item->getSku()),
                        'price_incl_tax' => (float) $item->getPrice(),
                        'qty_ordered' => (float) $item->getQty(),
                        'row_total' => (float) $item->getRowTotal(),
                        'row_total_incl_tax' => (float) $item->getRowTotal(),
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
                    ]
                ]
            ),
            []
        );
    }

    private function formatOrderAddress(
        AddressInterface $address,
        string $type
    ): array {
        return [
            'address_type' => $type,
            'city' => $address['city'],
            'country_id' => $address['country'],
            'firstname' => $address['firstname'],
            'lastname' => $address['lastname'],
            'postcode' => $address['postcode'],
            'street' => explode(
                "\n",
                $address['street'] ?? ''
            ),
            'telephone' => $address['telephone'] ?? '-'
        ];
    }

    /**
     * @throws LocalizedException
     */
    private function getOrderStatusByState(string $state): string
    {
        $orderStatuses = $this->config->getOrderStatuses();
        $state         = strtolower($state);

        if (!isset($orderStatuses[$state])) {
            throw new LocalizedException(
                __(
                    'Unknown order state "%1". Possible states: %2',
                    $state,
                    array_keys($orderStatuses)
                )
            );
        }

        return $orderStatuses[$state];
    }

    private function getProductIdBySku(string $sku): ?int
    {
        try {
            return (int) $this->productRepository->get($sku)->getId();
        } catch (NoSuchEntityException) {
            return null;
        }
    }
}
