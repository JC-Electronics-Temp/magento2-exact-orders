<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Model\ResourceModel\Attachment as ResourceModel;
use JcElectronics\ExactOrders\Model\ResourceModel\Attachment\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
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
        private readonly SearchResultsInterfaceFactory $searchResultsFactory,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly Uploader $uploader
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
            throw NoSuchEntityException::singleField(AttachmentInterface::KEY_ID, $id);
        }

        return $attachment;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByEntity(
        int $entityId,
        string $entityType
    ): AttachmentInterface {
        $items = $this->getList(
            $this->searchCriteriaBuilder
                ->addFilter(AttachmentInterface::KEY_ENTITY_ID, $entityId)
                ->addFilter(AttachmentInterface::KEY_ENTITY_TYPE_ID, $entityType)
                ->create()
        )->getItems();

        if (count($items) === 0) {
            throw NoSuchEntityException::doubleField(
                AttachmentInterface::KEY_ENTITY_ID,
                $entityId,
                AttachmentInterface::KEY_ENTITY_TYPE_ID,
                $entityType
            );
        }

        /** @var AttachmentInterface $item */
        $item = current($items);

        return $item;
    }

    public function delete(AttachmentInterface $attachment): void
    {
        $this->resourceModel->delete($attachment);
    }

    public function save(AttachmentInterface $attachment): AttachmentInterface
    {
        $this->resourceModel->save($attachment);

        // Save the file to the server
        $this->uploader->upload($attachment);

        return $attachment;
    }

    public function getList(
        SearchCriteriaInterface $searchCriteria
    ): SearchResultsInterface {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }
}
