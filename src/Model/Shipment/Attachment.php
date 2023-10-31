<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\Shipment;

use JcElectronics\ExactOrders\Api\Data\Shipment\AttachmentInterface;
use JcElectronics\ExactOrders\Model\Attachment as BaseAttachment;
use JcElectronics\ExactOrders\Model\ResourceModel\Shipment\Attachment as AttachmentResourceModel;
use Magento\Sales\Api\Data\ShipmentInterface;

class Attachment extends BaseAttachment implements AttachmentInterface
{
    public const CACHE_TAG = 'sales_shipment_attachment';

    private ShipmentInterface $shipment;

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = 'sales_shipment_attachment';

    protected function _construct(): void
    {
        $this->_init(AttachmentResourceModel::class);
    }

    public function getShipment(): ShipmentInterface
    {
        return $this->shipment;
    }

    public function setShipment(ShipmentInterface $shipment): AttachmentInterface
    {
        $this->shipment = $shipment;

        return $this;
    }
}
