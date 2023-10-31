<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data\Order;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface as BaseAttachmentInterface;
use Magento\Sales\Api\Data\OrderInterface;

interface AttachmentInterface extends BaseAttachmentInterface
{
    public function getOrder(): OrderInterface;

    public function setOrder(OrderInterface $order): self;
}
