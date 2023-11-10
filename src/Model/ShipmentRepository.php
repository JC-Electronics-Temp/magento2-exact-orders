<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalShipmentInterface;
use JcElectronics\ExactOrders\Api\ShipmentRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use JcElectronics\ExactOrders\Model\ExternalShipment\ItemFactory;
use JcElectronics\ExactOrders\Traits\FormatExternalOrderAddressTrait;
use JcElectronics\ExactOrders\Traits\FormatExternalShipmentDataTrait;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface as MagentoShipmentRepositoryInterface;
use Magento\Sales\Api\ShipOrderInterface;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    use FormatExternalShipmentDataTrait;
    use FormatExternalOrderAddressTrait;

    public function __construct(
        private readonly MagentoShipmentRepositoryInterface $shipmentRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ExternalShipmentFactory $externalShipmentFactory,
        private readonly AddressFactory $externalOrderAddressFactory,
        private readonly ItemFactory $externalShipmentItemFactory,
        private readonly Json $serializer,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly ShipOrderInterface $shipOrder
    ) {
    }

    public function getById(string $id): ExternalShipmentInterface
    {
        return $this->formatExternalShipmentData(
            $this->shipmentRepository->get($id)
        );
    }

    public function getByIncrementId(string $incrementId): ExternalShipmentInterface
    {
        return $this->formatExternalShipmentData(
            current(
                $this->shipmentRepository->getList(
                    $this->searchCriteriaBuilder
                        ->addFilter(ShipmentInterface::INCREMENT_ID, $incrementId)
                        ->create()
                )->getItems()
            )
        );
    }

    public function getByExternalId(string $id): ExternalShipmentInterface
    {
        return $this->formatExternalShipmentData(
            current(
                $this->shipmentRepository->getList(
                    $this->searchCriteriaBuilder
                        ->addFilter('ext_shipment_id', $id)
                        ->create()
                )->getItems()
            )
        );
    }

    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        return array_map(
            fn (ShipmentInterface $item) => $this->formatExternalShipmentData($item),
            $this->shipmentRepository->getList($searchCriteria)->getItems()
        );
    }

    public function save(
        ExternalShipmentInterface $shipment
    ): int {
        $shipmentId = $this->shipOrder->execute(
            $shipment->getOrderId()
        );

        foreach ($shipment->getAttachments() as $attachment) {
            /** @var AttachmentInterface $attachmentObject */
            $attachmentObject = $this->attachmentFactory->create();
            $attachmentObject->setParentId($shipmentId)
                ->setEntityTypeId(AttachmentInterface::ENTITY_TYPE_SHIPMENT)
                ->setFileName($attachment['name'])
                ->setFileContent($attachment['file_data']);

            $this->attachmentRepository->save($attachmentObject);
        }

        return $shipmentId;
    }
}
