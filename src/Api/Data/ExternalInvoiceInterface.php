<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data;

interface ExternalInvoiceInterface
{
    public const KEY_ID          = 'id',
        KEY_INVOICE_ID           = 'invoice_id',
        KEY_ORDER_IDS            = 'order_ids',
        KEY_MAGENTO_INVOICE_ID   = 'magento_invoice_id',
        KEY_MAGENTO_CUSTOMER_ID  = 'magento_customer_id',
        KEY_EXTERNAL_INVOICE_ID  = 'ext_invoice_id',
        KEY_PO_NUMBER            = 'po_number',
        KEY_BASE_TAX_AMOUNT      = 'base_tax_amount',
        KEY_BASE_DISCOUNT_AMOUNT = 'base_discount_amount',
        KEY_BASE_SHIPPING_AMOUNT = 'base_shipping_amount',
        KEY_BASE_SUBTOTAL        = 'base_subtotal',
        KEY_BASE_GRAND_TOTAL     = 'base_grandtotal',
        KEY_TAX_AMOUNT           = 'tax_amount',
        KEY_DISCOUNT_AMOUNT      = 'discount_amount',
        KEY_SHIPPING_AMOUNT      = 'shipping_amount',
        KEY_SUBTOTAL             = 'subtotal',
        KEY_GRAND_TOTAL          = 'grandtotal',
        KEY_INVOICE_DATE         = 'invoice_date',
        KEY_STATE                = 'state',
        KEY_MAGENTO_INCREMENT_ID = 'magento_increment_id',
        KEY_ADDITIONAL_DATA      = 'additional_data',
        KEY_ITEMS                = 'items',
        KEY_SHIPPING_ADDRESS     = 'shipping_address',
        KEY_BILLING_ADDRESS      = 'billing_address',
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
    public function getInvoiceId(): ?string;

    /**
     * @param string $invoiceId
     *
     * @return self
     */
    public function setInvoiceId(string $invoiceId): self;

    /**
     * @return int[]
     */
    public function getOrderIds(): array;

    /**
     * @param array $orderIds
     *
     * @return self
     */
    public function setOrderIds(array $orderIds): self;

    /**
     * @return string|null
     */
    public function getMagentoInvoiceId(): ?string;

    /**
     * @param string $invoiceId
     *
     * @return self
     */
    public function setMagentoInvoiceId(string $invoiceId): self;

    /**
     * @return string|null
     */
    public function getMagentoCustomerId(): ?string;

    /**
     * @param string|null $magentoCustomerId
     *
     * @return self
     */
    public function setMagentoCustomerId(?string $magentoCustomerId): self;

    /**
     * @return string|null
     */
    public function getExtInvoiceId(): ?string;

    /**
     * @param string|null $extInvoiceId
     *
     * @return self
     */
    public function setExtInvoiceId(?string $extInvoiceId): self;

    /**
     * @return string|null
     */
    public function getPoNumber(): ?string;

    /**
     * @param string|null $poNumber
     *
     * @return self
     */
    public function setPoNumber(?string $poNumber): self;

    /**
     * @return string|float|null
     */
    public function getBaseGrandtotal(): string|float|null;

    /**
     * @param string $grandTotal
     *
     * @return self
     */
    public function setBaseGrandtotal(string|float $grandTotal): self;

    /**
     * @return string|float|null
     */
    public function getBaseSubtotal(): string|float|null;

    /**
     * @param string $subtotal
     *
     * @return self
     */
    public function setBaseSubtotal(string|float $subtotal): self;

    /**
     * @return string|float|null
     */
    public function getGrandtotal(): string|float|null;

    /**
     * @param string $grandTotal
     *
     * @return self
     */
    public function setGrandtotal(string|float $grandTotal): self;

    /**
     * @return string|float|null
     */
    public function getSubtotal(): string|float|null;

    /**
     * @param string $subtotal
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
     * @return string|float|null
     */
    public function getBaseDiscountAmount(): string|float|null;

    /**
     * @param string $discountAmount
     *
     * @return self
     */
    public function setBaseDiscountAmount(string|float $discountAmount): self;

    /**
     * @return string|float|null
     */
    public function getDiscountAmount(): string|float|null;

    /**
     * @param string $discountAmount
     *
     * @return self
     */
    public function setDiscountAmount(string|float $discountAmount): self;

    /**
     * @return string|null
     */
    public function getInvoiceDate(): ?string;

    /**
     * @param string $invoiceDate
     *
     * @return self
     */
    public function setInvoiceDate(string $invoiceDate): self;

    /**
     * @return string|float|null;
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
     * @param string $taxAmount
     *
     * @return self
     */
    public function setTaxAmount(string|float $taxAmount): self;

    /**
     * @return string|float|null
     */
    public function getBaseShippingAmount(): string|float|null;

    /**
     * @param string $shippingAmount
     *
     * @return self
     */
    public function setBaseShippingAmount(string|float $shippingAmount): self;

    /**
     * @return string|float|null
     */
    public function getShippingAmount(): string|float|null;

    /**
     * @param string $shippingAmount
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
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalInvoice\ItemInterface[]
     */
    public function getItems(): array;

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\ExternalInvoice\ItemInterface[] $items
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
