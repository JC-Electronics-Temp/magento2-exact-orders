<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Model\ExternalShipment;

use JcElectronics\ExactOrders\Api\Data\ExternalShipment\ItemInterface;
use PHPUnit\Framework\TestCase;
use JcElectronics\ExactOrders\Model\ExternalShipment\Item;

/**
 * @coversDefaultClass \JcElectronics\ExactOrders\Model\ExternalShipment\Item
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ItemTest extends TestCase
{
    /**
     * @covers ::getShipmentitemId
     * @covers ::setShipmentitemId
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testShipmentitemId(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setShipmentitemId($itemData[ItemInterface::KEY_SHIPMENT_ITEM_ID])
        );

        $this->assertIsString($subject->getShipmentitemId());
        $this->assertEquals($itemData[ItemInterface::KEY_SHIPMENT_ITEM_ID], $subject->getShipmentitemId());
    }

    /**
     * @covers ::getShipmentId
     * @covers ::setShipmentId
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testShipmentId(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setShipmentId($itemData[ItemInterface::KEY_SHIPMENT_ID])
        );

        $this->assertIsString($subject->getShipmentId());
        $this->assertEquals($itemData[ItemInterface::KEY_SHIPMENT_ID], $subject->getShipmentId());
    }

    /**
     * @covers ::getName
     * @covers ::setName
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testName(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setName($itemData[ItemInterface::KEY_NAME])
        );

        $this->assertIsString($subject->getName());
        $this->assertEquals($itemData[ItemInterface::KEY_NAME], $subject->getName());
    }

    /**
     * @covers ::getSku
     * @covers ::setSku
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testSku(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setSku($itemData[ItemInterface::KEY_SKU])
        );

        $this->assertIsString($subject->getSku());
        $this->assertEquals($itemData[ItemInterface::KEY_SKU], $subject->getSku());
    }

    /**
     * @covers ::getPrice
     * @covers ::setPrice
     * @covers ::formatCurrencyValue
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testPrice(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setPrice($itemData[ItemInterface::KEY_PRICE])
        );

        $this->assertIsString($subject->getPrice());
        $this->assertEquals(
            str_replace(',', '.', $itemData[ItemInterface::KEY_PRICE]),
            $subject->getPrice()
        );
    }

    /**
     * @covers ::getRowTotal
     * @covers ::setRowTotal
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testRowTotal(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setRowTotal($itemData[ItemInterface::KEY_ROW_TOTAL])
        );

        $this->assertIsString($subject->getRowTotal());
        $this->assertEquals(
            str_replace(',', '.', $itemData[ItemInterface::KEY_ROW_TOTAL]),
            $subject->getRowTotal()
        );
    }

    /**
     * @covers ::getQty
     * @covers ::setQty
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testQty(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setQty($itemData[ItemInterface::KEY_QTY])
        );

        $this->assertIsString($subject->getQty());
        $this->assertEquals(
            str_replace(',', '.', $itemData[ItemInterface::KEY_QTY]),
            $subject->getQty()
        );
    }

    /**
     * @covers ::getWeight
     * @covers ::setWeight
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testWeight(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setWeight($itemData[ItemInterface::KEY_QTY])
        );

        $this->assertIsString($subject->getWeight());
        $this->assertEquals($itemData[ItemInterface::KEY_QTY], $subject->getWeight());
    }

    /**
     * @covers ::getAdditionalData
     * @covers ::setAdditionalData
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testAdditionalData(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setAdditionalData($itemData[ItemInterface::KEY_ADDITIONAL_DATA])
        );

        $this->assertIsArray($subject->getAdditionalData());
        $this->assertEquals($itemData[ItemInterface::KEY_ADDITIONAL_DATA], $subject->getAdditionalData());
    }

    /**
     * @covers ::getDescription
     * @covers ::setDescription
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testDescription(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setDescription($itemData[ItemInterface::KEY_DESCRIPTION])
        );

        $this->assertIsString($subject->getDescription());
        $this->assertEquals($itemData[ItemInterface::KEY_DESCRIPTION], $subject->getDescription());
    }

    public function setExternalShipmentItemData(): array
    {
        return [
            'data' => [
                [
                    ItemInterface::KEY_SHIPMENT_ITEM_ID => '1',
                    ItemInterface::KEY_SHIPMENT_ID => '8',
                    ItemInterface::KEY_NAME => 'Test Product',
                    ItemInterface::KEY_SKU => '0123-4567-89',
                    ItemInterface::KEY_PRICE => '50,0000',
                    ItemInterface::KEY_ROW_TOTAL => '100,0000',
                    ItemInterface::KEY_WEIGHT => '8,0000',
                    ItemInterface::KEY_QTY => '2',
                    ItemInterface::KEY_ADDITIONAL_DATA => [],
                    ItemInterface::KEY_DESCRIPTION => 'Descriptive Text'
                ]
            ]
        ];
    }
}
