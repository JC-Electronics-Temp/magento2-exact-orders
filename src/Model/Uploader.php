<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Uploader
{
    public function __construct(
        private readonly Filesystem $filesystem
    ) {
    }

    public function upload(
        AttachmentInterface $attachment
    ): ?string {
        $fileInfo = pathinfo($attachment->getFileName());

        if ($fileInfo['extension'] !== 'pdf') {
            return null;
        }

        $filePath = sprintf(
            'substitute_order/%s/%s',
            $attachment->getEntityTypeId(),
            $attachment->getFileName()
        );

        $destination = $this->filesystem
            ->getDirectoryWrite(DirectoryList::VAR_DIR);

        $destination->writeFile(
            $filePath,
            $attachment->getFileContent()
        );

        return $destination->getAbsolutePath($filePath);
    }
}
