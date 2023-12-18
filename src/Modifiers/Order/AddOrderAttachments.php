<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\Config;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderFactory;

class AddOrderAttachments implements ModifierInterface
{
    public function __construct(
        private readonly OrderExtensionFactory $extensionFactory
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
        $extensionAttributes = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttributes->setAttachments($model->getAttachments());

        return $result;
    }

    public function supports($entity): bool
    {
        return $entity instanceof ExternalOrderInterface;
    }
}
