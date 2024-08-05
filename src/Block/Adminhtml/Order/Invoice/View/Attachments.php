<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Block\Adminhtml\Order\Invoice\View;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Magento\Sales\Block\Adminhtml\Order\Invoice\View;

class Attachments extends \Magento\Backend\Block\Template
{
    private Registry $registry;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->registry = $registry;

        parent::__construct(
            $context,
            $data,
            $jsonHelper,
            $directoryHelper
        );
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
