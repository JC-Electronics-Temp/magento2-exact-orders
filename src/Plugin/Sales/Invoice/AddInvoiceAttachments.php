<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\Sales\Invoice;

use JcElectronics\ExactOrders\Plugin\Sales\AbstractAddAttachment;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceSearchResultInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;

class AddInvoiceAttachments extends AbstractAddAttachment
{
    public function afterGet(
        InvoiceRepositoryInterface $subject,
        InvoiceInterface $result
    ): InvoiceInterface {
        $extensionAttributes = $result->getExtensionAttributes();
        $extensionAttributes->setAttachments(
            $this->getAttachmentsByEntity($result, 'invoice')
        );

        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }

    public function afterGetList(
        InvoiceRepositoryInterface $subject,
        InvoiceSearchResultInterface $result
    ): InvoiceSearchResultInterface {
        foreach ($result->getItems() as $item) {
            $this->afterGet($subject, $item);
        }

        return $result;
    }

    public function afterSave(
        InvoiceRepositoryInterface $subject,
        $result,
        InvoiceInterface $invoice
    ): void {
        $attachments = $invoice->getExtensionAttributes()->getAttachments();

        foreach ($attachments as $attachment) {
            if ($attachment->getId()) {
                continue;
            }

            $this->attachmentRepository->save($attachment);
        }
    }
}
