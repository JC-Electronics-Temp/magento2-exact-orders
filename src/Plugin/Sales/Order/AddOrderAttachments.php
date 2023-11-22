<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\Sales\Order;

use JcElectronics\ExactOrders\Plugin\Sales\AbstractAddAttachment;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class AddOrderAttachments extends AbstractAddAttachment
{
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $result
    ): OrderInterface {
        $extensionAttributes = $result->getExtensionAttributes();
        $extensionAttributes->setAttachments(
            $this->getAttachmentsByEntity($result, Order::ENTITY)
        );

        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }

    public function afterGetList(
        OrderRepositoryInterface $subject,
        OrderSearchResultInterface $result
    ): OrderSearchResultInterface {
        foreach ($result->getItems() as $item) {
            $this->afterGet($subject, $item);
        }

        return $result;
    }

    public function beforeSave(
        OrderRepositoryInterface $subject,
        OrderInterface&Order $order
    ): array {
        $order->setData(
            'is_external_order',
            (bool) $order->getExtensionAttributes()->getIsExternalOrder()
        );

        return [$order];
    }

    public function afterSave(
        OrderRepositoryInterface $subject,
        OrderInterface $result,
        OrderInterface $order
    ): OrderInterface {
        $attachments = $order->getExtensionAttributes()->getAttachments() ?? [];

        foreach ($attachments as $attachment) {
            if ($attachment->getId()) {
                continue;
            }

            $this->attachmentRepository->save($attachment);
        }

        return $result;
    }
}
