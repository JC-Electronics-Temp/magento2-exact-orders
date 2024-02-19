<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Plugin\InventorySales;

use Magento\InventorySales\Model\AppendReservations;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\SalesEventExtension;
use Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use PHPUnit\Framework\TestCase;
use JcElectronics\ExactOrders\Plugin\InventorySales\DisableStockCheckForExactOrders;

/**
 * @coversDefaultClass \JcElectronics\ExactOrders\Plugin\InventorySales\DisableStockCheckForExactOrders
 */
class DisableStockCheckForExactOrdersTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::aroundReserve
     *
     * @dataProvider getReserveDataProvider
     */
    public function testAroundReserve(
        bool $isExternalOrder
    ): void {
        $subject = new DisableStockCheckForExactOrders(
            $this->createOrderExtensionFactoryMock($isExternalOrder),
            $this->createWebsiteRepositoryMock($isExternalOrder),
            $this->createSalesEventExtensionFactory($isExternalOrder),
            $this->createSalesChannelFactory($isExternalOrder)
        );

        $subject->aroundReserve(
            $this->createMock(AppendReservations::class),
            fn () => [],
            1,
            [],
            $this->createMock(OrderInterface::class),
            []
        );
    }

    private function createOrderExtensionFactoryMock(
        bool $isExternalOrder
    ): OrderExtensionFactory {
        $orderExtension = $this->getMockBuilder(OrderExtensionInterface::class)
            ->allowMockingUnknownTypes()
            ->disableOriginalConstructor()
            ->setMethods(['getIsExternalOrder'])
            ->getMock();

        $orderExtension->expects(self::once())
            ->method('getIsExternalOrder')
            ->willReturn($isExternalOrder);

        $orderExtensionFactory = $this->getMockBuilder(OrderExtensionFactory::class)
            ->allowMockingUnknownTypes()
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $orderExtensionFactory->expects(self::once())
            ->method('create')
            ->willReturn($orderExtension);

        return $orderExtensionFactory;
    }

    private function createSalesEventExtensionFactory(
        bool $isExternalOrder
    ): SalesEventExtensionFactory {
        $salesEventExtension = $this->getMockBuilder(SalesEventExtension::class)
            ->allowMockingUnknownTypes()
            ->disableOriginalConstructor()
            ->getMock();

        $salesEventExtensionFactory = $this->getMockBuilder(SalesEventExtensionFactory::class)
            ->allowMockingUnknownTypes()
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $salesEventExtensionFactory->expects(self::exactly($isExternalOrder ? 1 : 0))
            ->method('create')
            ->willReturn($salesEventExtension);

        return $salesEventExtensionFactory;
    }

    private function createSalesChannelFactory(
        bool $isExternalOrder
    ): SalesChannelInterfaceFactory {
        $salesChannelFactory = $this->getMockBuilder(SalesChannelInterfaceFactory::class)
            ->allowMockingUnknownTypes()
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $salesChannelFactory->expects(self::exactly($isExternalOrder ? 1 : 0))
            ->method('create')
            ->willReturn($this->createMock(SalesChannelInterface::class));

        return $salesChannelFactory;
    }

    private function createWebsiteRepositoryMock(
        bool $isExternalOrder
    ): WebsiteRepositoryInterface {
        $websiteRepository = $this->createMock(WebsiteRepositoryInterface::class);
        $websiteRepository->expects(self::exactly($isExternalOrder ? 1 : 0))
            ->method('getById')
            ->willReturn($this->createMock(WebsiteInterface::class));

        return $websiteRepository;
    }

    public function getReserveDataProvider(): array
    {
        return [
            'externalOrder' => [true],
            'internalOrder' => [false]
        ];
    }
}
