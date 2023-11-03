<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\ShipmentRepositoryInterface;
use JcElectronics\ExactOrders\Model\ResourceModel\Attachment as AttachmentResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class Attachment extends AbstractModel implements AttachmentInterface, IdentityInterface
{
    private const CACHE_TAG = 'sales_exact_attachment';

    protected $_cacheTag = self::CACHE_TAG;

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ShipmentRepositoryInterface $shipmentRepository,
        AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    protected function _construct(): void
    {
        $this->_init(AttachmentResourceModel::class);
    }

    public function getIdentities(): array
    {
        return [$this->_cacheTag . '_' . $this->getId()];
    }

    public function getParentId(): int
    {
        return (int) $this->_getData(static::KEY_ENTITY_ID);
    }

    public function setParentId(int $parentId): self
    {
        $this->setData(static::KEY_ENTITY_ID, $parentId);

        return $this;
    }

    public function getEntityTypeId(): string
    {
        return $this->_getData(static::KEY_ENTITY_TYPE_ID);
    }

    public function setEntityTypeId(string $entityTypeId): self
    {
        $this->setData(static::KEY_ENTITY_TYPE_ID, $entityTypeId);

        return $this;
    }

    public function getFileName(): string
    {
        return $this->_getData(self::KEY_FILE_NAME);
    }

    public function setFileName(string $fileName): AttachmentInterface
    {
        $this->setData(self::KEY_FILE_NAME, $fileName);

        return $this;
    }

    public function getParentEntity(): InvoiceInterface|OrderInterface|ShipmentInterface
    {
        return match ($this->getEntityTypeId()) {
            'invoice' => $this->invoiceRepository->get($this->getParentId()),
            'order' => $this->orderRepository->get($this->getParentId()),
            'shipment' => $this->shipmentRepository->get($this->getParentId()),
        };
    }
}
