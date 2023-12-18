<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;

abstract class AbstractModifier implements ModifierInterface
{
    public function supports(mixed $entity): bool
    {
        return $entity instanceof ExternalOrderInterface;
    }
}
