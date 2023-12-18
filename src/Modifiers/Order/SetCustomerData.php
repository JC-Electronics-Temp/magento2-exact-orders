<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderInterface;

class SetCustomerData implements ModifierInterface
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
    public function process($model, $result)
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

    public function supports($entity): bool
    {
        return $entity instanceof ExternalOrderInterface;
    }
}
