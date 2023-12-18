<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterfaceFactory;
use Magento\Sales\Api\Data\ShippingInterfaceFactory;
use Magento\Sales\Model\Order\AddressFactory;

class SetBillingAddress extends AbstractModifier
{
    public function __construct(
        private readonly AddressFactory $addressFactory
    ) {
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface         $result
     *
     * @return OrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $orderAddress = $model->getBillingAddress();

        /** @var OrderAddressInterface $billingAddress */
        $billingAddress = $this->addressFactory->create();
        $billingAddress->setAddressType(AbstractAddress::TYPE_BILLING)
            ->setTelephone($orderAddress->getTelephone())
            ->setFirstname($orderAddress->getFirstname())
            ->setLastname($orderAddress->getLastname())
            ->setCompany($orderAddress->getCompany())
            ->setStreet(
                explode("\n", $orderAddress->getStreet())
            )
            ->setCity($orderAddress->getCity())
            ->setPostcode($orderAddress->getPostcode())
            ->setCountryId($orderAddress->getCountry())
            ->setTelephone($orderAddress->getTelephone());

        $result->setBillingAddress($billingAddress);

        return $result;
    }
}
