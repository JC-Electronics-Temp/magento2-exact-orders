<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalShipmentInterface;
use Magento\Framework\DataObject;

class ExternalShipment extends DataObject implements ExternalShipmentInterface
{
    public function getId(): ?string
    {
        return $this->_getData(self::KEY_ID);
    }

    public function setId(string $id): ExternalShipmentInterface
    {
        $this->setData(self::KEY_ID, $id);

        return $this;
    }

    public function getShipmentId(): ?string
    {
        return $this->_getData(self::KEY_SHIPMENT_ID);
    }

    public function setShipmentId(string $shipmentId): ExternalShipmentInterface
    {
        $this->setData(self::KEY_SHIPMENT_ID, $shipmentId);

        return $this;
    }

    public function getInvoiceId(): ?string
    {
        return $this->_getData(self::KEY_INVOICE_ID);
    }

    public function setInvoiceId(string $invoiceId): ExternalShipmentInterface
    {
        $this->setData(self::KEY_INVOICE_ID, $invoiceId);

        return $this;
    }

    public function getOrderId(): string
    {
        return $this->_getData(self::KEY_ORDER_ID);
    }

    public function setOrderId(string $orderId): ExternalShipmentInterface
    {
        $this->setData(self::KEY_ORDER_ID, $orderId);

        return $this;
    }

    public function getCustomerId(): ?string
    {
        return $this->_getData(self::KEY_MAGENTO_CUSTOMER_ID);
    }

    public function setCustomerId(string $magentoCustomerId): ExternalShipmentInterface
    {
        $this->setData(self::KEY_MAGENTO_CUSTOMER_ID, $magentoCustomerId);

        return $this;
    }

    public function getExtShipmentId(): ?string
    {
        return $this->_getData(self::KEY_EXTERNAL_SHIPMENT_ID);
    }

    public function setExtShipmentId(string $extShipmentId): ExternalShipmentInterface
    {
        $this->setData(self::KEY_EXTERNAL_SHIPMENT_ID, $extShipmentId);

        return $this;
    }

    public function getShippingStatus(): ?string
    {
        return $this->_getData(self::KEY_SHIPMENT_STATUS);
    }

    public function setShippingStatus(string $status): ExternalShipmentInterface
    {
        $this->setData(self::KEY_SHIPMENT_STATUS, $status);

        return $this;
    }

    public function getShippingAddress(): ?AddressInterface
    {
        return $this->_getData(self::KEY_SHIPPING_ADDRESS);
    }

    public function setShippingAddress(
        AddressInterface $shippingAddress
    ): ExternalShipmentInterface {
        $this->setData(self::KEY_SHIPPING_ADDRESS, $shippingAddress);

        return $this;
    }

    public function getBillingAddress(): ?AddressInterface
    {
        return $this->_getData(self::KEY_BILLING_ADDRESS);
    }

    public function setBillingAddress(
        AddressInterface $billingAddress
    ): ExternalShipmentInterface {
        $this->setData(self::KEY_BILLING_ADDRESS, $billingAddress);

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->_getData(self::KEY_CREATED_AT);
    }

    public function setCreatedAt(string $createdAt): ExternalShipmentInterface
    {
        $this->setData(self::KEY_CREATED_AT, $createdAt);

        return $this;
    }

    public function getIncrementId(): ?string
    {
        return $this->_getData(self::KEY_MAGENTO_INCREMENT_ID);
    }

    public function setIncrementId(string $magentoIncrementId): ExternalShipmentInterface
    {
        $this->setData(self::KEY_MAGENTO_INCREMENT_ID, $magentoIncrementId);

        return $this;
    }

    public function getItems(): array
    {
        return $this->_getData(self::KEY_ITEMS);
    }

    public function setItems(array $items): ExternalShipmentInterface
    {
        $this->setData(self::KEY_ITEMS, $items);

        return $this;
    }

    public function getAdditionalData(): array
    {
        return $this->_getData(self::KEY_ADDITIONAL_DATA);
    }

    public function setAdditionalData(array $additionalData): ExternalShipmentInterface
    {
        $this->setData(self::KEY_ADDITIONAL_DATA, $additionalData);

        return $this;
    }

    public function getAttachments(): array
    {
        return $this->_getData(self::KEY_ATTACHMENTS);
    }

    public function setAttachments(array $attachments): ExternalShipmentInterface
    {
        $this->setData(self::KEY_ATTACHMENTS, $attachments);

        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->_getData(self::KEY_UPDATED_AT);
    }

    public function setUpdatedAt(string $updatedAt): ExternalShipmentInterface
    {
        $this->setData(self::KEY_UPDATED_AT, $updatedAt);

        return $this;
    }

    public function getTracking(): array
    {
        return $this->_getData(self::KEY_TRACKING) ?? [];
    }

    public function setTracking(array $tracking): ExternalShipmentInterface
    {
        $this->setData(self::KEY_TRACKING, $tracking);

        return $this;
    }
}
