<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterfaceFactory;
use Magento\Sales\Api\Data\ShippingInterfaceFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\AddressFactory;

class SetBillingAddress extends AbstractModifier
{
    public function __construct(
        private readonly AddressFactory $addressFactory,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface&Order   $result
     *
     * @return OrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $orderAddress = $model->getBillingAddress();
        $customer     = $this->customerRepository->getById($result->getCustomerId());

        /** @var OrderAddressInterface $billingAddress */
        $billingAddress = $this->addressFactory->create();
        $billingAddress->setAddressType(AbstractAddress::TYPE_BILLING)
            ->setFirstname(
                trim($orderAddress->getFirstname() ?? '')
                    ?: $customer->getFirstname()
            )
            ->setLastname(
                trim($orderAddress->getFirstname() ?? '')
                    ?: $customer->getLastname()
            )
            ->setCompany($orderAddress->getCompany())
            ->setStreet(
                explode("\n", $orderAddress->getStreet())
            )
            ->setCity($orderAddress->getCity())
            ->setPostcode($orderAddress->getPostcode())
            ->setCountryId($orderAddress->getCountry())
            ->setTelephone($orderAddress->getTelephone() ?? '-');

        $result->setBillingAddress($billingAddress);

        return $result;
    }
}
