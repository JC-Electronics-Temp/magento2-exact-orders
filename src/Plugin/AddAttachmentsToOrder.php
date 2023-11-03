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
use Magento\Sales\Model\Order;

class AddAttachmentsToOrder
{
    public function __construct(
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
    }

    public function afterLoad(
        Order $subject,
        Order $result
    ): Order {
        if ($subject->getId()) {
            $result->setData('attachments', $this->getAttachmentsByOrder($subject));
        }

        return $result;
    }

    private function getAttachmentsByOrder(Order $subject): array
    {
        return $this->attachmentRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(
                        AttachmentInterface::KEY_ENTITY_ID,
                        $subject->getId()
                    )
                    ->create()
            )
            ->getItems();
    }
}
