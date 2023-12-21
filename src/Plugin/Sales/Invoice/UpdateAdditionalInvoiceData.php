<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\Sales\Invoice;

use JcElectronics\ExactOrders\Api\Data\ExtendedInvoiceInterface;
use JcElectronics\ExactOrders\Api\ExtendedInvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExtendedInvoiceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceExtension;
use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Magento\Sales\Api\Data\InvoiceExtensionInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceSearchResultInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;

class UpdateAdditionalInvoiceData
{
    public function __construct(
        private readonly ExtendedInvoiceRepositoryInterface $repository,
        private readonly ExtendedInvoiceFactory $extendedInvoiceFactory,
        private readonly InvoiceExtensionFactory $extensionFactory
    ) {
    }

    public function afterGet(
        InvoiceRepositoryInterface $subject,
        InvoiceInterface $result
    ): InvoiceInterface {
        $extendedInvoice     = $this->getExtendedDataByInvoice($result);
        $extensionAttributes = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttributes->setExtInvoiceId($extendedInvoice->getExtInvoiceId());

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
        $extInvoiceId = $invoice->getExtensionAttributes()->getExtInvoiceId();

        try {
            $extendedInvoice = $this->repository->getByInvoiceId((int) $invoice->getEntityId());
        } catch (NoSuchEntityException) {
            $extendedInvoice = $this->extendedInvoiceFactory->create();
            $extendedInvoice->setInvoiceId((int) $invoice->getEntityId());
        }

        $extendedInvoice->setExtInvoiceId($extInvoiceId);

        $this->repository->save($extendedInvoice);
    }

    private function getExtendedDataByInvoice(InvoiceInterface $invoice): ExtendedInvoiceInterface
    {
        return $this->repository->getByInvoiceId((int) $invoice->getEntityId());
    }
}
