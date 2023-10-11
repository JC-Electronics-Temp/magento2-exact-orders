<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api;

use JcElectronics\ExactOrders\Api\Data\ExternalShipmentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ShipmentRepositoryInterface
{
    /**
     * @param string $id
     *
     * @return ExternalShipmentInterface
     */
    public function getById(string $id): ExternalShipmentInterface;

    /**
     * @param string $incrementId
     *
     * @return ExternalShipmentInterface
     */
    public function getByIncrementId(string $incrementId): ExternalShipmentInterface;

    /**
     * @param string $id
     *
     * @return ExternalShipmentInterface
     */
    public function getByExternalId(string $id): ExternalShipmentInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array;

    /**
     * @param ExternalShipmentInterface $externalShipment
     *
     * @return int
     */
    public function save(ExternalShipmentInterface $externalShipment): int;
}
