<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order;

class ExternalOrder extends DataObject implements ExternalOrderInterface
{
    public function normalizeOrder(Order $order): self
    {
        $this->setData($order->getData());

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
