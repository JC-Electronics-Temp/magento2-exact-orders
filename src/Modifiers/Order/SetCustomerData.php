<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class SetCustomerData extends AbstractModifier
{
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct(
            $orderRepository,
            $searchCriteriaBuilder
        );
    }

    public function supports(mixed $entity): bool
    {
        return parent::supports($entity) && !$entity->getOrderId();
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface         $result
     *
     * @return OrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $customer = $this->customerRepository
            ->getById($model->getMagentoCustomerId());

        $result->setCustomerId($customer->getId())
            ->setCustomerEmail($customer->getEmail())
            ->setCustomerPrefix($customer->getPrefix())
            ->setCustomerFirstname($customer->getFirstname())
            ->setCustomerMiddlename($customer->getMiddlename())
            ->setCustomerLastname($customer->getLastname())
            ->setCustomerSuffix($customer->getSuffix())
            ->setCustomerIsGuest(false)
            ->setCustomerGroupId($customer->getGroupId());

        return $result;
    }
}
