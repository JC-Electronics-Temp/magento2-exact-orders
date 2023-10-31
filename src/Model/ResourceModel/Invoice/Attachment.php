<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\ResourceModel\Invoice;

use JcElectronics\ExactOrders\Api\Data\Invoice\AttachmentInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Attachment extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('sales_invoice_attachment', AttachmentInterface::KEY_ID);
    }
}
