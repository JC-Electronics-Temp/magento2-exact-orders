<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Controller\Adminhtml\Attachment;

use Exception;
use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class View extends Action
{
    public function __construct(
        Context $context,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly RequestInterface $request,
        private readonly FileFactory $fileFactory,
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface|ResponseInterface|Forward
    {
        $id = (int) $this->request->getParam('id');

        try {
            $attachment = $this->attachmentRepository->get($id);

            return $this->fileFactory->create(
                $attachment->getFileName(),
                [
                    'type' => 'filename',
                    'value' => $this->getAttachmentFilePath($attachment)
                ],
                DirectoryList::VAR_DIR,
                'application/pdf'
            );
        } catch (NoSuchEntityException | Exception) {
            /** @var Forward $result */
            $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

            return $result->forward('noroute');
        }
    }

    private function getAttachmentFilePath(AttachmentInterface $attachment): string
    {
        return sprintf(
            'substitute_order/%s/%s',
            $attachment->getEntityTypeId(),
            $attachment->getFileName()
        );
    }
}
