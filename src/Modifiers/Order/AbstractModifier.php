<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

abstract class AbstractModifier implements ModifierInterface
{
    public function __construct(
        protected readonly OrderRepositoryInterface $orderRepository,
        protected readonly SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    }

    public function supports(mixed $entity): bool
    {
        return $entity instanceof ExternalOrderInterface &&
                !$this->getOrderEntity($entity) instanceof OrderInterface;
    }

    protected function getOrderEntity(ExternalOrderInterface $order): ?OrderInterface
    {
        $collection = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(OrderInterface::INCREMENT_ID, $order->getMagentoIncrementId())
                ->create()
        );

        if ($collection->getTotalCount() === 0) {
            return null;
        }

        return current($collection->getItems());
    }
}
