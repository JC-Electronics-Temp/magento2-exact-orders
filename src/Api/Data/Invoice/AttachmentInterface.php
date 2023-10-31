<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data\Invoice;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface as BaseAttachmentInterface;
use Magento\Sales\Api\Data\InvoiceInterface;

interface AttachmentInterface extends BaseAttachmentInterface
{
    public function getInvoice(): InvoiceInterface;

    public function setInvoice(InvoiceInterface $invoice): self;
}
