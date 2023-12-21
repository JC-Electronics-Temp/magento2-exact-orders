<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data;

interface AdditionalDataInterface
{
    public const KEY_KEY = 'key',
        KEY_VALUE = 'value';

    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @param string $key
     *
     * @return self
     */
    public function setKey(string $key): self;

    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @param string $value
     *
     * @return self
     */
    public function setValue(string $value): self;
}
