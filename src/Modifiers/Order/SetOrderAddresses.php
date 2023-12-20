<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class SetOrderAddresses extends AbstractModifier
{
    public function __construct(
        private readonly Order\AddressFactory $orderAddressFactory
    ) {
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface         $result
     *
     * @return OrderInterface
     */
    public function process(
        mixed $model,
        mixed $result
    ): mixed {
        $result->setBillingAddress(
            $this->formatOrderAddress(
                AbstractAddress::TYPE_BILLING,
                $model->getBillingAddress()
            )
        );

        $result->getExtensionAttributes()
            ->setShippingAssignments(
                $this->formatOrderAddress(
                    AbstractAddress::TYPE_SHIPPING,
                    $model->getShippingAddress()
                )
            );

        return $result;
    }

    private function formatOrderAddress(
        string $addressType,
        AddressInterface $address
    ): OrderAddressInterface {
        /** @var OrderAddressInterface $orderAddress */
        $orderAddress = $this->orderAddressFactory->create();
        $orderAddress->setAddressType($addressType)
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCompany($address->getCompany())
            ->setCity($address->getCity())
            ->setPostcode($address->getPostcode())
            ->setCountryId($address->getCountry())
            ->setStreet(
                explode(
                    "\n",
                    $address->getStreet()
                )
            )
            ->setTelephone($address->getTelephone() ?? '-');

        return $orderAddress;
    }
}
