<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Block\Adminhtml\Order\View;

class Attachments extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    public function getAttachments(): array
    {
        return $this->getOrder()->getExtensionAttributes()->getAttachments();
    }
}
