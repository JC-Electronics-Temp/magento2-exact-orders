<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command\Attachment;

use JcElectronics\ExactOrders\Model\AttachmentRepository;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;

class EntityProcessor
{
    public function __construct(
        $attachmentFactory,
        private readonly AttachmentRepository $attachmentRepository
    ){
        $this->attachmentFactory = $attachmentFactory;
    }

    public function process(array $attachment): void
    {
        /** @var AttachmentInterface $entity */
        $entity = $this->attachmentFactory->create()
            ->setParentId($entityId)
            ->setFileName($attachment['file']);

        $this->attachmentRepository->save($entity);
    }
}
