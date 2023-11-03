<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\ResourceModel;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Attachment extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('sales_exact_attachment', AttachmentInterface::KEY_ID);
    }
}
