<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface AttachmentRepositoryInterface
{
    public function get(int $id): AttachmentInterface;

    public function delete(AttachmentInterface $attachment): void;

    public function save(AttachmentInterface $attachment): AttachmentInterface;

    public function getByEntity(int $entityId, string $entityType): AttachmentInterface;

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
