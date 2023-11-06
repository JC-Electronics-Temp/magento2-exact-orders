<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

interface AttachmentInterface
{
    public const KEY_ID    = 'attachment_id',
        KEY_FILE_NAME      = 'file',
        KEY_FILE_CONTENT = 'file_content',
        KEY_ENTITY_ID      = 'entity_id',
        KEY_ENTITY_TYPE_ID = 'entity_type_id',
        ENTITY_TYPE_INVOICE = 'invoice',
        ENTITY_TYPE_ORDER = 'order',
        ENTITY_TYPE_SHIPMENT = 'shipment';

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setId($value);

    public function getParentId(): int;

    public function setParentId(int $parentId): self;

    public function getEntityTypeId(): string;

    public function setEntityTypeId(string $entityTypeId): self;

    public function getFileName(): string;

    public function setFileName(string $fileName): self;

    public function getFileContent(): string;

    public function setFileContent(string $fileContent): self;

    public function getParentEntity(): InvoiceInterface|OrderInterface|ShipmentInterface;
}
