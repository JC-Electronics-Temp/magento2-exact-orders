<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Api\InvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalInvoice\ItemFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use JcElectronics\ExactOrders\Traits\FormatExternalInvoiceDataTrait;
use JcElectronics\ExactOrders\Traits\FormatInvoiceDataTrait;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface as MagentoInvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    use FormatInvoiceDataTrait;
    use FormatExternalInvoiceDataTrait;

    public function __construct(
        private readonly MagentoInvoiceRepositoryInterface $invoiceRepository,
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly ServiceInputProcessor $serviceInputProcessor,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ExternalInvoiceFactory $externalInvoiceFactory,
        private readonly AddressFactory $externalOrderAddressFactory,
        private readonly ItemFactory $externalInvoiceItemFactory,
    ) {
    }

    public function getById(string $id): ExternalInvoiceInterface
    {
        return $this->formatExternalInvoiceData(
            $this->invoiceRepository->get($id)
        );
    }

    public function getByIncrementId(string $incrementId): ExternalInvoiceInterface
    {
        return $this->formatExternalInvoiceData(
            current(
                $this->invoiceRepository->getList(
                    $this->searchCriteriaBuilder
                        ->addFilter(InvoiceInterface::INCREMENT_ID, $incrementId)
                        ->create()
                )->getItems()
            )
        );
    }

    public function getByExternalId(string $id): ExternalInvoiceInterface
    {
        return $this->formatExternalInvoiceData(
            current(
                $this->invoiceRepository->getList(
                    $this->searchCriteriaBuilder
                        ->addFilter('ext_invoice_id', $id)
                        ->create()
                )->getItems()
            )
        );
    }

    public function getByOrder(string $id): array
    {
        return $this->getList(
            $this->searchCriteriaBuilder
                ->addFilter(InvoiceInterface::ORDER_ID, $id)
                ->create()
        );
    }

    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        return array_map(
            fn (InvoiceInterface $item) => $this->formatExternalInvoiceData($item),
            $this->invoiceRepository->getList($searchCriteria)->getItems()
        );
    }

    public function save(
        ExternalInvoiceInterface $invoice
    ): int {
        $result = $this->invoiceRepository->save(
            $this->formatInvoiceData(
                $invoice->getData(),
                $this->getOrderFromInvoice($invoice)
            )
        );
        
        // Store attachments
         if ($invoice->getAttachments()) {
             
         }

        return (int) $result->getEntityId();
    }

    private function getOrderFromInvoice(ExternalInvoiceInterface $invoice): OrderInterface
    {
        return $this->orderRepository->get(
            current($invoice->getOrderIds())
        );
    }
}
