<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data;

interface ExternalOrderInterface
{
    public const KEY_ID          = 'id',
        KEY_ORDER_ID             = 'order_id',
        KEY_INVOICE_IDS          = 'invoice_ids',
        KEY_MAGENTO_ORDER_ID     = 'magento_order_id',
        KEY_MAGENTO_CUSTOMER_ID  = 'magento_customer_id',
        KEY_EXTERNAL_CUSTOMER_ID = 'external_customer_id',
        KEY_EXTERNAL_ORDER_ID    = 'ext_order_id',
        KEY_GRAND_TOTAL          = 'grandtotal',
        KEY_SUBTOTAL             = 'subtotal',
        KEY_BASE_GRAND_TOTAL     = 'base_grandtotal',
        KEY_BASE_SUBTOTAL        = 'base_subtotal',
        KEY_STATE                = 'state',
        KEY_SHIPPING_METHOD      = 'shipping_method',
        KEY_SHIPPING_ADDRESS     = 'shipping_address',
        KEY_BILLING_ADDRESS      = 'billing_address',
        KEY_PAYMENT_METHOD       = 'payment_method',
        KEY_BASE_DISCOUNT_AMOUNT = 'base_discount_amount',
        KEY_DISCOUNT_AMOUNT      = 'discount_amount',
        KEY_ORDER_DATE           = 'order_date',
        KEY_BASE_TAX_AMOUNT      = 'base_tax_amount',
        KEY_TAX_AMOUNT           = 'tax_amount',
        KEY_BASE_SHIPPING_AMOUNT = 'base_shipping_amount',
        KEY_SHIPPING_AMOUNT      = 'shipping_amount',
        KEY_ITEMS                = 'items',
        KEY_MAGENTO_INCREMENT_ID = 'magento_increment_id',
        KEY_UPDATED_AT           = 'updated_at',
        KEY_ADDITIONAL_DATA      = 'additional_data',
        KEY_ATTACHMENTS          = 'attachments';

    /**
     * @return string|int|null
     */
    public function getId(): string|int|null;

    /**
     * @param string|int $id
     *
     * @return self
     */
    public function setId(string|int $id): self;

    /**
     * @return string|int|null
     */
    public function getOrderId(): string|int|null;

    /**
     * @param string|int $orderId
     *
     * @return self
     */
    public function setOrderId(string|int $orderId): self;

    /**
     * @return int[]
     */
    public function getInvoiceIds(): array;

    /**
     * @param int[] $invoiceIds
     *
     * @return self
     */
    public function setInvoiceIds(array $invoiceIds): self;

    /**
     * @return string|null
     */
    public function getMagentoOrderId(): ?string;

    /**
     * @param string|int $orderId
     *
     * @return self
     */
    public function setMagentoOrderId(string|int $orderId): self;

    /**
     * @return string|int|null
     */
    public function getMagentoCustomerId(): string|int|null;

    /**
     * @param string|int $magentoCustomerId
     *
     * @return self
     */
    public function setMagentoCustomerId(string|int $magentoCustomerId): self;

    /**
     * @return string|int|null
     */
    public function getExternalCustomerId(): string|int|null;

    /**
     * @param string|int $externalCustomerId
     *
     * @return self
     */
    public function setExternalCustomerId(string|int $externalCustomerId): self;

    /**
     * @return string|int|null
     */
    public function getExtOrderId(): string|int|null;

    /**
     * @param string|int $extOrderId
     *
     * @return self
     */
    public function setExtOrderId(string|int $extOrderId): self;

    /**
     * @return string|float|null
     */
    public function getBaseGrandtotal(): string|float|null;

    /**
     * @param string|float $grandTotal
     *
     * @return self
     */
    public function setBaseGrandtotal(string|float $grandTotal): self;

    /**
     * @return string|float|null
     */
    public function getBaseSubtotal(): string|float|null;

    /**
     * @param string|float $subtotal
     *
     * @return self
     */
    public function setBaseSubtotal(string|float $subtotal): self;

