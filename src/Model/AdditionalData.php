<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface;
use Magento\Framework\DataObject;

class AdditionalData extends DataObject implements AdditionalDataInterface
{
    public function getKey(): string
    {
        return $this->_getData(self::KEY_KEY);
    }

    public function setKey(string $key): self
    {
        $this->setData(self::KEY_KEY, $key);

        return $this;
    }

    public function getValue(): string
    {
        return $this->_getData(self::KEY_VALUE);
    }

    public function setValue(string $value): self
    {
        $this->setData(self::KEY_VALUE, $value);

        return $this;
    }
}
