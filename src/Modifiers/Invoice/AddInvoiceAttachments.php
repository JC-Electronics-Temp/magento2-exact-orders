<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Invoice;

use JcElectronics\ExactOrders\Api\AttachmentRepositoryInterface;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Model\AttachmentFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Magento\Sales\Api\Data\InvoiceInterface;

class AddInvoiceAttachments extends AbstractModifier
{
    public function __construct(
        private readonly InvoiceExtensionFactory $extensionFactory,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly AttachmentRepositoryInterface $attachmentRepository
    ) {
    }

    /**
     * @param ExternalInvoiceInterface $model
     * @param InvoiceInterface    $result
     *
     * @return mixed
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $extensionAttributes = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttributes->setAttachments(
            array_map(
                function ($attachment) use ($result) {
                    try {
                        $orderAttachment = $this->attachmentRepository->getByEntity(
                            (int) $result->getEntityId(),
                            AttachmentInterface::ENTITY_TYPE_INVOICE
                        );
                    } catch (NoSuchEntityException) {
                        /** @var AttachmentInterface $orderAttachment */
                        $orderAttachment = $this->attachmentFactory->create();
                        $orderAttachment->setEntityTypeId(AttachmentInterface::ENTITY_TYPE_INVOICE);

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
