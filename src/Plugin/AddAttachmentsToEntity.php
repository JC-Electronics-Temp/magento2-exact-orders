<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\AbstractModel;

class AddAttachmentsToEntity
{
    public function __construct(
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly string $entityTypeId
    ) {
    }

    public function afterLoad(
        AbstractModel $subject,
        AbstractModel $result
    ): AbstractModel {
        if ($result->getId()) {
            $result->setData('attachments', $this->getAttachmentsByEntity($result));
        }

        return $result;
    }

    private function getAttachmentsByEntity(AbstractModel $entity): array
    {
        return $this->attachmentRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(AttachmentInterface::KEY_ENTITY_ID, $entity->getId())
                    ->addFilter(AttachmentInterface::KEY_ENTITY_TYPE_ID, $this->entityTypeId)
                    ->create()
            )
            ->getItems();
    }
}
