<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\AttachmentFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderFactory;

class AddOrderAttachments extends AbstractModifier
{
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly OrderExtensionFactory $extensionFactory,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly AttachmentRepositoryInterface $attachmentRepository
    ) {
        parent::__construct(
            $orderRepository,
            $searchCriteriaBuilder
        );
    }

    public function supports(mixed $entity): bool
    {
        return $entity instanceof ExternalOrderInterface;
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface         $result
     *
     * @return OrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $extensionAttributes = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttributes->setAttachments(
            array_map(
                function ($attachment) use ($result) {
                    try {
                        $orderAttachment = $this->attachmentRepository->getByAttachmentName(
                            (int) $result->getEntityId(),
                            AttachmentInterface::ENTITY_TYPE_ORDER,
                            $attachment->getName()
                        );
                    } catch (NoSuchEntityException) {
                        /** @var AttachmentInterface $orderAttachment */
                        $orderAttachment = $this->attachmentFactory->create();
                        $orderAttachment->setEntityTypeId(AttachmentInterface::ENTITY_TYPE_ORDER);

                        if ($result->getEntityId()) {
                            $orderAttachment->setParentId((int) $result->getEntityId());
                        }
                    }

                    $orderAttachment->setFileName($attachment->getName())
                        ->setFileContent(
                            base64_decode($attachment->getFileData(), true)
                        );

                    return $orderAttachment;
                },
                $model->getAttachments()
            )
        );

        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
