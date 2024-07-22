<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Test\Model;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Model\Uploader;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use PHPUnit\Framework\TestCase;

class UploaderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::upload()
     *
     * @dataProvider setDataProvider
     */
    public function testUpload(
        string $filename
    ): void {
        $subject = new Uploader($this->createFilesystemMock($filename));
        $subject->upload($this->createAttachmentMock($filename));
    }

    private function createAttachmentMock(string $filename): AttachmentInterface
    {
        $fileInfo   = pathinfo($filename);
        $attachment = $this->createMock(AttachmentInterface::class);
        $attachment
            ->expects(
                $fileInfo['extension'] === 'pdf'
                    ? self::exactly(2)
                    : self::once()
            )
            ->method('getFileName')
            ->willReturn($filename);

        return $attachment;
    }

    public function createFilesystemMock(string $filename): Filesystem
    {
        $fileInfo    = pathinfo($filename);
        $isPdfFile   = $fileInfo['extension'] === 'pdf';
        $destination = $this->createMock(WriteInterface::class);
        $destination
            ->expects($isPdfFile ? self::once() : self::never())
            ->method('writeFile');

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem
            ->expects($isPdfFile ? self::once() : self::never())
            ->method('getDirectoryWrite')
            ->willReturn($destination);

        return $filesystem;
    }

    public function setDataProvider(): array
    {
        return [
            'pdf' => ['filename' => 'random-file.pdf'],
            'jpg' => ['filename' => 'random-image.jpg']
        ];
    }
}
