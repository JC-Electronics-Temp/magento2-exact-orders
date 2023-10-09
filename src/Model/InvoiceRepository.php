<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Api\InvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalInvoiceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface as MagentoInvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\Order\PaymentFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly MagentoInvoiceRepositoryInterface $invoiceRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ExternalInvoiceFactory $externalInvoiceFactory
    ) {
    }

    /**
     * @param string $id
     *
     * @return ExternalInvoiceInterface
     * @throws LocalizedException
     */
    public function getById(string $id): ExternalInvoiceInterface
    {
        try {
            $invoice = $this->invoiceRepository->get($id);
        } catch (NoSuchEntityException) {
            throw new LocalizedException(
                __('No invoice found with the specified ID.')
            );
        }

        return $this->normalize($invoice);
    }

    /**
     * @param string $incrementId
     *
     * @return ExternalInvoiceInterface
     * @throws LocalizedException
     */
    public function getByIncrementId(string $incrementId): ExternalInvoiceInterface
    {
        $collection = $this->invoiceRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(InvoiceInterface::INCREMENT_ID, $incrementId)
                ->create()
        );

        if (!$collection->getItems()) {
            throw new LocalizedException(
                __('No order found with the specified increment ID.')
            );
        }

        return $this->normalize(
            current($collection->getItems())
        );
    }

    /**
     * @param string $id
     *
     * @return ExternalInvoiceInterface
     * @throws LocalizedException
     */
    public function getByExternalId(string $id): ExternalInvoiceInterface
    {
        $collection = $this->invoiceRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(
                    '',
                    $id
                )
                ->create()
        );

        if (!$collection->getItems()) {
            throw new LocalizedException(
                __('No order found with the specified external ID.')
            );
        }

        return $this->normalize(
            current($collection->getItems())
        );
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        $collection = $this->orderRepository->getList($searchCriteria);

        return array_reduce(
            $collection->getItems(),
            fn (InvoiceInterface $invoice) => $this->normalize($invoice),
            []
        );
    }

    /**
     * @param ExternalInvoiceInterface $externalInvoice
     *
     * @return ExternalInvoiceInterface
     * @throws LocalizedException
     */
    public function save(
        ExternalInvoiceInterface $externalInvoice
    ): ExternalInvoiceInterface {
    }

    /**
     * @param string $id
     *
     * @return Collection
     * @throws LocalizedException
     */
    public function getByOrder(string $id): Collection
    {
        try {
            /** @var Order $order */
            $order = $this->orderRepository->get($id);
        } catch (NoSuchEntityException) {
            throw new LocalizedException(__('No order found with the given ID.'));
        }

        return $order->getInvoiceCollection();
    }

    private function normalize(
        InvoiceInterface $invoice
    ): ExternalInvoiceInterface {
        /** @var ExternalInvoiceInterface $externalInvoice */
        $externalInvoice = $this->externalInvoiceFactory->create();
        $externalInvoice->normalize($invoice);

        return $externalInvoice;
    }
}
