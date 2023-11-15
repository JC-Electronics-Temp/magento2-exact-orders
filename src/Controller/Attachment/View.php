<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Controller\Attachment;

use JcElectronics\ExactOrders\Model\AttachmentRepository;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Laminas\Mime\Mime;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Company\Api\Data\CompanyOrderInterface;
use Magento\Company\Api\Data\CompanyOrderInterfaceFactory;
use Magento\Company\Model\Company\Structure;
use Magento\Company\Model\ResourceModel\Order;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order as OrderModel;

class View implements HttpGetActionInterface
{
    public function __construct(
        private readonly AttachmentRepository $attachmentRepository,
        private readonly RequestInterface $request,
        private readonly ResultFactory $resultFactory,
        private readonly UserContextInterface $userContext,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CompanyOrderInterfaceFactory $companyOrderFactory,
        private readonly Order $companyOrderResource,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly Structure $structure,
        private readonly FileFactory $fileFactory,
        private readonly RawFactory $resultRawFactory
    ) {
    }

    public function execute(): ResultInterface
    {
        $id = (int) $this->request->getParam('id');

        /** @var Forward $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        try {
            $attachment = $this->attachmentRepository->get($id);
        } catch (NoSuchEntityException) {
            return $result->forward('noroute');
        }

        if (!$this->canView($attachment)) {
            return $result->forward('noroute');
        }

        $this->fileFactory->create(
            $attachment->getFileName(),
            [
                'type' => 'filename',
                'value' => $this->getAttachmentFilePath($attachment)
            ],
            DirectoryList::VAR_DIR,
            Mime::TYPE_OCTETSTREAM
        );

        return $this->resultRawFactory->create();
    }

    private function canView(
        AttachmentInterface $attachment
    ): bool {
        $orderId = $attachment->getEntityTypeId() === AttachmentInterface::ENTITY_TYPE_ORDER
            ? $attachment->getParentId()
            : $attachment->getParentEntity()->getOrderId();

        /** @var OrderModel $order */
        $order                 = $this->orderRepository->get($orderId);
        $orderCompanyAttribute = $this->getCompanyOrderEntityBySalesOrder($order);
        $isCompanyOrder        = (bool) $orderCompanyAttribute->getOrderId();
        $customerId            = $this->userContext->getUserId();

        if (!$isCompanyOrder) {
            return $order->getCustomerId() === $customerId;
        }

        $customer                  = $this->customerRepository->getById($customerId);
        $customerCompanyAttributes = $customer->getExtensionAttributes()->getCompanyAttributes();

        if ($customerCompanyAttributes->getCompanyId() !== $orderCompanyAttribute->getCompanyId()) {
            return false;
        }

        $allowedChildIds = array_merge(
            [$customerId],
            $this->structure->getAllowedChildrenIds($customerId)
        );

        return $order->getId() && $order->getCustomerId() && in_array($order->getCustomerId(), $allowedChildIds);
    }

    private function getCompanyOrderEntityBySalesOrder(
        OrderModel $order
    ): CompanyOrderInterface{
        /** @var CompanyOrderInterface $orderCompanyAttributes */
        $orderCompanyAttributes = $this->companyOrderFactory->create();
        $this->companyOrderResource->load(
            $orderCompanyAttributes,
            $order->getEntityId(),
            CompanyOrderInterface::ORDER_ID
        );

        return $orderCompanyAttributes;
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