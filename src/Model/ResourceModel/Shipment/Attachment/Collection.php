<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\ResourceModel\Shipment\Attachment;

use JcElectronics\ExactOrders\Model\Shipment\Attachment;
use JcElectronics\ExactOrders\Model\ResourceModel\Shipment\Attachment as AttachmentResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'attachment_id';

    protected $_eventPrefix = 'sales_shipment_attachment_collection';

    protected $_eventObject = 'sales_shipment_attachment_collection';

    protected function _construct(): void
    {
        $this->_init(Attachment::class, AttachmentResourceModel::class);
    }
}
