<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data\ExternalShipment;

use JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface;

interface ItemInterface
{
    public const KEY_SHIPMENT_ITEM_ID = 'shipmentitem_id',
        KEY_SHIPMENT_ID            = 'shipment_id',
        KEY_NAME                   = 'name',
        KEY_SKU                    = 'sku',
        KEY_PRICE                  = 'price',
        KEY_ROW_TOTAL              = 'row_total',
        KEY_WEIGHT                 = 'weight',
        KEY_QTY                    = 'qty',
        KEY_ADDITIONAL_DATA        = 'additional_data',
        KEY_DESCRIPTION            = 'description';

    /**
     * @return string|null
     */
    public function getShipmentitemId(): ?string;

    /**
     * @param string $shipmentItemId
     *
     * @return self
     */
    public function setShipmentitemId(string $shipmentItemId): self;

    /**
     * @return string|null
     */
    public function getShipmentId(): ?string;

    /**
     * @param string $shipmentId
     *
     * @return self
     */
    public function setShipmentId(string $shipmentId): self;

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
    public function getWeight(): ?string;

    /**
     * @param string $weight
     *
     * @return self
     */
    public function setWeight(string $weight): self;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(string $description): self;

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
