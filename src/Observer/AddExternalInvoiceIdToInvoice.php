<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddExternalInvoiceIdToInvoice implements ObserverInterface
{
    public function execute(
        Observer $observer
    ): void {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData('invoice');

        $extensionAttributes = $invoice->getExtensionAttributes();
        $invoice->setData('ext_invoice_id', $extensionAttributes->getExtInvoiceId());
    }
}
