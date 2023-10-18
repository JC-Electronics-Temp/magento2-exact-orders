<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Model\ExternalOrder;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use PHPUnit\Framework\TestCase;
use JcElectronics\ExactOrders\Model\ExternalOrder\Address;

/**
 * @coversDefaultClass \JcElectronics\ExactOrders\Model\ExternalOrder\Address
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class AddressTest extends TestCase
{
    /**
     * @covers ::getOrderaddressId
     * @covers ::setOrderaddressId
     *
     * @dataProvider setExternalOrderAddressData
     */
    public function testOrderaddressId(
        array $itemData
    ): void {
        $subject = new Address();

        $this->assertInstanceOf(
            Address::class,
            $subject->setOrderaddressId($itemData[AddressInterface::KEY_ORDER_ADDRESS_ID])
        );

        $this->assertIsString($subject->getOrderaddressId());
        $this->assertEquals($itemData[AddressInterface::KEY_ORDER_ADDRESS_ID], $subject->getOrderaddressId());
    }

    /**
     * @covers ::getFirstname
     * @covers ::setFirstname
     *
     * @dataProvider setExternalOrderAddressData
     */
    public function testFirstname(
        array $itemData
    ): void {
        $subject = new Address();

        $this->assertInstanceOf(
            Address::class,
            $subject->setFirstname($itemData[AddressInterface::KEY_FIRSTNAME])
        );

        $this->assertIsString($subject->getFirstname());
        $this->assertEquals($itemData[AddressInterface::KEY_FIRSTNAME], $subject->getFirstname());
    }

    /**
     * @covers ::getLastname
     * @covers ::setLastname
     *
     * @dataProvider setExternalOrderAddressData
     */
    public function testLastname(
        array $itemData
    ): void {
        $subject = new Address();

        $this->assertInstanceOf(
            Address::class,
            $subject->setLastname($itemData[AddressInterface::KEY_LASTNAME])
        );

        $this->assertIsString($subject->getLastname());
        $this->assertEquals($itemData[AddressInterface::KEY_LASTNAME], $subject->getLastname());
    }

    /**
     * @covers ::getCompany
     * @covers ::setCompany
     *
     * @dataProvider setExternalOrderAddressData
     */
    public function testSku(
        array $itemData
    ): void {
        $subject = new Address();

        $this->assertInstanceOf(
            Address::class,
            $subject->setCompany($itemData[AddressInterface::KEY_COMPANY])
        );

        $this->assertIsString($subject->getCompany());
        $this->assertEquals($itemData[AddressInterface::KEY_COMPANY], $subject->getCompany());
    }

    /**
     * @covers ::getStreet
     * @covers ::setStreet
     *
     * @dataProvider setExternalOrderAddressData
     */
    public function testBasePrice(
        array $itemData
    ): void {
        $subject = new Address();

        $this->assertInstanceOf(
            Address::class,
            $subject->setStreet($itemData[AddressInterface::KEY_STREET])
        );

        $this->assertIsString($subject->getStreet());
        $this->assertEquals($itemData[AddressInterface::KEY_STREET], $subject->getStreet());
    }

    /**
     * @covers ::getPostcode
     * @covers ::setPostcode
     *
     * @dataProvider setExternalOrderAddressData
     */
    public function testPostcode(
        array $itemData
    ): void {
        $subject = new Address();

        $this->assertInstanceOf(
            Address::class,
            $subject->setPostcode($itemData[AddressInterface::KEY_POSTCODE])
        );

        $this->assertIsString($subject->getPostcode());
        $this->assertEquals($itemData[AddressInterface::KEY_POSTCODE], $subject->getPostcode());
    }

    /**
     * @covers ::getCity
     * @covers ::setCity
     *
     * @dataProvider setExternalOrderAddressData
     */
    public function testCity(
        array $itemData
    ): void {
        $subject = new Address();

        $this->assertInstanceOf(
            Address::class,
            $subject->setCity($itemData[AddressInterface::KEY_CITY])
        );

        $this->assertIsString($subject->getCity());
        $this->assertEquals($itemData[AddressInterface::KEY_CITY], $subject->getCity());
    }

    /**
     * @covers ::getCountry
     * @covers ::setCountry
     *
     * @dataProvider setExternalOrderAddressData
     */
    public function testCountry(
        array $itemData
    ): void {
        $subject = new Address();

        $this->assertInstanceOf(
            Address::class,
            $subject->setCountry($itemData[AddressInterface::KEY_COUNTRY])
        );

        $this->assertIsString($subject->getCountry());
        $this->assertEquals($itemData[AddressInterface::KEY_COUNTRY], $subject->getCountry());
    }

    /**
     * @covers ::getAdditionalData
     * @covers ::setAdditionalData
     *
     * @dataProvider setExternalOrderAddressData
     */
    public function testAdditionalData(
        array $itemData
    ): void {
        $subject = new Address();

        $this->assertInstanceOf(
            Address::class,
            $subject->setAdditionalData($itemData[AddressInterface::KEY_ADDITIONAL_DATA])
        );

        $this->assertIsArray($subject->getAdditionalData());
        $this->assertEquals($itemData[AddressInterface::KEY_ADDITIONAL_DATA], $subject->getAdditionalData());
    }

    public function setExternalOrderAddressData(): array
    {
        return [
            'data' => [
                [
                    AddressInterface::KEY_ORDER_ADDRESS_ID => '1',
                    AddressInterface::KEY_FIRSTNAME => 'John',
                    AddressInterface::KEY_LASTNAME => 'Doe',
                    AddressInterface::KEY_COMPANY => 'Acme Inc.',
                    AddressInterface::KEY_STREET => 'Test 1',
                    AddressInterface::KEY_POSTCODE => '1234AB',
                    AddressInterface::KEY_CITY => 'Test',
                    AddressInterface::KEY_COUNTRY => 'NL',
                    AddressInterface::KEY_ADDITIONAL_DATA => []
                ]
            ]
        ];
    }
}
