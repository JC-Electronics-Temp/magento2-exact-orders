<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderFactory;

class CreateOrderModel implements ModifierInterface
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
    public function process($model, $result)
    {
        if ($result === null) {
            $result = $this->orderFactory->create();
        }

        return $result;
    }

    public function supports($entity): bool
    {
        return $entity instanceof ExternalOrderInterface;
    }
}
