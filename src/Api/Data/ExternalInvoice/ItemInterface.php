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
     * @return string|int|null
     */
    public function getInvoiceitemId(): string|int|null;

    /**
     * @param string|int $invoiceItemId
     *
     * @return self
     */
    public function setInvoiceitemId(string|int $invoiceItemId): self;

    /**
     * @return string|int|null
     */
    public function getInvoiceId(): string|int|null;

    /**
     * @param string|int $invoiceId
     *
     * @return self
     */
    public function setInvoiceId(string|int $invoiceId): self;

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
     * @return string|float|null
     */
    public function getBasePrice(): string|float|null;

    /**
     * @param string|float $price
     *
     * @return self
     */
    public function setBasePrice(string|float $price): self;

    /**
     * @return string|float|null
     */
    public function getPrice(): string|float|null;

    /**
     * @param string|float $price
     *
     * @return self
     */
    public function setPrice(string|float $price): self;

    /**
     * @return string|float|null
     */
    public function getBaseRowTotal(): string|float|null;

    /**
     * @param string|float $rowTotal
     *
     * @return self
     */
    public function setBaseRowTotal(string|float $rowTotal): self;

    /**
     * @return string|float|null
     */
    public function getRowTotal(): string|float|null;

    /**
     * @param string|float $rowTotal
     *
     * @return self
     */
    public function setRowTotal(string|float $rowTotal): self;

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
    public function getQty(): string|float|null;

    /**
     * @param string|float $qty
     *
     * @return self
     */
    public function setQty(string|float $qty): self;

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
     * @return \JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface[]
     */
    public function getAdditionalData(): array;

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface[] $additionalData
     *
     * @return self
     */
    public function setAdditionalData(array $additionalData): self;
}
