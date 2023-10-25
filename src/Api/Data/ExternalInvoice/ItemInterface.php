<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data\ExternalInvoice;

use JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface;

interface ItemInterface
{
    public const KEY_INVOICE_ITEM_ID = 'invoiceitem_id',
        KEY_INVOICE_ID               = 'invoice_id',
        KEY_ORDER_ID               = 'order_id',
        KEY_NAME                   = 'name',
        KEY_SKU                    = 'sku',
        KEY_BASE_PRICE             = 'base_price',
        KEY_PRICE                  = 'price',
        KEY_BASE_ROW_TOTAL         = 'base_row_total',
        KEY_ROW_TOTAL              = 'row_total',
        KEY_BASE_TAX_AMOUNT        = 'base_tax_amount',
        KEY_TAX_AMOUNT             = 'tax_amount',
        KEY_QTY                    = 'qty',
        KEY_ADDITIONAL_DATA        = 'additional_data',
        KEY_BASE_DISCOUNT_AMOUNT   = 'base_discount_amount',
        KEY_DISCOUNT_AMOUNT        = 'discount_amount';

    /**
     * @return string|null
     */
    public function getInvoiceitemId(): ?string;

    /**
     * @param string $invoiceItemId
     *
     * @return self
     */
    public function setInvoiceitemId(string $invoiceItemId): self;

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
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self;

    /**
     * @return string|null
     */
    public function getSku(): ?string;

    /**
     * @param string $sku
     *
     * @return self
     */
    public function setSku(string $sku): self;

    /**
     * @return string|null
     */
    public function getBasePrice(): ?string;

    /**
     * @param string $price
     *
     * @return self
     */
    public function setBasePrice(string $price): self;

    /**
     * @return string|null
     */
    public function getPrice(): ?string;

    /**
     * @param string $price
     *
     * @return self
     */
    public function setPrice(string $price): self;

    /**
     * @return string|null
     */
    public function getBaseRowTotal(): ?string;

    /**
     * @param string $rowTotal
     *
     * @return self
     */
    public function setBaseRowTotal(string $rowTotal): self;

    /**
     * @return string|null
     */
    public function getRowTotal(): ?string;

    /**
     * @param string $rowTotal
     *
     * @return self
     */
    public function setRowTotal(string $rowTotal): self;

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
    public function getQty(): ?string;

    /**
     * @param string $qty
     *
     * @return self
     */
    public function setQty(string $qty): self;

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
     * @return AdditionalDataInterface[]
     */
    public function getAdditionalData(): array;

    /**
     * @param AdditionalDataInterface[] $additionalData
     *
     * @return self
     */
    public function setAdditionalData(array $additionalData): self;
}
