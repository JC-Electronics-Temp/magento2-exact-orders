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
        KEY_FILE_NAME   = 'file';

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

    public function getFileName(): string;

    public function setFileName(string $fileName): self;
}
