<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalAttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Api\OrderRepositoryInterface;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly OrderManagementInterface $orderManagement,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly array $modifiers = []
    ) {
    }

    public function getById(string $id): ExternalOrderInterface
    {
        return $this->processModifiers(
            $this->orderRepository->get($id)
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByIncrementId(string $id): ExternalOrderInterface
    {
        $collection = $this->orderRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(OrderInterface::INCREMENT_ID, $id)
                    ->create()
            )
            ->getItems();

        if (count($collection) === 0) {
            throw NoSuchEntityException::singleField(OrderInterface::INCREMENT_ID, $id);
        }

        return $this->processModifiers(
            current($collection)
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByExternalId(string $id): ExternalOrderInterface
    {
        $collection = $this->orderRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(OrderInterface::EXT_ORDER_ID, $id)
                    ->create()
            )
            ->getItems();

        if (count($collection) === 0) {
            throw NoSuchEntityException::singleField(OrderInterface::EXT_ORDER_ID, $id);
        }

        return $this->processModifiers(
            current($collection)
        );
    }

    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        return array_map(
            fn (OrderInterface $item) => $this->processModifiers($item),
            $this->orderRepository->getList($searchCriteria)->getItems()
        );
    }

    public function save(ExternalOrderInterface $order): int
    {
        /** @var OrderInterface $order */
        $order  = $this->processModifiers($order);
        $result = $order->getEntityId()
            ? $this->orderRepository->save($order)
            : $this->orderManagement->place($order);

        $this->saveAttachments($order);

        return (int) $result->getEntityId();
    }

    private function saveAttachments(OrderInterface $order): void
    {
        /** @var ExternalAttachmentInterface[] $attachments */
        $attachments = $order->getExtensionAttributes()->getAttachments() ?? [];

        foreach ($attachments as $attachment) {
            try {
                $orderAttachment = $this->attachmentRepository->getByEntity(
                    (int) $order->getEntityId(),
                    AttachmentInterface::ENTITY_TYPE_ORDER
                );
            } catch (NoSuchEntityException) {
                /** @var AttachmentInterface $orderAttachment */
                $orderAttachment = $this->attachmentFactory->create();
                $orderAttachment->setEntityTypeId(AttachmentInterface::ENTITY_TYPE_ORDER)
                    ->setParentId($order->getEntityId());
            }

            $orderAttachment->setFileName($attachment->getName())
                ->setFileContent($attachment->getFileData());

            $this->attachmentRepository->save($orderAttachment);
        }
    }

    private function processModifiers(mixed $order): mixed
    {
        $result = null;

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiers as $modifier) {
            if (!$modifier->supports($order)) {
                continue;
            }

            $result = $modifier->process($order, $result);
        }

        return $result;
    }
}
