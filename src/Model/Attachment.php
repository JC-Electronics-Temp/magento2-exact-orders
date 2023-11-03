<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Model\ResourceModel\Attachment as AttachmentResourceModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Attachment extends AbstractModel implements AttachmentInterface, IdentityInterface
{
    private const CACHE_TAG = '';

    protected $_cacheTag = self::CACHE_TAG;

    protected function _construct(): void
    {
        $this->_init(AttachmentResourceModel::class);
    }

    public function getIdentities(): array
    {
        return [$this->_cacheTag . '_' . $this->getId()];
    }

    public function getEntityId(): int
    {
        return (int) $this->_getData(static::KEY_ENTITY_ID);
    }

    public function setEntityId(int $entityId): self
    {
        $this->setData(static::KEY_ENTITY_ID, $entityId);

        return $this;
    }

    public function getEntityTypeId(): int
    {
        return (int) $this->_getData(static::KEY_ENTITY_TYPE_ID);
    }

    public function setEntityTypeId(string $entityTypeId): self
    {
        $this->setData(static::KEY_ENTITY_TYPE_ID, $entityTypeId);

        return $this;
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
