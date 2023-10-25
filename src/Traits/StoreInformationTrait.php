<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Traits;

use Magento\Store\Api\Data\StoreInterface;

trait StoreInformationTrait
{
    public function getStoreById(int $storeId): StoreInterface
    {
        return $this->storeRepository->getById($storeId);
    }
}
