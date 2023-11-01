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
    public function __construct(
        Context $context,
        private readonly string $mainTable,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    protected function _construct(): void
    {
        $this->_init($this->mainTable, AttachmentInterface::KEY_ID);
    }
}
