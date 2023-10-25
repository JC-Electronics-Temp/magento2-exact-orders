<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalShipmentInterface;
use JcElectronics\ExactOrders\Api\ShipmentRepositoryInterface;
use JcElectronics\ExactOrders\Traits\FormatShipmentDataTrait;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface as MagentoShipmentRepositoryInterface;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    use FormatShipmentDataTrait;

    public function __construct(
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly MagentoShipmentRepositoryInterface $shipmentRepository,
        private readonly ServiceInputProcessor $serviceInputProcessor
    ) {
    }

    public function getById(string $id): ExternalShipmentInterface
    {
    }

    public function getByIncrementId(string $incrementId): ExternalShipmentInterface
    {
    }

    public function getByExternalId(string $id): ExternalShipmentInterface
    {
    }

    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
    }

    public function save(
        ExternalShipmentInterface $shipment
    ): int {
        $result = $this->shipmentRepository->save(
            $this->formatShipmentData(
                $shipment->getData(),
                $this->getOrderFromShipment($shipment)
            )
        );

        return (int) $result->getEntityId();
    }

    private function getOrderFromShipment(ExternalShipmentInterface $shipment): OrderInterface
    {
        return $this->orderRepository->get($shipment->getOrderId());
    }
}
