<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\Sales;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\AbstractModel;

abstract class AbstractAddAttachment
{
    public function __construct(
        protected readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    }

    protected function getAttachmentsByEntity(
        AbstractModel $entity,
        string $entityType
    ): array {
        return $this->attachmentRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(AttachmentInterface::KEY_ENTITY_ID, $entity->getId())
                    ->addFilter(AttachmentInterface::KEY_ENTITY_TYPE_ID, $entityType)
                    ->create()
            )
            ->getItems();
    }
}
