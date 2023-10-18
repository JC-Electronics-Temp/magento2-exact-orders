<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Model\ExternalInvoice;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoice\ItemInterface;
use PHPUnit\Framework\TestCase;
use JcElectronics\ExactOrders\Model\ExternalInvoice\Item;

/**
 * @coversDefaultClass \JcElectronics\ExactOrders\Model\ExternalInvoice\Item
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ItemTest extends TestCase
{
    /**
     * @covers ::getInvoiceitemId
     * @covers ::setInvoiceitemId
     *
     * @dataProvider setExternalInvoiceItemData
     */
    public function testInvoiceitemId(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setInvoiceitemId($itemData[ItemInterface::KEY_INVOICE_ITEM_ID])
        );

        $this->assertIsString($subject->getInvoiceitemId());
        $this->assertEquals($itemData[ItemInterface::KEY_INVOICE_ITEM_ID], $subject->getInvoiceitemId());
    }

    /**
     * @covers ::getInvoiceId
     * @covers ::setInvoiceId
     *
     * @dataProvider setExternalInvoiceItemData
     */
    public function testInvoiceId(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setInvoiceId($itemData[ItemInterface::KEY_INVOICE_ID])
        );

        $this->assertIsString($subject->getInvoiceId());
        $this->assertEquals($itemData[ItemInterface::KEY_INVOICE_ID], $subject->getInvoiceId());
    }

    /**
     * @covers ::getOrderId
     * @covers ::setOrderId
     *
     * @dataProvider setExternalInvoiceItemData
     */
    public function testOrderId(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setOrderId($itemData[ItemInterface::KEY_ORDER_ID])
        );

        $this->assertIsString($subject->getOrderId());
        $this->assertEquals($itemData[ItemInterface::KEY_ORDER_ID], $subject->getOrderId());
    }

    /**
     * @covers ::getName
     * @covers ::setName
     *
     * @dataProvider setExternalInvoiceItemData
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
     * @dataProvider setExternalInvoiceItemData
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
     * @covers ::getBasePrice
     * @covers ::setBasePrice
     * @covers ::formatCurrencyValue
     *
     * @dataProvider setExternalInvoiceItemData
     */
    public function testBasePrice(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setBasePrice($itemData[ItemInterface::KEY_BASE_PRICE])
        );

        $this->assertIsString($subject->getBasePrice());
        $this->assertEquals(
            str_replace(',', '.', $itemData[ItemInterface::KEY_BASE_PRICE]),
            $subject->getBasePrice()
        );
    }

    /**
     * @covers ::getPrice
     * @covers ::setPrice
     * @covers ::formatCurrencyValue
     *
     * @dataProvider setExternalInvoiceItemData
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
     * @covers ::getBaseRowTotal
     * @covers ::setBaseRowTotal
     *
     * @dataProvider setExternalInvoiceItemData
     */
    public function testBaseRowTotal(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setBaseRowTotal($itemData[ItemInterface::KEY_BASE_ROW_TOTAL])
        );

        $this->assertIsString($subject->getBaseRowTotal());
        $this->assertEquals(
            str_replace(',', '.', $itemData[ItemInterface::KEY_BASE_ROW_TOTAL]),
            $subject->getBaseRowTotal()
        );
    }

    /**
     * @covers ::getRowTotal
     * @covers ::setRowTotal
     *
     * @dataProvider setExternalInvoiceItemData
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
     * @covers ::getBaseTaxAmount
     * @covers ::setBaseTaxAmount
     *
     * @dataProvider setExternalInvoiceItemData
     */
    public function testBaseTaxAmount(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setBaseTaxAmount($itemData[ItemInterface::KEY_BASE_TAX_AMOUNT])
        );

        $this->assertIsString($subject->getBaseTaxAmount());
        $this->assertEquals(
            str_replace(',', '.', $itemData[ItemInterface::KEY_BASE_TAX_AMOUNT]),
            $subject->getBaseTaxAmount()
        );
    }

    /**
     * @covers ::getTaxAmount
     * @covers ::setTaxAmount
     *
     * @dataProvider setExternalInvoiceItemData
     */
    public function testTaxAmount(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setTaxAmount($itemData[ItemInterface::KEY_TAX_AMOUNT])
        );

        $this->assertIsString($subject->getTaxAmount());
        $this->assertEquals(
            str_replace(',', '.', $itemData[ItemInterface::KEY_TAX_AMOUNT]),
            $subject->getTaxAmount()
        );
    }

    /**
     * @covers ::getQty
     * @covers ::setQty
     *
     * @dataProvider setExternalInvoiceItemData
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
     * @covers ::getBaseDiscountAmount
     * @covers ::setBaseDiscountAmount
     *
     * @dataProvider setExternalInvoiceItemData
     */
    public function testBaseDiscountAmount(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setBaseDiscountAmount($itemData[ItemInterface::KEY_BASE_DISCOUNT_AMOUNT])
        );

        $this->assertIsString($subject->getBaseDiscountAmount());
        $this->assertEquals(
            str_replace(',', '.', $itemData[ItemInterface::KEY_BASE_DISCOUNT_AMOUNT]),
            $subject->getBaseDiscountAmount()
        );
    }

    /**
     * @covers ::getDiscountAmount
     * @covers ::setDiscountAmount
     *
     * @dataProvider setExternalInvoiceItemData
     */
    public function testDiscountAmount(
        array $itemData
    ): void {
        $subject = new Item();

        $this->assertInstanceOf(
            Item::class,
            $subject->setDiscountAmount($itemData[ItemInterface::KEY_DISCOUNT_AMOUNT])
        );

        $this->assertIsString($subject->getDiscountAmount());
        $this->assertEquals(
            str_replace(',', '.', $itemData[ItemInterface::KEY_DISCOUNT_AMOUNT]),
            $subject->getDiscountAmount()
        );
    }

    /**
     * @covers ::getAdditionalData
     * @covers ::setAdditionalData
     *
     * @dataProvider setExternalInvoiceItemData
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

    public function setExternalInvoiceItemData(): array
    {
        return [
            'data' => [
                [
                    ItemInterface::KEY_INVOICE_ITEM_ID => '1',
                    ItemInterface::KEY_INVOICE_ID => '8',
                    ItemInterface::KEY_ORDER_ID => '2',
                    ItemInterface::KEY_NAME => 'Test Product',
                    ItemInterface::KEY_SKU => '0123-4567-89',
                    ItemInterface::KEY_BASE_PRICE => '50,0000',
                    ItemInterface::KEY_PRICE => '50,0000',
                    ItemInterface::KEY_BASE_ROW_TOTAL => '100,0000',
                    ItemInterface::KEY_ROW_TOTAL => '100,0000',
                    ItemInterface::KEY_BASE_TAX_AMOUNT => '8,0000',
                    ItemInterface::KEY_TAX_AMOUNT => '8,0000',
                    ItemInterface::KEY_QTY => '2',
                    ItemInterface::KEY_ADDITIONAL_DATA => [],
                    ItemInterface::KEY_BASE_DISCOUNT_AMOUNT => '0,0000',
                    ItemInterface::KEY_DISCOUNT_AMOUNT => '0,0000'
                ]
            ]
        ];
    }
}
