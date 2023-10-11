<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Framework\DataObject;

class Attachment extends DataObject implements AttachmentInterface
{
    public function getFileData(): string
    {
        return $this->_getData(self::KEY_FILE_DATA);
    }

    public function setFileData(string $fileData): AttachmentInterface
    {
        $this->setData(self::KEY_FILE_DATA, $fileData);

        return $this;
    }

    public function getName(): string
    {
        return $this->_getData(self::KEY_NAME);
    }

    public function setName(string $name): AttachmentInterface
    {
        $this->setData(self::KEY_NAME, $name);

        return $this;
    }
}
