<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Model\ResourceModel\Attachment as ResourceModel;
use JcElectronics\ExactOrders\Model\ResourceModel\Attachment\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class AttachmentRepository implements AttachmentRepositoryInterface
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly ResourceModel $resourceModel,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    public function get(int $id): AttachmentInterface
    {
        /** @var AttachmentInterface $attachment */
        $attachment = $this->attachmentFactory->create();
        $this->resourceModel->load($attachment, $id);

        if (!$attachment->getId()) {
            throw NoSuchEntityException::singleField('attachment_id', $id);
        }

        return $attachment;
    }

    public function delete(AttachmentInterface $attachment): void
    {
        $this->resourceModel->delete($attachment);
    }

    public function save(AttachmentInterface $attachment): AttachmentInterface
    {
        $this->resourceModel->save($attachment);

        return $attachment;
    }

    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): SearchResultsInterface {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }
}
