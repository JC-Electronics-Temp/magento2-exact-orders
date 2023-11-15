<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\ResourceModel;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Attachment extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('sales_exact_attachment', AttachmentInterface::KEY_ID);
    }
}
