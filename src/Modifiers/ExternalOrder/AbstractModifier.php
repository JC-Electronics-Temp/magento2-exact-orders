<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalOrder;

use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Sales\Api\Data\OrderInterface;

abstract class AbstractModifier implements ModifierInterface
{
    public function supports(mixed $entity): bool
    {
        return $entity instanceof OrderInterface;
    }
}
