<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class SetOrderAddresses implements ModifierInterface
{
    public function __construct(
        private readonly Order\AddressFactory $orderAddressFactory
    ) {
    }

    /**
     * @param DataObject&ExternalOrderInterface $model
     * @param DataObject&OrderInterface         $result
     *
     * @return OrderInterface
     * @throws LocalizedException
     */
    public function process(
        DataObject $model,
        DataObject $result
    ): DataObject {
        if (!$result instanceof OrderInterface) {
            throw new LocalizedException(
                __('Expecting %1, but got %2', OrderInterface::class, get_class($result))
            );
        }

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

    public function supports(DataObject $entity): bool
    {
        return $entity instanceof ExternalOrderInterface;
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
            ->setTelephone($address->getTelephone());

        return $orderAddress;
    }
}