    /**
     * @return string|float|null
     */
    public function getGrandtotal(): string|float|null;

    /**
     * @param string|float $grandTotal
     *
     * @return self
     */
    public function setGrandtotal(string|float $grandTotal): self;

    /**
     * @return string|float|null
     */
    public function getSubtotal(): string|float|null;

    /**
     * @param string|float $subtotal
     *
     * @return self
     */
    public function setSubtotal(string|float $subtotal): self;

    /**
     * @return string|null
     */
    public function getState(): ?string;

    /**
     * @param string $state
     *
     * @return self
     */
    public function setState(string $state): self;

    /**
     * @return string|null
     */
    public function getShippingMethod(): ?string;

    /**
     * @param string $shippingMethod
     *
     * @return self
     */
    public function setShippingMethod(string $shippingMethod): self;

    /**
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface|null
     */
    public function getShippingAddress(): ?\JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface $shippingAddress
     *
     * @return self
     */
    public function setShippingAddress(
        \JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface $shippingAddress
    ): self;

    /**
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface|null
     */
    public function getBillingAddress(): ?\JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface $billingAddress
     *
     * @return self
     */
    public function setBillingAddress(
        \JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface $billingAddress
    ): self;

    /**
     * @return string|null
     */
    public function getPaymentMethod(): ?string;

    /**
     * @param string $paymentMethod
     *
     * @return self
     */
    public function setPaymentMethod(string $paymentMethod): self;

    /**
     * @return string|float|null
     */
    public function getBaseDiscountAmount(): string|float|null;

    /**
     * @param string|float $discountAmount
     *
     * @return self
     */
    public function setBaseDiscountAmount(string|float $discountAmount): self;

    /**
     * @return string|float|null
     */
    public function getDiscountAmount(): string|float|null;

    /**
     * @param string|float $discountAmount
     *
     * @return self
     */
    public function setDiscountAmount(string|float $discountAmount): self;

    /**
     * @return string|null
     */
    public function getOrderDate(): ?string;

    /**
     * @param string $orderDate
     *
     * @return self
     */
    public function setOrderDate(string $orderDate): self;

    /**
     * @return string|float|null
     */
    public function getBaseTaxAmount(): string|float|null;

    /**
     * @param string|float $taxAmount
     *
     * @return self
     */
    public function setBaseTaxAmount(string|float $taxAmount): self;

    /**
     * @return string|float|null
     */
    public function getTaxAmount(): string|float|null;

    /**
     * @param string|float $taxAmount
     *
     * @return self
     */
    public function setTaxAmount(string|float $taxAmount): self;

    /**
     * @return string|float|null
     */
    public function getBaseShippingAmount(): string|float|null;

    /**
     * @param string|float $shippingAmount
     *
     * @return self
     */
    public function setBaseShippingAmount(string|float $shippingAmount): self;

    /**
     * @return string|float|null
     */
    public function getShippingAmount(): string|float|null;

    /**
     * @param string|float $shippingAmount
     *
     * @return self
     */
    public function setShippingAmount(string|float $shippingAmount): self;

    /**
     * @return string|null
     */
    public function getMagentoIncrementId(): ?string;

    /**
     * @param string $magentoIncrementId
     *
     * @return self
     */
    public function setMagentoIncrementId(string $magentoIncrementId): self;

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string;

    /**
     * @param string $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(string $updatedAt): self;

    /**
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalOrder\ItemInterface[]
     */
    public function getItems(): array;

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\ExternalOrder\ItemInterface[] $items
     *
     * @return self
     */
    public function setItems(array $items): self;

    /**
     * @return \JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface[]
     */
    public function getAdditionalData(): array;

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface[] $additionalData
     *
     * @return self
     */
    public function setAdditionalData(array $additionalData): self;

    /**
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalAttachmentInterface[]
     */
    public function getAttachments(): array;

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\ExternalAttachmentInterface[] $attachments
     *
     * @return self
     */
    public function setAttachments(array $attachments): self;
}
