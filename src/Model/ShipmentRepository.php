<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalShipmentInterface;
use JcElectronics\ExactOrders\Api\ShipmentRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalShipment\ItemFactory as ExternalShipmentItemFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory as ExternalOrderAddressFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface as MagentoShipmentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Model\Convert\Order;
use Magento\Sales\Model\Order as OrderModel;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Service\ShipmentService;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    public function __construct(
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly MagentoShipmentRepositoryInterface $shipmentRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ExternalShipmentFactory $externalShipmentFactory,
        private readonly ExternalShipmentItemFactory $externalShipmentItemFactory,
        private readonly ExternalOrderAddressFactory $externalOrderAddressFactory,
        private readonly Order $convertOrder,
        private readonly ShipmentService $shipmentService
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
        return $this->normalize($this->shipmentRepository->get($id));
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
                __('No shipment found with the specified increment ID.')
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

        return $collection->getItems();
    }

    /**
     * @param ExternalShipmentInterface $externalShipment
     *
     * @return int
     * @throws LocalizedException
     */
    public function save(
        ExternalShipmentInterface $externalShipment
    ): int {
        /** @var OrderModel $order */
        $order = $this->orderRepository->get($externalShipment->getOrderId());
        $magentoShipment = $this->convertOrder->toShipment($order);

        foreach ($order->getAllItems() as $item) {
            $magentoShipment->addItem(
                $this->convertOrder->itemToShipmentItem($item)
            );
        }

        $magentoShipment->register();

        $this->shipmentRepository->save($magentoShipment);
        $this->orderRepository->save($magentoShipment->getOrder());

        $this->shipmentService->notify(
            $magentoShipment->getEntityId()
        );

        return (int) $magentoShipment->getEntityId();
    }

    private function normalize(Shipment $shipment): ExternalShipmentInterface
    {
        $externalShipment = $this->externalShipmentFactory->create();
        $externalShipment->setData(
            [
                "shipment_id" => $shipment->getId(),
                "ext_shipment_id" => $shipment->getData('ext_invoice_id'),
                "customer_id" => $shipment->getOrder()->getCustomerId(),
                "order_id" => $shipment->getOrderId(),
                "shipment_status" => $shipment->getShipmentStatus(),
                "increment_id" => $shipment->getIncrementId(),
                "created_at" => $shipment->getCreatedAt(),
                "updated_at" => $shipment->getUpdatedAt(),
                "tracking" => $shipment->getTracks(),
                "additional_data" => [],
                "attachments" => [],
                'items' => array_map(
                    function (ShipmentItemInterface $item) use ($shipment) {
                        $externalItem = $this->externalShipmentItemFactory->create();
                        $externalItem->setData(
                            [
                                'shipmentitem_id' => $item->getEntityId(),
                                'shipment_id' => $shipment->getId(),
                                'name' => $item->getName(),
                                'sku' => $item->getSku(),
                                'price' => (string)$item->getPrice(),
                                'row_total' => (string)$item->getRowTotal(),
                                'qty' => $item->getQty(),
                                'additionalData' => []
                            ]
                        );

                        return $externalItem;
                    },
                    $shipment->getAllItems()
                ),
                'shipping_address' => $this->normalizeAddress($shipment->getShippingAddress()),
                'billing_address' => $this->normalizeAddress($shipment->getBillingAddress()),
            ]
        );

        return $externalShipment;
    }

    private function normalizeAddress(
        OrderAddressInterface $address
    ): AddressInterface {
        $externalAddress = $this->externalOrderAddressFactory->create();
        $externalAddress->setData(
            [
                'orderaddress_id' => $address->getEntityId(),
                'firstname' => $address->getFirstname(),
                'lastname' => $address->getLastname(),
                'company' => $address->getCompany(),
                'street' => $address->getStreet(),
                'postcode' => $address->getPostcode(),
                'city' => $address->getCity(),
                'country' => $address->getCountryId(),
                'additional_data' => []
            ]
        );

        return $externalAddress;
    }
}
