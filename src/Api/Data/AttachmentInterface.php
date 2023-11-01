<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data;

interface AttachmentInterface
{
    public const KEY_ID = 'attachment_id',
        KEY_FILE_NAME   = 'file',
        KEY_PARENT_ID   = 'parent_id';

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

    public function getFileName(): string;

    public function setFileName(string $fileName): self;
}
