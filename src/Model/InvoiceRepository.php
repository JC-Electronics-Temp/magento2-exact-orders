<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Api\InvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalInvoice\ItemFactory as ExternalInvoiceItemFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory as ExternalOrderAddressFactory;
use JcElectronics\ExactOrders\Traits\FormatInvoiceDataTrait;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface as MagentoInvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Model\Order\Invoice\AddressFactory;
use Magento\Sales\Model\Order\Invoice\ItemFactory;
use Magento\Sales\Model\Order\InvoiceFactory;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    use FormatInvoiceDataTrait;

    public function __construct(
        private readonly MagentoInvoiceRepositoryInterface $invoiceRepository,
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly ServiceInputProcessor $serviceInputProcessor
    ) {
    }

    public function getById(string $id): ExternalInvoiceInterface
    {
    }

    public function getByIncrementId(string $incrementId): ExternalInvoiceInterface
    {
    }

    public function getByExternalId(string $id): ExternalInvoiceInterface
    {
    }

    public function getByOrder(string $id): array
    {
    }

    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
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

        return (int) $result->getEntityId();
    }

    private function getOrderFromInvoice(ExternalInvoiceInterface $invoice): OrderInterface
    {
        return $this->orderRepository->get(
            current($invoice->getOrderIds())
        );
    }
}
