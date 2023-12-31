<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;

class AddExternalInvoiceIdToInvoice implements ObserverInterface
{
    public function execute(
        Observer $observer
    ): void {
        /** @var Invoice $invoice */
        $invoice = $observer->getData('invoice');

        $extensionAttributes = $invoice->getExtensionAttributes();
        $invoice->setData('ext_invoice_id', $extensionAttributes->getExtInvoiceId());
    }
}
