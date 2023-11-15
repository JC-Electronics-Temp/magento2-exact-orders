<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Sales\Model\AbstractModel;
use Magento\Sales\Model\ResourceModel\EntityAbstract;

class UploadAttachmentsOnEntity
{
    public function __construct(
        private readonly AttachmentRepositoryInterface $attachmentRepository
    ) {
    }

    public function afterSave(
        EntityAbstract $subject,
        EntityAbstract $result,
        AbstractModel $object
    ): AbstractModel {
        /** @var AttachmentInterface[] $attachments */
        $attachments = $object->getData('attachments') ?? [];

        foreach ($attachments as $attachment) {
            if ($attachment->getId()) {
                continue;
            }

            $this->attachmentRepository->save($attachment);
        }

        return $object;
    }
}
