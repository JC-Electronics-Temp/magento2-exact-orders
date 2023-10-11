<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data;

interface AttachmentInterface
{
    public const KEY_FILE_DATA = 'file_data',
        KEY_NAME = 'name';

    /**
     * @return string
     */
    public function getFileData(): string;

    /**
     * @param string $fileData
     *
     * @return self
     */
    public function setFileData(string $fileData): self;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name): self;
}
