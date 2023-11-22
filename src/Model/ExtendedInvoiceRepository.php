<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExtendedInvoiceInterface;
use JcElectronics\ExactOrders\Api\ExtendedInvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Model\ResourceModel\ExtendedInvoice as ResourceModel;
use Magento\Framework\Exception\NoSuchEntityException;

class ExtendedInvoiceRepository implements ExtendedInvoiceRepositoryInterface
{
    public function __construct(
        private readonly ResourceModel $resourceModel,
        private readonly ExtendedInvoiceFactory $extendedInvoiceFactory
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    public function get(int $id): ExtendedInvoiceInterface
    {
        /** @var ExtendedInvoiceInterface $extendedInvoice */
        $extendedInvoice = $this->extendedInvoiceFactory->create();
        $this->resourceModel->load($extendedInvoice, $id);

        if (!$extendedInvoice->getId()) {
            throw NoSuchEntityException::singleField(ExtendedInvoiceInterface::KEY_ID, $id);
        }

        return $extendedInvoice;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByInvoiceId(int $invoiceId): ExtendedInvoiceInterface
    {
        /** @var ExtendedInvoiceInterface $extendedInvoice */
        $extendedInvoice = $this->extendedInvoiceFactory->create();
        $this->resourceModel->load($extendedInvoice, $invoiceId, ExtendedInvoiceInterface::KEY_INVOICE_ID);

        if (!$extendedInvoice->getId()) {
            throw NoSuchEntityException::singleField(ExtendedInvoiceInterface::KEY_INVOICE_ID, $invoiceId);
        }

        return $extendedInvoice;
    }

    public function save(ExtendedInvoiceInterface $invoice): ExtendedInvoiceInterface
    {
        $this->resourceModel->save($invoice);

        return $invoice;
    }

    public function delete(ExtendedInvoiceInterface $invoice): void
    {
        $this->resourceModel->delete($invoice);
    }
}