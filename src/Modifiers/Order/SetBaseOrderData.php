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
use Magento\Sales\Model\OrderFactory;

class SetBaseOrderData extends AbstractModifier
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Config $config,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly OrderExtensionFactory $extensionFactory
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
        $customer = $this->customerRepository->getById($model->getMagentoCustomerId());

        $result->setEntityId($this->getOrderEntityId($model))
            ->setIsVirtual(false)
            ->setCreatedAt($model->getOrderDate())
            ->setExtOrderId($model->getExtOrderId())
            ->setIncrementId($this->getIncrementId($model))
            ->setState($this->getOrderState($model))
            ->setStatus($this->getOrderStatus($model))
            ->setStoreId($customer->getStoreId())
            ->setUpdatedAt($model->getUpdatedAt());

        $extensionAttributes = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttributes->setIsExternalOrder(true);

        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }

    private function getOrderEntityId(ExternalOrderInterface $order): ?int
    {
        $collection = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(OrderInterface::INCREMENT_ID, $order->getMagentoIncrementId())
                ->create()
        );

        if ($collection->getTotalCount() === 0) {
            return null;
        }

        $entity = current($collection->getItems());

        return (int) $entity->getEntityId();
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
        $state         = strtolower($order->getState());

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
