<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Model;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Model\Attachment;
use PHPUnit\Framework\TestCase;
use JcElectronics\ExactOrders\Model\ExternalInvoice;

/**
 * @coversDefaultClass \JcElectronics\ExactOrders\Model\ExternalInvoice
 */
class ExternalInvoiceTest extends TestCase
{
    /**
     * @covers ::getName
     * @covers ::setName
     *
     * @dataProvider setExternalInvoiceData
     */
    public function testName(
        array $itemData
    ): void {
        $subject = new Attachment();

        $this->assertInstanceOf(
            Attachment::class,
            $subject->setName($itemData[AttachmentInterface::KEY_FILE_DATA])
        );

        $this->assertIsString($subject->getName());
        $this->assertEquals($itemData[AttachmentInterface::KEY_FILE_DATA], $subject->getName());
    }

    /**
     * @covers ::getFileData
     * @covers ::setFileData
     *
     * @dataProvider setExternalInvoiceData
     */
    public function testFileData(
        array $itemData
    ): void {
        $subject = new Attachment();

        $this->assertInstanceOf(
            Attachment::class,
            $subject->setFileData($itemData[AttachmentInterface::KEY_FILE_DATA])
        );

        $this->assertIsString($subject->getFileData());
        $this->assertEquals($itemData[AttachmentInterface::KEY_FILE_DATA], $subject->getFileData());
    }

    public function setExternalInvoiceData(): array
    {
        return [
            'data' => [
                [
                    ExternalInvoiceInterface::KEY_ID => '4',
                    ExternalInvoiceInterface::KEY_INVOICE_ID => '4',
                    ExternalInvoiceInterface::KEY_ORDER_IDS => [5, 10, 12],
                    ExternalInvoiceInterface::KEY_MAGENTO_INVOICE_ID => '4',
                    ExternalInvoiceInterface::KEY_MAGENTO_CUSTOMER_ID => '10',
                    ExternalInvoiceInterface::KEY_EXTERNAL_INVOICE_ID => '100483',
                    ExternalInvoiceInterface::KEY_PO_NUMBER => '1897',
                    ExternalInvoiceInterface::KEY_BASE_GRAND_TOTAL => '78,5800',
                    ExternalInvoiceInterface::KEY_BASE_SUBTOTAL => '78,5800',
                    ExternalInvoiceInterface::KEY_GRAND_TOTAL => '78,5800',
                    ExternalInvoiceInterface::KEY_SUBTOTAL => '78,5800',
                    ExternalInvoiceInterface::KEY_STATE => 'complete',
                    ExternalInvoiceInterface::KEY_SHIPPING_ADDRESS => [],
                    ExternalInvoiceInterface::KEY_BILLING_ADDRESS => [],
                    ExternalInvoiceInterface::KEY_BASE_DISCOUNT_AMOUNT => '0,0000',
                    ExternalInvoiceInterface::KEY_DISCOUNT_AMOUNT => '0,0000',
                    ExternalInvoiceInterface::KEY_INVOICE_DATE => '2023-01-01 05:48:13',
                    ExternalInvoiceInterface::KEY_BASE_TAX_AMOUNT => '12,0000',
                    ExternalInvoiceInterface::KEY_TAX_AMOUNT => '12,0000',
                    ExternalInvoiceInterface::KEY_BASE_SHIPPING_AMOUNT => '0,0000',
                    ExternalInvoiceInterface::KEY_SHIPPING_AMOUNT => '0,0000',
                    ExternalInvoiceInterface::KEY_MAGENTO_INCREMENT_ID => '10005874',
                    ExternalInvoiceInterface::KEY_ADDITIONAL_DATA => [],
                    ExternalInvoiceInterface::KEY_ATTACHMENTS => [],
                    ExternalInvoiceInterface::KEY_ITEMS => [],
                ]
            ]
        ];
    }
}
