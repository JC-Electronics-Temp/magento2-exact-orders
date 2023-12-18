<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Api\InvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface as MagentoInvoiceRepositoryInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        private readonly MagentoInvoiceRepositoryInterface $invoiceRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly InvoiceOrderInterface $invoiceOrder,
        private readonly array $modifiers = []
    ) {
    }

    public function getById(string $id): ExternalInvoiceInterface
    {
        return $this->processModifiers(
            $this->invoiceRepository->get($id)
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByIncrementId(string $incrementId): ExternalInvoiceInterface
    {
        $collection = $this->invoiceRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(InvoiceInterface::INCREMENT_ID, $incrementId)
                    ->create()
            )
            ->getItems();

        if (count($collection) === 0) {
            throw NoSuchEntityException::singleField(InvoiceInterface::INCREMENT_ID, $incrementId);
        }

        return $this->processModifiers(
            current($collection)
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByExternalId(string $id): ExternalInvoiceInterface
    {
        $collection = $this->invoiceRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter('ext_invoice_id', $id)
                    ->create()
            )
            ->getItems();

        if (count($collection) === 0) {
            throw NoSuchEntityException::singleField('ext_invoice_id', $id);
        }

        return $this->processModifiers(
            current($collection)
        );
    }

    public function getByOrder(string $id): array
    {
        return $this->getList(
            $this->searchCriteriaBuilder
                ->addFilter(InvoiceInterface::ORDER_ID, $id)
                ->create()
        );
    }

    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        return array_map(
            fn (InvoiceInterface $item) => $this->processModifiers($item),
            $this->invoiceRepository->getList($searchCriteria)->getItems()
        );
    }

    public function save(
        ExternalInvoiceInterface $invoice
    ): int {
        $invoiceId = $this->invoiceOrder->execute(
            current($invoice->getOrderIds())
        );

        foreach ($invoice->getAttachments() as $attachment) {
            /** @var AttachmentInterface $attachmentObject */
            $attachmentObject = $this->attachmentFactory->create();
            $attachmentObject->setParentId($invoiceId)
                ->setEntityTypeId(AttachmentInterface::ENTITY_TYPE_INVOICE)
                ->setFileName($attachment['name'])
                ->setFileContent($attachment['file_data']);

            $this->attachmentRepository->save($attachmentObject);
        }

        return $invoiceId;
    }

    private function processModifiers(mixed $invoice): mixed
    {
        $result = null;

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiers as $modifier) {
            if (!$modifier->supports($invoice)) {
                continue;
            }

            $result = $modifier->process($invoice, $result);
        }

        return $result;
    }
}
