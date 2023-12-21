<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;

class SetCustomerData extends AbstractModifier
{
    public function __construct(
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
        $customer = $this->customerRepository
            ->getById($model->getMagentoCustomerId());

        $result->setExtCustomerId($model->getExternalCustomerId())
            ->setCustomerId($customer->getId())
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
