<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\ViewModel\Adminhtml\Order\View;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Registry;

class Customer implements ArgumentInterface
{
    public function __construct(
        private readonly Registry $registry
    ) {

    }

    public function getOrder(): OrderInterface
    {
        return $this->registry->registry('current_order');
    }
}
