<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data;

interface ExternalShipmentInterface
{
    public const KEY_ID          = 'id',
        KEY_SHIPMENT_ID          = 'shipment_id',
        KEY_INVOICE_ID           = 'invoice_id',
        KEY_ORDER_ID             = 'order_id',
        KEY_MAGENTO_CUSTOMER_ID  = 'customer_id',
        KEY_EXTERNAL_SHIPMENT_ID  = 'ext_shipment_id',
        KEY_SHIPMENT_STATUS      = 'shipment_status',
        KEY_CREATED_AT           = 'created_at',
        KEY_UPDATED_AT           = 'updated_at',
        KEY_MAGENTO_INCREMENT_ID = 'increment_id',
        KEY_NAME                 = 'name',
        KEY_TRACKING             = 'tracking',
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
    public function getInvoiceId(): ?string;

    /**
     * @param string $invoiceId
     *
     * @return self
     */
    public function setInvoiceId(string $invoiceId): self;

    /**
     * @return string
     */
    public function getOrderId(): string;

    /**
     * @param string $orderId
     *
     * @return self
     */
    public function setOrderId(string $orderId): self;

    /**
     * @return string|null
     */
    public function getCustomerId(): ?string;

    /**
     * @param string $magentoCustomerId
     *
     * @return self
     */
    public function setCustomerId(string $magentoCustomerId): self;

    /**
     * @return string|null
     */
    public function getExtShipmentId(): ?string;

    /**
     * @param string $extShipmentId
     *
     * @return self
     */
    public function setExtShipmentId(string $extShipmentId): self;

    /**
     * @return string|null
     */
    public function getShippingStatus(): ?string;

    /**
     * @param string $status
     *
     * @return self
     */
    public function setShippingStatus(string $status): self;

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
    public function getCreatedAt(): ?string;

    /**
     * @param string $createdAt
     *
     * @return self
     */
    public function setCreatedAt(string $createdAt): self;

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
     * @return string|null
     */
    public function getIncrementId(): ?string;

    /**
     * @param string $magentoIncrementId
     *
     * @return self
     */
    public function setIncrementId(string $magentoIncrementId): self;

    /**
     * @return array
     */
    public function getTracking(): array;

    /**
     * @param array $tracking
     *
     * @return self
     */
    public function setTracking(array $tracking): self;

    /**
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalShipment\ItemInterface[]
     */
    public function getItems(): array;

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\ExternalShipment\ItemInterface[] $items
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
     * @return \JcElectronics\ExactOrders\Api\Data\AttachmentInterface[]
     */
    public function getAttachments(): array;

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\AttachmentInterface[] $attachments
     *
     * @return self
     */
    public function setAttachments(array $attachments): self;
}
