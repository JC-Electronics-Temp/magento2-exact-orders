<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\SearchResultsInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface OrderRepositoryInterface
{
    /**
     * @param string $id
     *
     * @return ExternalOrderInterface
     */
    public function getById(string $id): ExternalOrderInterface;

    /**
     * @param string $id
     *
     * @return ExternalOrderInterface
     */
    public function getByIncrementId(string $id): ExternalOrderInterface;

    /**
     * @param string $id
     *
     * @return ExternalOrderInterface
     */
    public function getByExternalId(string $id): ExternalOrderInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalOrder\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param ExternalOrderInterface $order
     *
     * @return int
     */
    public function save(ExternalOrderInterface $order): int;
}
