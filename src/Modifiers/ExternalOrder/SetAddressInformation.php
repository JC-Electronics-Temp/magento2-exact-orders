<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalOrder;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;

class SetAddressInformation extends AbstractModifier
{
    public function __construct(
        private readonly AddressFactory $addressFactory
    ) {
    }

    /**
     * @param OrderInterface&Order   $model
     * @param ExternalOrderInterface $result
     *
     * @return ExternalOrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setBillingAddress($this->formatAddress($model->getBillingAddress()))
            ->setShippingAddress($this->formatAddress($model->getShippingAddress()));

        return $result;
    }

    private function formatAddress(
        OrderAddressInterface $address
    ): AddressInterface {
        /** @var AddressInterface $orderAddress */
        $orderAddress = $this->addressFactory->create();
        $orderAddress->setOrderaddressId((string) $address->getEntityId())
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCompany($address->getCompany())
            ->setStreet(implode(' ', $address->getStreet()))
            ->setPostcode($address->getPostcode())
            ->setCity($address->getCity())
            ->setCountry($address->getCountryId())
            ->setTelephone($address->getTelephone());

        return $orderAddress;
    }
}
