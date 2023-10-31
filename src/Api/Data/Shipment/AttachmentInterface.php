<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data\Shipment;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface as BaseAttachmentInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

interface AttachmentInterface extends BaseAttachmentInterface
{
    public function getShipment(): ShipmentInterface;

    public function setShipment(ShipmentInterface $shipment): self;
}
