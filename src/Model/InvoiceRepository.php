<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoice\SearchResultsInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalInvoice\SearchResultsInterfaceFactory;
use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Api\InvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceSearchResultInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface as MagentoInvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        private readonly MagentoInvoiceRepositoryInterface $invoiceRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly array $modifiers = []
    ) {
    }

    public function getById(string $id): ExternalInvoiceInterface
    {
        return $this->processModifiers(
            $this->invoiceRepository->get($id)
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByIncrementId(string $id): ExternalInvoiceInterface
    {
        $collection = $this->invoiceRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter(InvoiceInterface::INCREMENT_ID, $id)
                    ->create()
            )
            ->getItems();

        if (count($collection) === 0) {
            throw NoSuchEntityException::singleField(InvoiceInterface::INCREMENT_ID, $id);
        }

        return $this->processModifiers(
            current($collection)
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByExternalId(string $id): ExternalInvoiceInterface
    {
        $collection = $this->invoiceRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter('ext_invoice_id', $id)
                    ->create()
            )
            ->getItems();

        if (count($collection) === 0) {
            throw NoSuchEntityException::singleField('ext_invoice_id', $id);
        }

        return $this->processModifiers(
            current($collection)
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByOrder(string $id): SearchResultsInterface
    {
        $collection = $this->orderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(OrderInterface::INCREMENT_ID, $id)
                ->create()
        );

        if (!$collection->getTotalCount()) {
            throw NoSuchEntityException::singleField(OrderInterface::INCREMENT_ID, $id);
        }

        /** @var OrderInterface $order */
        $order = current($collection->getItems());

        return $this->getList(
            $this->searchCriteriaBuilder
                ->addFilter(InvoiceInterface::ORDER_ID, $order->getEntityId())
                ->create()
        );
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->invoiceRepository->getList($searchCriteria);

        /** @var SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems(
            array_map(
                fn (InvoiceInterface $item) => $this->processModifiers($item),
                $collection->getItems()
            )
        );
        $searchResults->setTotalCount($collection->getTotalCount());

        return $searchResults;
    }

    public function save(ExternalInvoiceInterface $invoice): int
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->processModifiers($invoice);

        return (int) $invoice->getEntityId();
    }

    private function processModifiers(mixed $invoice): mixed
    {
        $result = null;

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiers as $modifier) {
            if (!$modifier->supports($invoice)) {
                continue;
            }

            $result = $modifier->process($invoice, $result);
        }

        return $result;
    }
}
