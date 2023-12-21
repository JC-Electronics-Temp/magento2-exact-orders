<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use Magento\Framework\DataObject;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ExternalInvoice extends DataObject implements ExternalInvoiceInterface
{
    public function getId(): ?string
    {
        return $this->_getData(self::KEY_ID);
    }

    public function setId(string $id): self
    {
        $this->setData(self::KEY_ID, $id);

        return $this;
    }

    public function getInvoiceId(): ?string
    {
        return $this->_getData(self::KEY_INVOICE_ID);
    }

    public function setInvoiceId(string $invoiceId): self
    {
        $this->setData(self::KEY_INVOICE_ID, $invoiceId);

        return $this;
    }

    public function getOrderIds(): array
    {
        return $this->_getData(self::KEY_ORDER_IDS) ?? [];
    }

    public function setOrderIds(array $orderIds): self
    {
        $this->setData(self::KEY_ORDER_IDS, $orderIds);

        return $this;
    }

    public function getMagentoInvoiceId(): ?string
    {
        return $this->_getData(self::KEY_MAGENTO_INVOICE_ID);
    }

    public function setMagentoInvoiceId(string $invoiceId): self
    {
        $this->setData(self::KEY_MAGENTO_INVOICE_ID, $invoiceId);

        return $this;
    }

    public function getMagentoCustomerId(): ?string
    {
        return $this->_getData(self::KEY_MAGENTO_CUSTOMER_ID);
    }

    public function setMagentoCustomerId(string $magentoCustomerId): self
    {
        $this->setData(self::KEY_MAGENTO_CUSTOMER_ID, $magentoCustomerId);

        return $this;
    }

    public function getExtInvoiceId(): ?string
    {
        return $this->_getData(self::KEY_EXTERNAL_INVOICE_ID);
    }

    public function setExtInvoiceId(string $extInvoiceId): self
    {
        $this->setData(self::KEY_EXTERNAL_INVOICE_ID, $extInvoiceId);

        return $this;
    }

    public function getPoNumber(): ?string
    {
        return $this->_getData(self::KEY_PO_NUMBER);
    }

    public function setPoNumber(string $poNumber): self
    {
        $this->setData(self::KEY_PO_NUMBER, $poNumber);

        return $this;
    }

    public function getBaseGrandtotal(): ?string
    {
        return $this->formatCurrencyValue($this->_getData(self::KEY_BASE_GRAND_TOTAL));
    }

    public function setBaseGrandtotal(string $grandTotal): self
    {
        $this->setData(self::KEY_BASE_GRAND_TOTAL, $grandTotal);

        return $this;
    }

    public function getBaseSubtotal(): ?string
    {
        return $this->formatCurrencyValue($this->_getData(self::KEY_BASE_SUBTOTAL));
    }

    public function setBaseSubtotal(string $subtotal): self
    {
        $this->setData(self::KEY_BASE_SUBTOTAL, $subtotal);

        return $this;
    }

    public function getGrandtotal(): ?string
    {
        return $this->formatCurrencyValue($this->_getData(self::KEY_GRAND_TOTAL));
    }

    public function setGrandtotal(string $grandTotal): self
    {
        $this->setData(self::KEY_GRAND_TOTAL, $grandTotal);

        return $this;
    }

    public function getSubtotal(): ?string
    {
        return   $this->formatCurrencyValue($this->_getData(self::KEY_SUBTOTAL));
    }

    public function setSubtotal(string $subtotal): self
    {
        $this->setData(self::KEY_SUBTOTAL, $subtotal);

        return $this;
    }

    public function getState(): ?string
    {
        return $this->_getData(self::KEY_STATE);
    }

    public function setState(string $state): self
    {
        $this->setData(self::KEY_STATE, $state);

        return $this;
    }

    public function getShippingAddress(): ?AddressInterface
    {
        return $this->_getData(self::KEY_SHIPPING_ADDRESS);
    }

    public function setShippingAddress(
        AddressInterface $shippingAddress
    ): self {
        $this->setData(self::KEY_SHIPPING_ADDRESS, $shippingAddress);

        return $this;
    }

    public function getBillingAddress(): ?AddressInterface
    {
        return $this->_getData(self::KEY_BILLING_ADDRESS);
    }

    public function setBillingAddress(AddressInterface $billingAddress): self
    {
        $this->setData(self::KEY_BILLING_ADDRESS, $billingAddress);

        return $this;
    }

    public function getBaseDiscountAmount(): ?string
    {
        return $this->_getData(self::KEY_BASE_DISCOUNT_AMOUNT);
    }

    public function setBaseDiscountAmount(string $discountAmount): self
    {
        $this->setData(self::KEY_BASE_DISCOUNT_AMOUNT, $discountAmount);

        return $this;
    }

    public function getDiscountAmount(): ?string
    {
        return $this->_getData(self::KEY_DISCOUNT_AMOUNT);
    }

    public function setDiscountAmount(string $discountAmount): self
    {
        $this->setData(self::KEY_DISCOUNT_AMOUNT, $discountAmount);

        return $this;
    }

    public function getInvoiceDate(): ?string
    {
        return $this->_getData(self::KEY_INVOICE_DATE);
    }

    public function setInvoiceDate(string $invoiceDate): self
    {
        $this->setData(self::KEY_INVOICE_DATE, $invoiceDate);

        return $this;
    }

    public function getBaseTaxAmount(): ?string
    {
        return $this->_getData(self::KEY_BASE_TAX_AMOUNT);
    }

    public function setBaseTaxAmount(string $taxAmount): self
    {
        $this->setData(self::KEY_BASE_TAX_AMOUNT, $taxAmount);

        return $this;
    }

    public function getTaxAmount(): ?string
    {
        return $this->formatCurrencyValue($this->_getData(self::KEY_TAX_AMOUNT));
    }

    public function setTaxAmount(string $taxAmount): self
    {
        $this->setData(self::KEY_TAX_AMOUNT, $taxAmount);

        return $this;
    }

    public function getBaseShippingAmount(): ?string
    {
        return $this->formatCurrencyValue($this->_getData(self::KEY_BASE_SHIPPING_AMOUNT));
    }

    public function setBaseShippingAmount(string $shippingAmount): self
    {
        $this->setData(self::KEY_BASE_SHIPPING_AMOUNT, $shippingAmount);

        return $this;
    }

    public function getShippingAmount(): ?string
    {
        return $this->formatCurrencyValue($this->_getData(self::KEY_SHIPPING_AMOUNT));
    }

    public function setShippingAmount(string $shippingAmount): self
    {
        $this->setData(self::KEY_SHIPPING_AMOUNT, $shippingAmount);

        return $this;
    }

    public function getMagentoIncrementId(): ?string
    {
        return $this->_getData(self::KEY_MAGENTO_INCREMENT_ID);
    }

    public function setMagentoIncrementId(string $magentoIncrementId): self
    {
        $this->setData(self::KEY_MAGENTO_INCREMENT_ID, $magentoIncrementId);

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

    public function getAttachments(): array
    {
        return $this->_getData(self::KEY_ATTACHMENTS) ?? [];
    }

    public function setAttachments(array $attachments): self
    {
        $this->setData(self::KEY_ATTACHMENTS, $attachments);

        return $this;
    }

    public function getItems(): array
    {
        return $this->_getData(self::KEY_ITEMS) ?? [];
    }

    public function setItems(array $items): self
    {
        $this->setData(self::KEY_ITEMS, $items);

        return $this;
    }

    private function formatCurrencyValue(?string $value): ?string
    {
        return $value !== null
            ? str_replace(',', '.', $value)
            : null;
    }
}
