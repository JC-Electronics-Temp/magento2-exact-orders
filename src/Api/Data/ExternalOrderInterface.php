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
     * @return string|null
     */
    public function getId(): ?string;

    /**
     * @param string $id
     *
     * @return self
     */
    public function setId(string $id): self;

    /**
     * @return string|null
     */
    public function getOrderId(): ?string;

    /**
     * @param string $orderId
     *
     * @return self
     */
    public function setOrderId(string $orderId): self;

    /**
     * @return int[]
     */
    public function getInvoiceIds(): array;

    /**
     * @param array $invoiceIds
     *
     * @return self
     */
    public function setInvoiceIds(array $invoiceIds): self;

    /**
     * @return string|null
     */
    public function getMagentoOrderId(): ?string;

    /**
     * @param string $orderId
     *
     * @return self
     */
    public function setMagentoOrderId(string $orderId): self;

    /**
     * @return string|null
     */
    public function getMagentoCustomerId(): ?string;

    /**
     * @param string $magentoCustomerId
     *
     * @return self
     */
    public function setMagentoCustomerId(string $magentoCustomerId): self;

    /**
     * @return string|null
     */
    public function getExternalCustomerId(): ?string;

    /**
     * @param string $externalCustomerId
     *
     * @return self
     */
    public function setExternalCustomerId(string $externalCustomerId): self;

    /**
     * @return string|null
     */
    public function getExtOrderId(): ?string;

    /**
     * @param string $extOrderId
     *
     * @return self
     */
    public function setExtOrderId(string $extOrderId): self;

    /**
     * @return string|null
     */
    public function getBaseGrandtotal(): ?string;

    /**
     * @param string $grandTotal
     *
     * @return self
     */
    public function setBaseGrandtotal(string $grandTotal): self;

    /**
     * @return string|null
     */
    public function getBaseSubtotal(): ?string;

    /**
     * @param string $subtotal
     *
     * @return self
     */
    public function setBaseSubtotal(string $subtotal): self;

    /**
     * @return string|null
     */
    public function getGrandtotal(): ?string;

    /**
     * @param string $grandTotal
     *
     * @return self
     */
    public function setGrandtotal(string $grandTotal): self;

    /**
     * @return string|null
     */
    public function getSubtotal(): ?string;

    /**
     * @param string $subtotal
     *
     * @return self
     */
    public function setSubtotal(string $subtotal): self;

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
     * @return string|null
     */
    public function getBaseDiscountAmount(): ?string;

    /**
     * @param string $discountAmount
     *
     * @return self
     */
    public function setBaseDiscountAmount(string $discountAmount): self;

    /**
     * @return string|null
     */
    public function getDiscountAmount(): ?string;

    /**
     * @param string $discountAmount
     *
     * @return self
     */
    public function setDiscountAmount(string $discountAmount): self;

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
     * @return string|null
     */
    public function getBaseTaxAmount(): ?string;

    /**
     * @param string $taxAmount
     *
     * @return self
     */
    public function setBaseTaxAmount(string $taxAmount): self;

    /**
     * @return string|null
     */
    public function getTaxAmount(): ?string;

    /**
     * @param string $taxAmount
     *
     * @return self
     */
    public function setTaxAmount(string $taxAmount): self;

    /**
     * @return string|null
     */
    public function getBaseShippingAmount(): ?string;

    /**
     * @param string $shippingAmount
     *
     * @return self
     */
    public function setBaseShippingAmount(string $shippingAmount): self;

    /**
     * @return string|null
     */
    public function getShippingAmount(): ?string;

    /**
     * @param string $shippingAmount
     *
     * @return self
     */
    public function setShippingAmount(string $shippingAmount): self;

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
