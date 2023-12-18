<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalInvoice;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Invoice;

class SetAddressInformation extends AbstractModifier
{
    public function __construct(
        private readonly AddressFactory $addressFactory
    ) {
    }

    /**
     * @param InvoiceInterface&Invoice $model
     * @param ExternalInvoiceInterface $result
     *
     * @return ExternalInvoiceInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setBillingAddress($this->formatAddress($model->getBillingAddress()))
            ->setShippingAddress($this->formatAddress($model->getShippingAddress()));

        return $result;
    }

    private function formatAddress(
        Address $address
    ): AddressInterface {
        /** @var AddressInterface $invoiceAddress */
        $invoiceAddress = $this->addressFactory->create();
        $invoiceAddress
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCompany($address->getCompany())
            ->setStreet(implode(' ', $address->getStreet()))
            ->setPostcode($address->getPostcode())
            ->setCity($address->getCity())
            ->setCountry($address->getCountryId())
            ->setTelephone($address->getTelephone());

        return $invoiceAddress;
    }
}
