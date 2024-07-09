<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Sales\Api\Data\OrderInterface;

class SetExternalOrderData extends AbstractModifier
{
    /**
     * @param ExternalOrderInterface $entity
     */
    public function supports(mixed $entity): bool
    {
        return $entity instanceof ExternalOrderInterface &&
            $entity->getExtOrderId();
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface         $result
     *
     * @return OrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        if ($result->getExtOrderId()) {
            return $result;
        }

        $result->setExtOrderId($model->getExtOrderId())
            ->setExtCustomerId($model->getExternalCustomerId());

        return $result;
    }
}
