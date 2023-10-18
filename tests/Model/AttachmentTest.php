<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Model;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use PHPUnit\Framework\TestCase;
use JcElectronics\ExactOrders\Model\Attachment;

/**
 * @coversDefaultClass \JcElectronics\ExactOrders\Model\Attachment
 */
class AttachmentTest extends TestCase
{
    /**
     * @covers ::getName
     * @covers ::setName
     *
     * @dataProvider setAttachmentData
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
     * @dataProvider setAttachmentData
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

    public function setAttachmentData(): array
    {
        return [
            'data' => [
                [
                    AttachmentInterface::KEY_NAME => 'test-file.jpg',
                    AttachmentInterface::KEY_FILE_DATA => 'foobar',
                ]
            ]
        ];
    }
}
