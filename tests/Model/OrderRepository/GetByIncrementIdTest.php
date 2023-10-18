<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Model\OrderRepository;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\OrderRepository;
use JcElectronics\ExactOrders\Test\Traits\EntriesDataProviderTrait;
use JcElectronics\ExactOrders\Test\Traits\OrderRepositoryMockTrait;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \JcElectronics\ExactOrders\Model\OrderRepository
 */
class GetByIncrementIdTest extends TestCase
{
    use EntriesDataProviderTrait;
    use OrderRepositoryMockTrait;

    private const TEST_ENTITY_TYPE = 'orders';

    /**
     * @covers ::__construct
     * @covers ::getByIncrementId
     * @covers ::normalize
     * @covers ::normalizeAddress
     *
     * @dataProvider setEntriesDataProvider
     */
    public function testGetByExternalId(
        string $id,
        ?array $magentoOrderData
    ): void {
        $subject = new OrderRepository(
            $this->createMagentoOrderRepositoryMock($magentoOrderData),
            $this->createSearchCriteriaBuilderMock(),
            $this->createExternalOrderFactoryMock(
                $magentoOrderData !== null
            ),
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
            $this->expectException(LocalizedException::class);
        }

        $result = $subject->getByIncrementId($id);

        $this->assertInstanceOf(ExternalOrderInterface::class, $result);
        $this->assertIsArray($result->getData());
    }
}
