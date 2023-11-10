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
use JcElectronics\ExactOrders\Model\ExternalInvoice\ItemFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use JcElectronics\ExactOrders\Traits\FormatExternalInvoiceDataTrait;
use JcElectronics\ExactOrders\Traits\FormatExternalOrderAddressTrait;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface as MagentoInvoiceRepositoryInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    use FormatExternalInvoiceDataTrait;
    use FormatExternalOrderAddressTrait;

    public function __construct(
        private readonly MagentoInvoiceRepositoryInterface $invoiceRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ExternalInvoiceFactory $externalInvoiceFactory,
        private readonly AddressFactory $externalOrderAddressFactory,
        private readonly ItemFactory $externalInvoiceItemFactory,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly InvoiceOrderInterface $invoiceOrder
    ) {
    }

    public function getById(string $id): ExternalInvoiceInterface
    {
        return $this->formatExternalInvoiceData(
            $this->invoiceRepository->get($id)
        );
    }

    public function getByIncrementId(string $incrementId): ExternalInvoiceInterface
    {
        return $this->formatExternalInvoiceData(
            current(
                $this->invoiceRepository->getList(
                    $this->searchCriteriaBuilder
                        ->addFilter(InvoiceInterface::INCREMENT_ID, $incrementId)
                        ->create()
                )->getItems()
            )
        );
    }

    public function getByExternalId(string $id): ExternalInvoiceInterface
    {
        return $this->formatExternalInvoiceData(
            current(
                $this->invoiceRepository->getList(
                    $this->searchCriteriaBuilder
                        ->addFilter('ext_invoice_id', $id)
                        ->create()
                )->getItems()
            )
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
            fn (InvoiceInterface $item) => $this->formatExternalInvoiceData($item),
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
}
