<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Model\OrderRepository;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\ExternalOrder;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory as ExternalOrderAddressFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\ItemFactory as ExternalOrderItemFactory;
use JcElectronics\ExactOrders\Model\ExternalOrderFactory;
use JcElectronics\ExactOrders\Model\OrderRepository;
use JcElectronics\ExactOrders\Test\Traits\EntriesDataProviderTrait;
use JcElectronics\ExactOrders\Test\Traits\OrderRepositoryMockTrait;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\Order\PaymentFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection as InvoiceCollection;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \JcElectronics\ExactOrders\Model\OrderRepository
 */
class GetByIdTest extends TestCase
{
    use EntriesDataProviderTrait;
    use OrderRepositoryMockTrait;

    private const TEST_ENTITY_TYPE = 'orders';

    /**
     * @covers ::__construct
     * @covers ::getById
     * @covers ::normalize
     * @covers ::normalizeAddress
     *
     * @dataProvider setEntriesDataProvider
     */
    public function testGetById(
        string $id,
        ?array $magentoOrderData
    ): void {
        $subject = new OrderRepository(
            $this->createMagentoOrderRepositoryMock(null, 'get'),
            $this->createMock(SearchCriteriaBuilder::class),
            $this->createExternalOrderFactoryMock($magentoOrderData !== null),
            $this->createOrderFactoryMock(null),
            $this->createMock(CustomerRepositoryInterface::class),
            $this->createOrderPaymentFactory(),
            $this->createOrderItemFactory(),
            $this->createMock(AddressRepositoryInterface::class),
            $this->createOrderAddressFactory(),
            $this->createMock(ProductRepositoryInterface::class),
            $this->createExternalOrderAddressFactory(),
            $this->createExternalOrderItemFactory(),
        );

        if ($magentoOrderData === null) {
            $this->expectException(NoSuchEntityException::class);
        }

        $result = $subject->getById($id);

        $this->assertInstanceOf(ExternalOrderInterface::class, $result);
        $this->assertIsArray($result->getData());
    }
}
