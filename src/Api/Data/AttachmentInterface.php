<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data;

interface AttachmentInterface
{
    public const KEY_ID    = 'attachment_id',
        KEY_FILE_NAME      = 'file',
        KEY_FILE_CONTENT = 'file_content',
        KEY_ENTITY_ID      = 'entity_id',
        KEY_ENTITY_TYPE_ID = 'entity_type_id',
        ENTITY_TYPE_INVOICE = 'invoice',
        ENTITY_TYPE_ORDER = 'order';

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

    /**
     * @return int|null
     */
    public function getParentId(): ?int;

    /**
     * @param int $parentId
     *
     * @return self
     */
    public function setParentId(int $parentId): self;

    /**
     * @return string|null
     */
    public function getEntityTypeId(): ?string;

    /**
     * @param string $entityTypeId
     *
     * @return self
     */
    public function setEntityTypeId(string $entityTypeId): self;

    /**
     * @return string|null
     */
    public function getFileName(): ?string;

    /**
     * @param string $fileName
     *
     * @return self
     */
    public function setFileName(string $fileName): self;

    /**
     * @return string|null
     */
    public function getFileContent(): ?string;

    /**
     * @param string $fileContent
     *
     * @return self
     */
    public function setFileContent(string $fileContent): self;
}
