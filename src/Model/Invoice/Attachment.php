<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\Invoice;

use JcElectronics\ExactOrders\Api\Data\Invoice\AttachmentInterface;
use JcElectronics\ExactOrders\Model\Attachment as BaseAttachment;
use JcElectronics\ExactOrders\Model\ResourceModel\Invoice\Attachment as AttachmentResourceModel;
use Magento\Sales\Api\Data\InvoiceInterface;

class Attachment extends BaseAttachment implements AttachmentInterface
{
    public const CACHE_TAG = 'sales_invoice_attachment';

    private InvoiceInterface $invoice;

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = 'sales_invoice_attachment';

    protected function _construct(): void
    {
        $this->_init(AttachmentResourceModel::class);
    }

    public function getInvoice(): InvoiceInterface
    {
        return $this->invoice;
    }

    public function setInvoice(InvoiceInterface $invoice): AttachmentInterface
    {
        $this->invoice = $invoice;

        return $this;
    }
}
