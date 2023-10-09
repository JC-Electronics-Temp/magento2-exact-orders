<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalShipmentInterface;
use JcElectronics\ExactOrders\Api\ShipmentRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalShipmentFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface as MagentoShipmentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\Order\PaymentFactory;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    public function __construct(
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly MagentoShipmentRepositoryInterface $shipmentRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ExternalShipmentFactory $externalShipmentFactory
    ) {
    }

    /**
     * @param string $id
     *
     * @return ExternalShipmentInterface
     * @throws LocalizedException
     */
    public function getById(string $id): ExternalShipmentInterface
    {
        try {
            $shipment = $this->shipmentRepository->get($id);
        } catch (NoSuchEntityException) {
            throw new LocalizedException(
                __('No shipment found with the specified ID.')
            );
        }

        return $this->normalize($shipment);
    }

    /**
     * @param string $incrementId
     *
     * @return ExternalShipmentInterface
     * @throws LocalizedException
     */
    public function getByIncrementId(string $incrementId): ExternalShipmentInterface
    {
        $collection = $this->shipmentRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(InvoiceInterface::INCREMENT_ID, $incrementId)
                ->create()
        );

        if (!$collection->getItems()) {
            throw new LocalizedException(
                __('No order found with the specified increment ID.')
            );
        }

        return $this->normalize(
            current($collection->getItems())
        );
    }

    /**
     * @param string $id
     *
     * @return ExternalShipmentInterface
     * @throws LocalizedException
     */
    public function getByExternalId(string $id): ExternalShipmentInterface
    {
        $collection = $this->shipmentRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(
                    '',
                    $id
                )
                ->create()
        );

        if (!$collection->getItems()) {
            throw new LocalizedException(
                __('No order found with the specified external ID.')
            );
        }

        return $this->normalize(
            current($collection->getItems())
        );
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        $collection = $this->orderRepository->getList($searchCriteria);

        return array_reduce(
            $collection->getItems(),
            fn (ShipmentInterface $shipment) => $this->normalize($shipment),
            []
        );
    }

    /**
     * @param ExternalShipmentInterface $externalShipment
     *
     * @return ExternalShipmentInterface
     * @throws LocalizedException
     */
    public function save(
        ExternalShipmentInterface $externalShipment
    ): ExternalShipmentInterface {
    }

    private function normalize(
        ShipmentInterface $shipment
    ): ExternalShipmentInterface {
        /** @var ExternalShipmentInterface $externalShipment */
        $externalShipment = $this->externalShipmentFactory->create();
        $externalShipment->normalize($shipment);

        return $externalShipment;
    }
}
