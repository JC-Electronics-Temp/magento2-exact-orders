<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\ExternalShipment;

use JcElectronics\ExactOrders\Api\Data\ExternalShipment\ItemInterface;
use Magento\Framework\DataObject;

class Item extends DataObject implements ItemInterface
{
    public function getShipmentitemId(): ?string
    {
        return $this->_getData(self::KEY_SHIPMENT_ITEM_ID);
    }

    public function setShipmentitemId(string $shipmentItemId): self
    {
        $this->setData(self::KEY_SHIPMENT_ITEM_ID, $shipmentItemId);

        return $this;
    }

    public function getShipmentId(): ?string
    {
        return $this->_getData(self::KEY_SHIPMENT_ID);
    }

    public function setShipmentId(string $shipmentId): self
    {
        $this->setData(self::KEY_SHIPMENT_ID, $shipmentId);

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

    public function getPrice(): ?string
    {
        return $this->_getData(self::KEY_PRICE);
    }

    public function setPrice(string $price): self
    {
        $this->setData(self::KEY_PRICE, $this->formatCurrencyValue($price));

        return $this;
    }

    public function getRowTotal(): ?string
    {
        return $this->_getData(self::KEY_ROW_TOTAL);
    }

    public function setRowTotal(string $rowTotal): self
    {
        $this->setData(self::KEY_ROW_TOTAL, $this->formatCurrencyValue($rowTotal));

        return $this;
    }

    public function getQty(): ?string
    {
        return $this->_getData(self::KEY_QTY);
    }

    public function setQty(string $qty): self
    {
        $this->setData(self::KEY_QTY, $qty);

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->_getData(self::KEY_WEIGHT);
    }

    public function setWeight(string $weight): self
    {
        $this->setData(self::KEY_WEIGHT, $weight);

        return $this;
    }

    public function getAdditionalData(): array
    {
        return $this->_getData(self::KEY_ADDITIONAL_DATA);
    }

    public function setAdditionalData(array $additionalData): self
    {
        $this->setData(self::KEY_ADDITIONAL_DATA, $additionalData);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->_getData(self::KEY_DESCRIPTION);
    }

    public function setDescription(string $description): ItemInterface
    {
        $this->setData(self::KEY_DESCRIPTION, $description);

        return $this;
    }

    private function formatCurrencyValue(?string $value): ?string
    {
        return $value !== null
            ? str_replace(',', '.', $value)
            : null;
    }
}
