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
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\Payment\ExternalPayment;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Company\Api\Data\CompanyInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

trait FormatOrderDataTrait
{
    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    // phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
    // phpcs:disable Generic.Metrics.CyclomaticComplexity.MaxExceeded
    public function formatOrderData(ExternalOrderInterface $order, ?int $magentoOrderId): OrderInterface
    {
        $customer         = $this->getCustomerById((int) $order->getMagentoCustomerId());
        $company          = $this->getCompanyByCustomerId((int) $customer->getId());
        $store            = $this->getStoreById((int) $customer->getStoreId());
        $orderInclTax     = ((float) $order->getTaxAmount()) > 0;
        $orderIncrementId = $order->getMagentoIncrementId()
            ?? (
                $this->config->useExternalIncrementId()
                    ? $order->getExtOrderId()
                    : null
            );

        $grandTotal = (float)($order->getBaseGrandtotal() ?? $order->getGrandtotal());
        $baseDiscount = (float)($order->getBaseDiscountAmount() ?? $order->getDiscountAmount());
        $baseShippingAmount = (float)($order->getBaseShippingAmount() ?? $order->getShippingAmount());
        $baseSubtotal = (float)($order->getBaseSubtotal() ?? $order->getSubtotal());
        $baseTaxAmount = (float)($order->getBaseTaxAmount() ?? $order->getTaxAmount());
        $shippingMethod = $this->getShippingMethod($order->getShippingMethod(), (int) $store->getId());

        return $this->serviceInputProcessor->convertValue(
            [
                'base_discount_amount' => $baseDiscount,
                'base_grand_total' => $grandTotal,
                'base_shipping_amount' => $baseShippingAmount,
                'base_shipping_incl_tax' => $orderInclTax ? $baseShippingAmount : 0,
                'base_shipping_tax_amount' => 0,
                'base_subtotal' => $baseSubtotal,
                'base_subtotal_incl_tax' => $orderInclTax ? $baseSubtotal : 0,
                'base_tax_amount' => $baseTaxAmount,
                'base_total_due' => 0,
                'base_total_paid' => $grandTotal,
                'base_total_qty_ordered' => array_sum(
                    array_column($order->getItems(), 'qty')
                ),
                'discount_amount' => (float) $order->getDiscountAmount(),
                'grand_total' => (float) $order->getGrandtotal(),
                'shipping_amount' => (float) $order->getShippingAmount(),
                'shipping_incl_tax' => (float) $order->getShippingAmount(),
                'shipping_tax_amount' => 0,
                'subtotal' => (float) $order->getSubtotal(),
                'subtotal_incl_tax' => (float) $order->getSubtotal(),
                'tax_amount' => (float) $order->getTaxAmount(),
                'total_item_count' => count($order->getItems()),
                'total_paid' => (float) $order->getGrandtotal(),
                'total_qty_ordered' => array_sum(
                    array_column($order->getItems(), 'qty')
                ),
                'base_currency_code' => $this->config->getBaseCurrencyCode($store),
                'global_currency_code' => $this->config->getGlobalCurrencyCode(),
                'order_currency_code' => $this->config->getBaseCurrencyCode($store),
                'items' => $this->formatOrderItems($order->getItems(), $magentoOrderId),
                'billing_address' => $this->formatOrderAddress(
                    $order->getBillingAddress(),
                    AbstractAddress::TYPE_BILLING
                ),
                'extension_attributes' => [
                    'shipping_assignments' => [
                        [
                            'shipping' => [
                                'address' => $this->formatOrderAddress(
                                    $order->getShippingAddress(),
                                    AbstractAddress::TYPE_SHIPPING
                                ),
                                'method' => $shippingMethod['code'],
                                'total' => [
                                    'base_shipping_amount' => $baseShippingAmount,
                                    'base_shipping_incl_tax' => $orderInclTax ? $baseShippingAmount : 0,
                                    'base_shipping_tax_amount' => 0,
                                    'shipping_amount' => (float) $order->getShippingAmount(),
                                    'shipping_incl_tax' => (float) $order->getShippingAmount(),
                                    'shipping_tax_amount' => 0,
                                ]
                            ]
                        ]
                    ],
                    'is_external_order' => true,
                    'company_order_attributes' => $this->getCompanyOrderData($company)
                ],
            ],
            OrderInterface::class
        );
    }

