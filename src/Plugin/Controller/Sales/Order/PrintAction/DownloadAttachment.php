<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\Controller\Sales\Order\PrintAction;

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
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface;
use Magento\Sales\Controller\Order\PrintAction;

class DownloadAttachment
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly OrderViewAuthorizationInterface $orderViewAuthorization,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly Filesystem $filesystem,
        private readonly FileFactory $fileFactory,
        private readonly ResultFactory $resultFactory
    ) {
    }

    public function aroundExecute(
        PrintAction $subject,
        callable $proceed
    ): ResultInterface|ResponseInterface {
        $orderId    = (int) $this->request->getParam('order_id');
        $attachment = $this->getAttachmentByOrderId($orderId);

        if (!$attachment instanceof AttachmentInterface) {
            /** @var Redirect $redirect */
            $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $redirect->setPath(
                sprintf('sales/order/view/order_id/%d', $orderId)
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

    private function getAttachmentByOrderId(int $orderId): ?AttachmentInterface
    {
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException) {
            return null;
        }

        if (!$this->orderViewAuthorization->canView($order)) {
            return null;
        }

        try {
            return $this->attachmentRepository
                ->getByEntity($orderId, AttachmentInterface::ENTITY_TYPE_ORDER);
        } catch (NoSuchEntityException) {
            return null;
        }
    }
}
