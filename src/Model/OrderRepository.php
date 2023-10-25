<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Api\OrderRepositoryInterface;
use JcElectronics\ExactOrders\Model\Traits\FormatOrderDataTrait;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    use FormatOrderDataTrait;

    public function __construct(
        private readonly OrderManagementInterface $orderManagement,
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CompanyManagementInterface $companyManagement,
        private readonly ServiceInputProcessor $serviceInputProcessor
    ) {
    }

    public function getById(string $id): ExternalOrderInterface
    {

    }

    public function getByIncrementId(string $incrementId): ExternalOrderInterface
    {

    }

    public function getByExternalId(string $id): ExternalOrderInterface
    {

    }

    public function getList(SearchCriteriaInterface $searchCriteria): array
    {

    }

    public function save(ExternalOrderInterface $order): int
    {
        $result = $this->orderRepository->save(
            $this->formatOrderData($order->getData())
        );

        return (int) $result->getEntityId();
    }

    private function getOrderStatusByState(string $state): string
    {
        return strtolower($state . ' status');
    }
}