    //phpcs:enable

    private function formatOrderItems(array $items, ?int $orderId): array
    {
        return array_reduce(
            $items,
            function (array $carry, ItemInterface $item) use ($orderId) {
                $product = $this->getProductBySku($item->getSku());
                $carry[] = [
                    'item_id' => $this->getMagentoOrderItemId($item, $orderId),
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
                    'product_id' => $product?->getId(),
                    'product_type' => $product?->getTypeId(),
                    'price_incl_tax' => (float) $item->getPrice(),
                    'qty_ordered' => (float) $item->getQty(),
                    'row_total' => (float) $item->getRowTotal(),
                    'row_total_incl_tax' => (float) $item->getRowTotal(),
                    'sku' => $item->getSku(),
                    'tax_amount' => (float) $item->getTaxAmount(),
                    'extension_attributes' => array_reduce(
                        $item->getAdditionalData(),
                        function (array $carry, AdditionalDataInterface $item) {
                            $carry[$item->getKey()] = $item->getValue();

                            return $carry;
                        },
                        []
                    )
                ];

                return $carry;
            },
            []
        );
    }

    private function formatOrderAddress(
        AddressInterface $address,
        string $type
    ): array {
        return [
            'address_type' => $type,
            'city' => $address->getCity(),
            'country_id' => $address->getCountry(),
            'company' => $address->getCompany(),
            'firstname' => $address->getFirstname(),
            'lastname' => $address->getLastname(),
            'postcode' => $address->getPostcode(),
            'street' => explode(
                "\n",
                $address->getStreet() ?? ''
            ),
            'telephone' => $address->getTelephone() ?? '-'
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
                    implode(', ', array_keys($orderStatuses))
                )
            );
        }

        return $orderStatuses[$state];
    }

    private function getProductBySku(string $sku): ?ProductInterface
    {
        try {
            return $this->productRepository->get($sku);
        } catch (NoSuchEntityException) {
            return null;
        }
    }

    private function getPaymentMethod(?string $code): string
    {
        return array_key_exists($code, $this->paymentHelper->getPaymentMethodList())
            ? $code
            : ExternalPayment::PAYMENT_METHOD_CODE;
    }

    private function getShippingMethod(?string $code, int $storeId): array
    {
        $shippingMethod = current(
            array_filter(
                $this->config->getShippingMethodMapping($storeId),
                static fn (array $option) => $option['value'] === $code
            )
        );

        return $shippingMethod === false
            ? $this->config->getDefaultShippingMethod($storeId)
            : [
                'code' => $shippingMethod['shipping_method'],
                'title' => $this->scopeConfig->getValue(
                    sprintf(
                        'carriers/%s/title',
                        current(
                            explode('_', $shippingMethod['shipping_method'])
                        )
                    ),
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                ) ?? $shippingMethod['shipping_method']
            ];
    }

    private function getCompanyOrderData(?CompanyInterface $company): array
    {
        return !$company instanceof CompanyInterface
            ? []
            : [
                'company_id' => $company->getId(),
                'company_name' => $company->getCompanyName()
            ];
    }

    private function getMagentoOrderId(string $incrementId): ?int
    {
        /** @var OrderInterface|false $order */
        $order = current(
            $this->orderRepository->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(OrderInterface::INCREMENT_ID, $incrementId)
                    ->create()
            )->getItems()
        );

        return $order ? (int) $order->getEntityId() : null;
    }

    private function getMagentoOrderItemId(ItemInterface $item, ?int $orderId): ?int
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderItemInterface::SKU, $item->getSku())
            ->addFilter(OrderItemInterface::QTY_ORDERED, $item->getQty());

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

    private function getStoreById(int $storeId): StoreInterface
    {
        return $this->storeRepository->getById($storeId);
    }

    private function getCustomerById(int $customerId): CustomerInterface
    {
        return $this->customerRepository->getById($customerId);
    }

    private function getCompanyByCustomerId(int $customerId): ?CompanyInterface
    {
        /** @var CompanyInterface|null $company */
        $company = $this->companyManagement->getByCustomerId($customerId);

        return $company;
    }
}
