<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\Controller\Sales\Order\PrintInvoice;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Sales\Controller\Order\PrintInvoice;
use Magento\Sales\Model\Order\Invoice;

class DownloadAttachment
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly OrderViewAuthorizationInterface $orderViewAuthorization,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly Filesystem $filesystem,
        private readonly FileFactory $fileFactory,
        private readonly ResultFactory $resultFactory
    ) {
    }

    public function aroundExecute(
        PrintInvoice $subject,
        callable $proceed
    ): ResultInterface|ResponseInterface {
        $invoiceId  = (int) $this->request->getParam('invoice_id');
        $invoice    = $this->invoiceRepository->get($invoiceId);
        $attachment = $this->getAttachmentByInvoice($invoice);

        if (!$attachment instanceof AttachmentInterface) {
            /** @var Redirect $redirect */
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $redirect->setPath(
                sprintf('sales/order/invoice/order_id/%d', $invoice->getOrderId())
            );

            return $redirect;
        }

        $filePath = sprintf(
            'substitute_order/%s/%s',
            $attachment->getEntityTypeId(),
            $attachment->getFileName()
        );

        $destination = $this->filesystem
            ->getDirectoryRead(DirectoryList::VAR_DIR);

        return $this->fileFactory->create(
            $attachment->getFileName(),
            base64_decode($destination->readFile($filePath), true) ?: $destination->readFile($filePath),
            DirectoryList::VAR_DIR
        );
    }

    private function getAttachmentByInvoice(Invoice $invoice): ?AttachmentInterface
    {
        if (!$this->orderViewAuthorization->canView($invoice->getOrder())) {
            return null;
        }

        try {
            return $this->attachmentRepository->getByEntity(
                (int) $invoice->getEntityId(),
                AttachmentInterface::ENTITY_TYPE_INVOICE
            );
        } catch (NoSuchEntityException) {
            return null;
        }
    }
}
