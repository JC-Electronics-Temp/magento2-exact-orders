<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalOrder;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;

class CreateExternalOrder extends AbstractModifier
{
    public function __construct(
        private readonly ExternalOrderFactory $orderFactory
    )
    {
    }

    public function process(mixed $model, mixed $result): mixed
    {
        if (!$result instanceof ExternalOrderInterface) {
            $result = $this->orderFactory->create();
        }

        return $result;
    }
}
