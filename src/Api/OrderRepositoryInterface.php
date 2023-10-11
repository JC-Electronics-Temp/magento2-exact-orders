<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Sales\Api\Data\OrderInterface;

interface OrderRepositoryInterface
{
    /**
     * @param string $id
     *
     * @return ExternalOrderInterface
     */
    public function getById(string $id): ExternalOrderInterface;

    /**
     * @param string $incrementId
     * @param bool   $normalize
     *
     * @return ExternalOrderInterface|OrderInterface
     */
    public function getByIncrementId(
        string $incrementId,
        bool $normalize = true
    ): ExternalOrderInterface|OrderInterface;

    /**
     * @param string $id
     *
     * @return ExternalOrderInterface
     */
    public function getByExternalId(string $id): ExternalOrderInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array;

    /**
     * @param ExternalOrderInterface $order
     *
     * @return int
     */
    public function save(ExternalOrderInterface $order): int;
}
