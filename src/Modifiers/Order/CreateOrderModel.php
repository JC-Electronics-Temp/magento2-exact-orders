<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderFactory;

class CreateOrderModel extends AbstractModifier
{
    public function __construct(
        private readonly OrderFactory $orderFactory
    ) {
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface|null    $result
     *
     * @return OrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        if ($result === null) {
            $result = $this->orderFactory->create();
        }

        return $result;
    }
}
