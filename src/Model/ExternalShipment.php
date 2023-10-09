<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalShipmentInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order\Shipment;

class ExternalShipment extends DataObject implements ExternalShipmentInterface
{
    public function normalize(Shipment $shipment): self
    {
        $this->setData($shipment->getData());

        return $this;
    }

    public function getItems(): array
    {
        return array_map(
            static fn (array $item) => new DataObject($item),
            $this->getData('items') ?? []
        );
    }
}
