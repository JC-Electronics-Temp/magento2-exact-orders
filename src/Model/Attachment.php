<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Attachment extends AbstractModel implements AttachmentInterface, IdentityInterface
{
    public function getIdentities(): array
    {
        return [$this->_cacheTag . '_' . $this->getId()];
    }

    public function getFileName(): string
    {
        return $this->_getData(self::KEY_FILE_NAME);
    }

    public function setFileName(string $fileName): AttachmentInterface
    {
        $this->setData(self::KEY_FILE_NAME, $fileName);

        return $this;
    }
}
