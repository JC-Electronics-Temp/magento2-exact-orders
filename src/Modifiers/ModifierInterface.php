<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers;

use Magento\Eav\Helper\Data;
use Magento\Framework\DataObject;

interface ModifierInterface
{
    /**
     * @param DataObject $model
     * @param DataObject $result
     *
     * @return DataObject
     */
    public function process($model, $result);

    /**
     * @param DataObject $entity
     *
     * @return bool
     */
    public function supports($entity): bool;
}
