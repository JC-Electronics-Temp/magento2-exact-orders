<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Framework\DataObject;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ExternalOrder extends DataObject implements ExternalOrderInterface
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

    public function getOrderId(): ?string
    {
        return $this->_getData(self::KEY_ORDER_ID);
    }

    public function setOrderId(string $orderId): self
    {
        $this->setData(self::KEY_ORDER_ID, $orderId);

        return $this;
    }

    public function getInvoiceIds(): array
    {
        return $this->_getData(self::KEY_INVOICE_IDS) ?? [];
    }

    public function setInvoiceIds(array $invoiceIds): self
    {
        $this->setData(self::KEY_INVOICE_IDS, $invoiceIds);

        return $this;
    }

    public function getMagentoOrderId(): ?string
    {
        return $this->_getData(self::KEY_MAGENTO_ORDER_ID);
    }

    public function setMagentoOrderId(string $orderId): self
    {
        $this->setData(self::KEY_MAGENTO_ORDER_ID, $orderId);

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

    public function getExternalCustomerId(): ?string
    {
        return $this->_getData(self::KEY_EXTERNAL_CUSTOMER_ID);
    }

    public function setExternalCustomerId(string $externalCustomerId): self
    {
        $this->setData(self::KEY_EXTERNAL_CUSTOMER_ID, $externalCustomerId);

        return $this;
    }

    public function getExtOrderId(): ?string
    {
        return $this->_getData(self::KEY_EXTERNAL_ORDER_ID);
    }

    public function setExtOrderId(string $extOrderId): self
    {
        $this->setData(self::KEY_EXTERNAL_ORDER_ID, $extOrderId);

        return $this;
    }

    public function getBaseGrandtotal(): ?string
    {
        return $this->_getData(self::KEY_BASE_GRAND_TOTAL);
    }

    public function setBaseGrandtotal(string $grandTotal): self
    {
        $this->setData(self::KEY_BASE_GRAND_TOTAL, $this->formatCurrencyValue($grandTotal));

        return $this;
    }

    public function getBaseSubtotal(): ?string
    {
        return $this->_getData(self::KEY_BASE_SUBTOTAL);
    }

    public function setBaseSubtotal(string $subtotal): self
    {
        $this->setData(self::KEY_BASE_SUBTOTAL, $this->formatCurrencyValue($subtotal));

        return $this;
    }

    public function getGrandtotal(): ?string
    {
        return $this->_getData(self::KEY_GRAND_TOTAL);
    }

    public function setGrandtotal(string $grandTotal): self
    {
        $this->setData(self::KEY_GRAND_TOTAL, $this->formatCurrencyValue($grandTotal));

        return $this;
    }

    public function getSubtotal(): ?string
    {
        return   $this->_getData(self::KEY_SUBTOTAL);
    }

    public function setSubtotal(string $subtotal): self
    {
        $this->setData(self::KEY_SUBTOTAL, $this->formatCurrencyValue($subtotal));

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

    public function getShippingMethod(): ?string
    {
        return $this->_getData(self::KEY_SHIPPING_METHOD);
    }

    public function setShippingMethod(string $shippingMethod): self
    {
        $this->setData(self::KEY_SHIPPING_METHOD, $shippingMethod);

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

    public function getPaymentMethod(): ?string
    {
        return $this->_getData(self::KEY_PAYMENT_METHOD);
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->setData(self::KEY_PAYMENT_METHOD, $paymentMethod);

        return $this;
    }

    public function getBaseDiscountAmount(): ?string
    {
        return $this->_getData(self::KEY_BASE_DISCOUNT_AMOUNT);
    }

    public function setBaseDiscountAmount(string $discountAmount): self
    {
        $this->setData(self::KEY_BASE_DISCOUNT_AMOUNT, $this->formatCurrencyValue($discountAmount));

        return $this;
    }

    public function getDiscountAmount(): ?string
    {
        return $this->_getData(self::KEY_DISCOUNT_AMOUNT);
    }

    public function setDiscountAmount(string $discountAmount): self
    {
        $this->setData(self::KEY_DISCOUNT_AMOUNT, $this->formatCurrencyValue($discountAmount));

        return $this;
    }

    public function getOrderDate(): ?string
    {
        return $this->_getData(self::KEY_ORDER_DATE);
    }

    public function setOrderDate(string $orderDate): self
    {
        $this->setData(self::KEY_ORDER_DATE, $orderDate);

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
        return $this->_getData(self::KEY_TAX_AMOUNT);
    }

    public function setTaxAmount(string $taxAmount): self
    {
        $this->setData(self::KEY_TAX_AMOUNT, $this->formatCurrencyValue($taxAmount));

        return $this;
    }

    public function getBaseShippingAmount(): ?string
    {
        return $this->_getData(self::KEY_BASE_SHIPPING_AMOUNT);
    }

    public function setBaseShippingAmount(string $shippingAmount): self
    {
        $this->setData(self::KEY_BASE_SHIPPING_AMOUNT, $this->formatCurrencyValue($shippingAmount));

        return $this;
    }

    public function getShippingAmount(): ?string
    {
        return $this->_getData(self::KEY_SHIPPING_AMOUNT);
    }

    public function setShippingAmount(string $shippingAmount): self
    {
        $this->setData(self::KEY_SHIPPING_AMOUNT, $this->formatCurrencyValue($shippingAmount));

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

    public function getUpdatedAt(): ?string
    {
        return $this->_getData(self::KEY_UPDATED_AT);
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        $this->setData(self::KEY_UPDATED_AT, $updatedAt);

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
