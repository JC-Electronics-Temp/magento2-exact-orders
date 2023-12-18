<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\Config;
use JcElectronics\ExactOrders\Model\Payment\ExternalPayment;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\DataObject;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterfaceFactory;
use Magento\Sales\Api\Data\ShippingInterfaceFactory;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Store\Model\ScopeInterface;

class SetBillingAddress implements ModifierInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly AddressFactory $addressFactory
    ) {
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface         $result
     *
     * @return OrderInterface
     */
    public function process($model, $result)
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

    public function supports($entity): bool
    {
        return $entity instanceof ExternalOrderInterface;
    }

    private function getShippingMethod(string $code, int $storeId)
    {
        $shippingMethod = current(
            array_filter(
                $this->config->getShippignMethodMapping($storeId),
                static fn (array $option) => $option['value'] === $code
            )
        );

        return $shippingMethod === false
            ? $this->config->getDefaultShippingMethod($storeId)
            : [
                'code' => $shippingMethod['shipping_method'],
                'title' => $this->scopeConfig->getValue(
                    sprintf(
                        'carriers/%s/title',
                        current(
                            explode('_', $shippingMethod['shipping_method'])
                        )
                    ),
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                ) ?? $shippingMethod['shipping_method']
            ];
    }

    private function getShippingAssigment(ExternalOrderInterface $order, array $shippingMethod)
    {
        /** @var \Magento\Sales\Api\Data\ShippingInterface $shipping */
        $shipping = $this->shippingFactory->create();
        $shipping->setMethod($shippingMethod['code'])
            ->setTotal()
            ->setAddress();

        /** @var ShippingAssignmentInterface $shippingAssigment */
        $shippingAssigment = $this->shippingAssignmentFactory->create();
        $shippingAssigment->setShipping($shipping);

        return $shippingAssigment;
    }
}
