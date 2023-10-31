<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\Order;

use JcElectronics\ExactOrders\Api\Data\Order\AttachmentInterface;
use JcElectronics\ExactOrders\Model\Attachment as BaseAttachment;
use JcElectronics\ExactOrders\Model\ResourceModel\Order\Attachment as AttachmentResourceModel;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;

class Attachment extends BaseAttachment implements AttachmentInterface
{
    public const CACHE_TAG = 'sales_order_attachment';

    private OrderInterface $order;

    protected $_cacheTag = self::CACHE_TAG;

    protected $_eventPrefix = 'sales_order_attachment';

    protected function _construct(): void
    {
        $this->_init(AttachmentResourceModel::class);
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function setOrder(OrderInterface $order): AttachmentInterface
    {
        $this->order = $order;

        return $this;
    }
}
