<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Controller\Attachment;

use JcElectronics\ExactOrders\Model\AttachmentRepository;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Invoice implements HttpGetActionInterface
{
    public function __construct(
        private readonly PageFactory $pageFactory,
        private readonly AttachmentRepository $attachmentRepository,
        private readonly RequestInterface $request,
        private readonly RedirectFactory $redirectFactory,
        private readonly Session $customerSession
    ) {
    }

    public function execute(): ResultInterface
    {
        $id = (int) $this->request->getParam('id');

        try {
            $attachment = $this->attachmentRepository->get($id);
        } catch (NoSuchEntityException) {
            return $this->redirectFactory->create()
                ->setPath('noroute');
        }

        if (!$this->customerIsAllowed($attachment)) {
            return $this->redirectFactory->create()
                ->setPath('noroute');
        }

        return $this->pageFactory->create();
    }

    private function customerIsAllowed(
        AttachmentInterface $attachment
    ): bool {
        $customer = $this->customerSession->getCustomer();

        return $attachment->getParentEntity()->getCustomerId() === $customer->getId();
    }
}
