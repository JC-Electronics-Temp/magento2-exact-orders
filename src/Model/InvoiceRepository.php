<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\InvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalInvoice\ItemFactory as ExternalInvoiceItemFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory as ExternalOrderAddressFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface as MagentoInvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice\AddressFactory;
use Magento\Sales\Model\Order\Invoice\ItemFactory;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Sales\Model\Service\InvoiceService;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly MagentoInvoiceRepositoryInterface $invoiceRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly ExternalInvoiceFactory $externalInvoiceFactory,
        private readonly ExternalInvoiceItemFactory $externalInvoiceItemFactory,
        private readonly ExternalOrderAddressFactory $externalOrderAddressFactory,
        private readonly InvoiceService $invoiceService,
        private readonly \Magento\Sales\Model\Convert\Order $convertOrder
    ) {
    }

    /**
     * @param string $id
     *
     * @return ExternalInvoiceInterface
     */
    public function getById(string $id): ExternalInvoiceInterface
    {
        return $this->normalize(
            $this->invoiceRepository->get($id)
        );
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
                ->addFilter('ext_invoice_id', $id)
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

        return $collection->getItems();
    }

    /**
     * @param ExternalInvoiceInterface $invoice
     *
     * @return int
     * @throws LocalizedException
     */
    public function save(
        ExternalInvoiceInterface $invoice
    ): int {
        /** @var Order $order */
        $order = $this->orderRepository->get(
            current($invoice->getOrderIds())
        );

        $magentoInvoice =   $this->convertOrder->toInvoice($order);

        foreach ($order->getAllItems() as $item) {
            $magentoInvoice->addItem(
                $this->convertOrder->itemToInvoiceItem($item)
            );
        }

        $this->invoiceRepository->save($magentoInvoice);
        $this->orderRepository->save($magentoInvoice->getOrder());

        $this->invoiceService->notify(
            $magentoInvoice->getEntityId()
        );

        return (int) $magentoInvoice->getEntityId();
    }

    /**
     * @param string $id
     *
     * @return array
     */
    public function getByOrder(string $id): array
    {
        /** @var Order $order */
        $order = $this->orderRepository->get($id);

        return array_map(
            fn (InvoiceInterface $invoice) => $this->normalize($invoice),
            $order->getInvoiceCollection()->getItems()
        );
    }

    private function normalize(Order\Invoice $invoice): ExternalInvoiceInterface
    {
        $externalInvoice = $this->externalInvoiceFactory->create();
        $externalInvoice->setData(
            [
                "invoice_id" => $invoice->getId(),
                "order_ids" => [$invoice->getOrderId()],
                "ext_invoice_id" => $invoice->getData('ext_invoice_id'),
                'po_number' => $invoice->getData('po_number'),
                "magento_customer_id" => $invoice->getOrder()->getCustomerId(),
                "base_tax_amount" => $invoice->getBaseTaxAmount() ?? '0.0000',
                "base_discount_amount" => $invoice->getBaseDiscountAmount() ?? '0.0000',
                "base_shipping_amount" => $invoice->getBaseShippingAmount() ?? '0.0000',
                "base_subtotal" => (string) $invoice->getBaseSubtotal(),
                "base_grandtotal" => (string) $invoice->getBaseGrandTotal(),
                "tax_amount" => $invoice->getTaxAmount() ?? '0.0000',
                "discount_amount" => $invoice->getDiscountAmount() ?? '0.0000',
                "shipping_amount" => $invoice->getShippingAmount() ?? '0.0000',
                "subtotal" => (string) $invoice->getSubtotal(),
                "grandtotal" => (string) $invoice->getGrandTotal(),
                "invoice_date" => $invoice->getCreatedAt(),
                "state" => $invoice->getState(),
                "magento_increment_id" => $invoice->getIncrementId(),
                "additional_data" => [],
                'items' => array_map(
                    function (InvoiceItemInterface $item) use ($invoice) {
                        $externalItem = $this->externalInvoiceItemFactory->create();
                        $externalItem->setData(
                            [
                                'invoiceitem_id' => $item->getEntityId(),
                                'invoice_id' => $invoice->getId(),
                                'order_id' => $invoice->getOrderId(),
                                'name' => $item->getName(),
                                'sku' => $item->getSku(),
                                'base_price' => (string)$item->getBasePrice(),
                                'price' => (string)$item->getPrice(),
                                'base_row_total' => (string)$item->getBaseRowTotal(),
                                'row_total' => (string)$item->getRowTotal(),
                                'base_tax_amount' => (string)$item->getBaseTaxAmount(),
                                'tax_amount' => (string)$item->getTaxAmount(),
                                'qty' => $item->getQty(),
                                'base_discount_amount' => (string)$item->getBaseDiscountAmount(),
                                'discount_amount' => (string)$item->getDiscountAmount(),
                                'additionalData' => []
                            ]
                        );

                        return $externalItem;
                    },
                    $invoice->getAllItems()
                ),
                'shipping_address' => $this->normalizeAddress($invoice->getShippingAddress()),
                'billing_address' => $this->normalizeAddress($invoice->getBillingAddress()),
                "attachments" => []
            ]
        );

        return $externalInvoice;
    }

    private function normalizeAddress(
        OrderAddressInterface $address
    ): AddressInterface {
        $externalAddress = $this->externalOrderAddressFactory->create();
        $externalAddress->setData(
            [
                'orderaddress_id' => $address->getEntityId(),
                'firstname' => $address->getFirstname(),
                'lastname' => $address->getLastname(),
                'company' => $address->getCompany(),
                'street' => $address->getStreet(),
                'postcode' => $address->getPostcode(),
                'city' => $address->getCity(),
                'country' => $address->getCountryId(),
                'additional_data' => []
            ]
        );

        return $externalAddress;
    }
}
