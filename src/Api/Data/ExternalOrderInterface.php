<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data;

use Magento\Sales\Model\Order;

interface ExternalOrderInterface
{
    public function getItems(): array;

    public function normalizeOrder(Order $order): self;

    /**
     * @param string     $key
     * @param int|string $index
     *
     * @return mixed
     */
    public function getData($key = '', $index = null);

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function setData($key, $value = null);
}
