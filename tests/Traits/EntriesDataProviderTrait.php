<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Traits;

trait EntriesDataProviderTrait
{
    public function setEntriesDataProvider(): array
    {
        $testCases = glob(
            sprintf(
                '%s/../data/testCases/%s/*',
                __DIR__,
                self::TEST_ENTITY_TYPE
            )
        );

        return array_combine(
            array_map(
                static fn (string $fileName) => basename($fileName),
                $testCases
            ),
            array_map(
                static fn (string $fileName) => json_decode(
                    file_get_contents($fileName),
                    true
                ),
                $testCases
            )
        );
    }
}
