<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Setup\Patch\Data;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use PHPUnit\Framework\TestCase;
use JcElectronics\ExactOrders\Setup\Patch\Data\AddImportedProcessingOrderStatus;

/**
 * @coversDefaultClass \JcElectronics\ExactOrders\Setup\Patch\Data\AddImportedProcessingOrderStatus
 */
class AddImportedProcessingOrderStatusTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::apply
     */
    public function testApply(): void
    {
        $subject = new AddImportedProcessingOrderStatus(
            $this->createStatusFactoryMock(true),
            $this->createMock(StatusResource::class)
        );

        $subject->apply();
    }

    /**
     * @covers ::__construct
     * @covers ::getAliases
     */
    public function testGetAliases(): void
    {
        $subject = new AddImportedProcessingOrderStatus(
            $this->createStatusFactoryMock(false),
            $this->createMock(StatusResource::class)
        );

        $result = $subject->getAliases();

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    /**
     * @covers ::__construct
     * @covers ::getDependencies
     */
    public function testGetDependencies(): void
    {
        $subject = new AddImportedProcessingOrderStatus(
            $this->createStatusFactoryMock(false),
            $this->createMock(StatusResource::class)
        );

        $result = $subject->getDependencies();

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    private function createStatusFactoryMock(bool $isCalled): StatusFactory
    {
        $statusFactory = $this->getMockBuilder(StatusFactory::class)
            ->allowMockingUnknownTypes()
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        if (!$isCalled) {
            return $statusFactory;
        }

        $status = $this->createMock(Status::class);

        $status->expects(self::exactly(2))
            ->method('setData')
            ->willReturnSelf();

        $status->expects(self::once())
            ->method('assignState')
            ->willReturnSelf();

        $statusFactory->expects(self::atMost(1))
            ->method('create')
            ->willReturn($status);

        return $statusFactory;
    }
}
