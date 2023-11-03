<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data;

interface AttachmentInterface
{
    public const KEY_ID    = 'attachment_id',
        KEY_FILE_NAME      = 'file',
        KEY_ENTITY_ID      = 'entity_id',
        KEY_ENTITY_TYPE_ID = 'entity_type_id';

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

    public function getEntityId(): int;

    public function setEntityId(int $entityId): self;

    public function getEntityTypeId(): int;

    public function setEntityTypeId(string $entityTypeId): self;

    public function getFileName(): string;

    public function setFileName(string $fileName): self;
}
