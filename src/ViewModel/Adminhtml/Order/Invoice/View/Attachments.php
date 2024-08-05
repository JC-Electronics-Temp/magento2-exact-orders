<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\ViewModel\Adminhtml\Order\Invoice\View;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Attachments implements ArgumentInterface
{
    public function __construct(
        private readonly Registry $registry
    ) {
    }

    public function getAttachments(): array
    {
        return $this->getInvoice()->getExtensionAttributes()->getAttachments();
    }

    public function getInvoice()
    {
        return $this->registry->registry('current_invoice');
    }
}
