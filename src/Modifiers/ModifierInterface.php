<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers;

interface ModifierInterface
{
    public function process(mixed $model, mixed $result): mixed;

    public function supports(mixed $entity): bool;
}
