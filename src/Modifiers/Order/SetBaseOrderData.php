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
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;

class SetBaseOrderData extends AbstractModifier
{
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Config $config,
        private readonly OrderExtensionFactory $extensionFactory
    ) {
        parent::__construct(
            $orderRepository,
            $searchCriteriaBuilder
        );
    }

    /**
     * @param ExternalOrderInterface $model
     * @param Order                  $result
     *
     * @return Order
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $customer = $this->customerRepository->getById($model->getMagentoCustomerId());

        $result->setIsVirtual(false)
            ->setCreatedAt($model->getOrderDate())
            ->setExtOrderId($model->getExtOrderId())
            ->setIncrementId($this->getIncrementId($model))
            ->setState($this->getOrderState($model))
            ->setStatus($this->getOrderStatus($model))
            ->setStoreId($customer->getStoreId())
            ->setUpdatedAt($model->getUpdatedAt())
            ->setEmailSent(0)
            ->setData('send_email', 0)
            ->setBaseToGlobalRate(1)
            ->setBaseToOrderRate(1)
            ->setStoreToBaseRate(0)
            ->setStoreToOrderRate(0);

        $extensionAttributes = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttributes->setIsExternalOrder(true);

        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }

    private function getIncrementId(ExternalOrderInterface $order): ?string
    {
        return $order->getMagentoIncrementId()
            ?? (
                $this->config->useExternalIncrementId()
                    ? $order->getExtOrderId()
                    : null
            );
    }

    private function getOrderState(ExternalOrderInterface $order): string
    {
        $orderState = strtolower($order->getState());

        return $orderState === 'completed' ? 'complete' : $orderState;
    }

    /**
     * @throws LocalizedException
     */
    private function getOrderStatus(ExternalOrderInterface $order): string
    {
        $orderStatuses = $this->config->getOrderStatuses();
        $state         = $this->getOrderState($order);

        if (!isset($orderStatuses[$state])) {
            throw new LocalizedException(
                __(
                    'Unknown order state "%1". Possible states: %2',
                    $state,
                    implode(', ', array_keys($orderStatuses))
                )
            );
        }

        return $orderStatuses[$state];
    }
}
