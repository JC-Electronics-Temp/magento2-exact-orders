<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\Sales\Shipment;

use JcElectronics\ExactOrders\Plugin\Sales\AbstractAddAttachment;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentSearchResultInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;

class AddShipmentAttachments extends AbstractAddAttachment
{
    public function afterGet(
        ShipmentRepositoryInterface $subject,
        ShipmentInterface $result
    ): ShipmentInterface {
        $extensionAttributes = $result->getExtensionAttributes();
        $extensionAttributes->setAttachments(
            $this->getAttachmentsByEntity($result, 'shipment')
        );

        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }

    public function afterGetList(
        ShipmentRepositoryInterface $subject,
        ShipmentSearchResultInterface $result
    ): ShipmentSearchResultInterface {
        foreach ($result->getItems() as $item) {
            $this->afterGet($subject, $item);
        }

        return $result;
    }

    public function afterSave(
        ShipmentRepositoryInterface $subject,
        $result,
        ShipmentInterface $shipment
    ): void {
        $attachments = $shipment->getExtensionAttributes()->getAttachments();

        foreach ($attachments as $attachment) {
            if ($attachment->getId()) {
                continue;
            }

            $this->attachmentRepository->save($attachment);
        }
    }
}
