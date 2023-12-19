<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\ExternalOrder;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\ItemInterface;
use Magento\Framework\DataObject;

class Item extends DataObject implements ItemInterface
{
    public function getOrderitemId(): ?string
    {
        return $this->_getData(self::KEY_ORDER_ITEM_ID);
    }

    public function setOrderitemId(string|int $orderItemId): self
    {
        $this->setData(self::KEY_ORDER_ITEM_ID, $orderItemId);

        return $this;
    }

    public function getOrderId(): string|int|null
    {
        return $this->_getData(self::KEY_ORDER_ID);
    }

    public function setOrderId(string|int $orderId): self
    {
        $this->setData(self::KEY_ORDER_ID, $orderId);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->_getData(self::KEY_NAME);
    }

    public function setName(string $name): self
    {
        $this->setData(self::KEY_NAME, $name);

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->_getData(self::KEY_SKU);
    }

    public function setSku(string $sku): self
    {
        $this->setData(self::KEY_SKU, $sku);

        return $this;
    }

    public function getBasePrice(): string|float|null
    {
        return $this->_getData(self::KEY_BASE_PRICE);
    }

    public function setBasePrice(string|float $price): self
    {
        $this->setData(self::KEY_BASE_PRICE, $this->formatCurrencyValue($price));

        return $this;
    }

    public function getPrice(): string|float|null
    {
        return $this->_getData(self::KEY_PRICE);
    }

    public function setPrice(string|float $price): self
    {
        $this->setData(self::KEY_PRICE, $this->formatCurrencyValue($price));

        return $this;
    }

    public function getBaseRowTotal(): string|float|null
    {
        return $this->_getData(self::KEY_BASE_ROW_TOTAL);
    }

    public function setBaseRowTotal(string|float $rowTotal): self
    {
        $this->setData(self::KEY_BASE_ROW_TOTAL, $this->formatCurrencyValue($rowTotal));

        return $this;
    }

    public function getRowTotal(): string|float|null
    {
        return $this->_getData(self::KEY_ROW_TOTAL);
    }

    public function setRowTotal(string|float $rowTotal): self
    {
        $this->setData(self::KEY_ROW_TOTAL, $this->formatCurrencyValue($rowTotal));

        return $this;
    }

    public function getBaseTaxAmount(): string|float|null
    {
        return $this->_getData(self::KEY_BASE_TAX_AMOUNT);
    }

    public function setBaseTaxAmount(string|float $taxAmount): self
    {
        $this->setData(self::KEY_BASE_TAX_AMOUNT, $this->formatCurrencyValue($taxAmount));

        return $this;
    }

    public function getTaxAmount(): string|float|null
    {
        return $this->_getData(self::KEY_TAX_AMOUNT);
    }

    public function setTaxAmount(string|float $taxAmount): self
    {
        $this->setData(self::KEY_TAX_AMOUNT, $this->formatCurrencyValue($taxAmount));

        return $this;
    }

    public function getQty(): string|float|null
    {
        return $this->_getData(self::KEY_QTY);
    }

    public function setQty(string|float $qty): self
    {
        $this->setData(self::KEY_QTY, $qty);

        return $this;
    }

    public function getBaseDiscountAmount(): string|float|null
    {
        return $this->_getData(self::KEY_BASE_DISCOUNT_AMOUNT);
    }

    public function setBaseDiscountAmount(string|float $discountAmount): self
    {
        $this->setData(self::KEY_BASE_DISCOUNT_AMOUNT, $this->formatCurrencyValue($discountAmount));

        return $this;
    }

    public function getDiscountAmount(): string|float|null
    {
        return $this->_getData(self::KEY_DISCOUNT_AMOUNT);
    }

    public function setDiscountAmount(string|float $discountAmount): self
    {
        $this->setData(self::KEY_DISCOUNT_AMOUNT, $this->formatCurrencyValue($discountAmount));

        return $this;
    }

    public function getAdditionalData(): array
    {
        return $this->_getData(self::KEY_ADDITIONAL_DATA) ?? [];
    }

    public function setAdditionalData(array $additionalData): self
    {
        $this->setData(self::KEY_ADDITIONAL_DATA, $additionalData);

        return $this;
    }

    private function formatCurrencyValue(string|int|float|null $value): ?float
    {
        return $value !== null
            ? (float) str_replace(',', '.', $value)
            : null;
    }
}
