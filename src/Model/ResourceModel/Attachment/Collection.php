<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\ResourceModel\Attachment;

use JcElectronics\ExactOrders\Model\Attachment;
use JcElectronics\ExactOrders\Model\ResourceModel\Attachment as AttachmentResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'attachment_id';

    protected $_eventPrefix = 'sales_exact_attachment_collection';

    protected $_eventObject = 'sales_exact_attachment_collection';

    public function _construct(): void
    {
        $this->_init(
            Attachment::class,
            AttachmentResourceModel::class
        );
    }
}
