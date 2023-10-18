<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Traits;

use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory as ExternalOrderAddressFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\ItemFactory as ExternalOrderItemFactory;
use JcElectronics\ExactOrders\Model\ExternalOrderFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection as InvoiceCollection;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\Order\PaymentFactory;
use Magento\Sales\Model\OrderFactory;

trait OrderRepositoryMockTrait
{
    private function createExternalOrderFactoryMock(
        bool $isCalled
    ): ExternalOrderFactory {
        $externalOrderFactory = $this->getMockBuilder(ExternalOrderFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $externalOrderFactory->expects(self::exactly((int) $isCalled))
            ->method('create')
            ->willReturn($this->createExternalOrderMock($isCalled));

        return $externalOrderFactory;
    }

    private function createExternalOrderMock(bool $isCalled): ExternalOrder
    {
        $externalOrder = $this->createMock(ExternalOrder::class);
        $externalOrder->expects(self::exactly((int) $isCalled))
            ->method('setData')
            ->willReturnSelf();

        $externalOrder->expects(self::any())
            ->method('getData')
            ->willReturn([]);

        return $externalOrder;
    }

    private function createOrderFactoryMock(?array $magentoOrderData): OrderFactory
    {
        $orderFactory = $this->getMockBuilder(OrderFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $orderFactory->expects(self::exactly($magentoOrderData === null ? 0 : 1))
            ->method('create')
            ->willReturn($this->createOrderMock($magentoOrderData));

        return $orderFactory;
    }

    private function createOrderPaymentFactory(): PaymentFactory
    {
        $paymentFactory = $this->getMockBuilder(PaymentFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $paymentFactory->expects(self::any())
            ->method('create')
            ->willReturn($this->createMock(OrderPaymentInterface::class));

        return $paymentFactory;
    }

    private function createOrderItemFactory(): ItemFactory
    {
        $itemFactory = $this->getMockBuilder(ItemFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $itemFactory->expects(self::any())
            ->method('create')
            ->willReturn($this->createMock(OrderItemInterface::class));

        return $itemFactory;
    }

    private function createOrderAddressFactory(): AddressFactory
    {
        $addressFactory = $this->getMockBuilder(AddressFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $addressFactory->expects(self::any())
            ->method('create')
            ->willReturn($this->createMock(OrderAddressInterface::class));

        return $addressFactory;
    }

    private function createExternalOrderAddressFactory(): ExternalOrderAddressFactory
    {
        $externalOrderAddressFactory = $this->getMockBuilder(ExternalOrderAddressFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $externalOrderAddressFactory->expects(self::any())
            ->method('create')
            ->willReturn($this->createMock(ExternalOrder\Address::class));

        return $externalOrderAddressFactory;
    }

    private function createExternalOrderItemFactory(): ExternalOrderItemFactory
    {
        $externalOrderItemFactory = $this->getMockBuilder(ExternalOrderItemFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $externalOrderItemFactory->expects(self::any())
            ->method('create')
            ->willReturn($this->createMock(ExternalOrder\Item::class));

        return $externalOrderItemFactory;
    }

    private function createMagentoOrderRepositoryMock(
        ?array $magentoOrderData,
        string $mode = 'getList'
    ): MagentoOrderRepositoryInterface {
        $orderRepository = $this->createMock(MagentoOrderRepositoryInterface::class);
        $orderRepository
            ->expects(self::exactly($mode === 'getList' ? 1 : 0))
            ->method('getList')
            ->willReturn($this->createOrderSearchResultMock($magentoOrderData));

        $orderRepository
            ->expects(self::exactly($mode === 'get' ? 1 : 0))
            ->method('get')
            ->willReturn($this->createOrderMock($magentoOrderData));

        return $orderRepository;
    }

    private function createOrderMock(?array $magentoOrderData = null): Order
    {
        $order = $this->createMock(Order::class);

        if ($magentoOrderData !== null) {
            $order->expects(self::once())
                ->method('getInvoiceCollection')
                ->willReturn($this->createInvoiceCollectionMock($magentoOrderData['invoices'] ?? []));

            $order->expects(self::once())
                ->method('getBillingAddress')
                ->willReturn($this->createMock(Address::class));
        
            $order->expects(self::once())
                ->method('getShippingAddress')
                ->willReturn($this->createMock(Address::class));
        
            $order->expects(self::once())
                ->method('getPayment')
                ->willReturn($this->createMock(OrderPaymentInterface::class));
        
            $order->expects(self::once())
                ->method('getAllItems')
                ->willReturn($this->createOrderItemMocks($magentoOrderData['items'] ?? []));
        }

        return $order;
    }

    private function createOrderItemMocks(array $items): array
    {
        return array_reduce(
            $items,
            function (array $carry, array $itemData) {
                $carry[] = $this->createMock(Order\Item::class);

                return $carry;
            },
            []
        );
    }

    private function createInvoiceCollectionMock(array $invoices): InvoiceCollection
    {
        $invoiceCollection = $this->createMock(InvoiceCollection::class);
        $invoiceCollection->expects(self::once())
            ->method('getItems')
            ->willReturn(
                $this->createInvoiceMocks($invoices)
            );

        return $invoiceCollection;
    }

    private function createInvoiceMocks(array $invoices): array
    {
        return array_reduce(
            $invoices,
            function (array $carry, array $invoice) {
                $invoiceMock = $this->createMock(InvoiceInterface::class);
                $carry[]     = $invoiceMock;

                return $carry;
            },
            []
        );
    }

    private function createSearchCriteriaBuilderMock(): SearchCriteriaBuilder
    {
        $searchCriteriaBuilder = $this->createMock(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->expects(self::once())
            ->method('addFilter')
            ->willReturnSelf();

        $searchCriteriaBuilder->expects(self::once())
            ->method('create')
            ->willReturn($this->createMock(SearchCriteria::class));

        return $searchCriteriaBuilder;
    }

    private function createOrderSearchResultMock(
        ?array $magentoOrderData
    ): OrderSearchResultInterface {
        $orderSearchResult = $this->createMock(OrderSearchResultInterface::class);
        $orderSearchResult->expects(self::once())
            ->method('getItems')
            ->willReturn(
                $magentoOrderData === null
                    ? []
                    : [$this->createOrderMock($magentoOrderData)]
            );

        return $orderSearchResult;
    }
}
