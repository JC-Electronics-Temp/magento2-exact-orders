<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Model;

use JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface;
use PHPUnit\Framework\TestCase;
use JcElectronics\ExactOrders\Model\AdditionalData;

/**
 * @coversDefaultClass \JcElectronics\ExactOrders\Model\AdditionalData
 */
class AdditionalDataTest extends TestCase
{
    /**
     * @covers ::getKey
     * @covers ::setKey
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testKey(
        array $itemData
    ): void {
        $subject = new AdditionalData();

        $this->assertInstanceOf(
            AdditionalData::class,
            $subject->setKey($itemData[AdditionalDataInterface::KEY_KEY])
        );

        $this->assertIsString($subject->getKey());
        $this->assertEquals($itemData[AdditionalDataInterface::KEY_KEY], $subject->getKey());
    }

    /**
     * @covers ::getValue
     * @covers ::setValue
     *
     * @dataProvider setExternalShipmentItemData
     */
    public function testValue(
        array $itemData
    ): void {
        $subject = new AdditionalData();

        $this->assertInstanceOf(
            AdditionalData::class,
            $subject->setValue($itemData[AdditionalDataInterface::KEY_VALUE])
        );

        $this->assertIsString($subject->getValue());
        $this->assertEquals($itemData[AdditionalDataInterface::KEY_VALUE], $subject->getValue());
    }

    public function setExternalShipmentItemData(): array
    {
        return [
            'data' => [
                [
                    AdditionalDataInterface::KEY_KEY => 'Foo',
                    AdditionalDataInterface::KEY_VALUE => 'Bar',
                ]
            ]
        ];
    }
}
