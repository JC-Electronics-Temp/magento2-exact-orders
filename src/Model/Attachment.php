<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Attachment extends AbstractModel implements AttachmentInterface, IdentityInterface
{
    public function __construct(
        Context $context,
        Registry $registry,
        string $cacheTag,
        string $eventPrefix,
        private readonly string $resourceModelClass,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        
        $this->_cacheTag = $cacheTag;
        $this->_eventPrefix = $eventPrefix;
    }

    protected function _construct(): void
    {
        $this->_init($this->resourceModelClass);
    }

    public function getIdentities(): array
    {
        return [$this->_cacheTag . '_' . $this->getId()];
    }

    public function getParentId(): int
    {
        return (int) $this->_getData(static::KEY_PARENT_ID);
    }

    public function setParentId(int $parentId): self
    {
        $this->setData(static::KEY_PARENT_ID, $parentId);

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
