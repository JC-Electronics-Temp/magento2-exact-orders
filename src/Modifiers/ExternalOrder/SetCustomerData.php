<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalOrder;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderFactory;

class SetCustomerData extends AbstractModifier
{
    /**
     * @param OrderInterface         $model
     * @param ExternalOrderInterface $result
     *
     * @return ExternalOrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setMagentoCustomerId($model->getCustomerId())
            ->setExternalCustomerId($model->getExtCustomerId());

        return $result;
    }
}
