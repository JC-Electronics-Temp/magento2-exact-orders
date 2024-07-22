<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Block\Adminhtml\Order\View;

use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;

class Attachments extends AbstractOrder
{
    public function getAttachments(): array
    {
        return $this->getOrder()->getExtensionAttributes()->getAttachments();
    }
}
