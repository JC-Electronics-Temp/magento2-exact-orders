<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\Config;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterfaceFactory;
use Magento\Sales\Api\Data\ShippingInterface;
use Magento\Sales\Api\Data\ShippingInterfaceFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\Order\ShippingTotal;
use Magento\Sales\Model\Order\ShippingTotalFactory;
use Magento\Store\Model\ScopeInterface;

class SetShippingInformation extends AbstractModifier
{
    public function __construct(
        private readonly Config $config,
        private readonly OrderExtensionFactory $extensionFactory,
        private readonly ShippingAssignmentInterfaceFactory $shippingAssignmentFactory,
        private readonly ShippingInterfaceFactory $shippingFactory,
        private readonly ShippingTotalFactory $shippingTotalFactory,
        private readonly AddressFactory $addressFactory,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly CustomerRepositoryInterface $customerRepository
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
        $shippingMethod = $this->getShippingMethod(
            $model->getShippingMethod(),
            (int) $result->getStoreId()
        );

        $result->setShippingDescription($shippingMethod['title']);
        $extensionAttributes = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttributes->setShippingAssignments(
            [$this->getShippingAssigment($model, $result, $shippingMethod)]
        );

        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }

    private function getShippingMethod(?string $code, int $storeId): array
    {
        if ($code === null) {
            return $this->config->getDefaultShippingMethod($storeId);
        }

        $shippingMethod = current(
            array_filter(
                $this->config->getShippingMethodMapping($storeId),
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

    // phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function getShippingAssigment(
        ExternalOrderInterface $externalOrder,
        Order $order,
        array $shippingMethod
    ): ShippingAssignmentInterface {
        /** @var ShippingTotal $totals */
        $totals = $this->shippingTotalFactory->create();
        $totals->setBaseShippingAmount($externalOrder->getBaseShippingAmount() ?: $externalOrder->getShippingAmount() ?: 0)
            ->setBaseShippingInclTax($externalOrder->getBaseShippingAmount() ?: $externalOrder->getShippingAmount() ?: 0)
            ->setBaseShippingTaxAmount(0)
            ->setShippingAmount($externalOrder->getShippingAmount() ?: 0)
            ->setShippingInclTax($externalOrder->getShippingAmount() ?: 0)
            ->setShippingTaxAmount(0);

        $orderAddress = $externalOrder->getShippingAddress();
        $customer     = $this->customerRepository->getById($order->getCustomerId());

        /** @var OrderAddressInterface $shippingAddress */
        $shippingAddress = $this->addressFactory->create();
        $shippingAddress->setAddressType(AbstractAddress::TYPE_SHIPPING)
            ->setFirstname(
                trim($orderAddress->getFirstname() ?? '')
                    ?: $customer->getFirstname()
            )
            ->setLastname(
                trim($orderAddress->getLastname() ?? '')
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

        /** @var ShippingInterface $shipping */
        $shipping = $this->shippingFactory->create();
        $shipping->setMethod($shippingMethod['code'])
            ->setTotal($totals)
            ->setAddress($shippingAddress);

        /** @var ShippingAssignmentInterface $shippingAssigment */
        $shippingAssigment = $this->shippingAssignmentFactory->create();
        $shippingAssigment->setShipping($shipping);

        return $shippingAssigment;
    }

    // phpcs:enable
}
