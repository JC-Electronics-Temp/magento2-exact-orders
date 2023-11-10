<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data\ExternalOrder;

interface ItemInterface
{
    public const KEY_ORDER_ITEM_ID = 'orderitem_id',
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
    public function getOrderitemId(): ?string;

    /**
     * @param string $orderItemId
     *
     * @return self
     */
    public function setOrderitemId(string $orderItemId): self;

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
