<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Model\AttachmentFactory;
use JcElectronics\ExactOrders\Model\AttachmentRepository;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateAttachments extends Command
{
    private const COMMAND_NAME = 'exact:migrate:attachments',
        COMMAND_DESCRIPTION    = 'Migrate all attachments from the original Dealer4Dealer substitute module.';

    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly AttachmentRepository $attachmentRepository,
        private readonly Filesystem $filesystem,
        string $name = null
    ) {
        parent::__construct($name ?? self::COMMAND_NAME);
    }

    protected function configure(): void
    {
        $this->setDescription(self::COMMAND_DESCRIPTION);
        $this->addOption(
            'limit',
            'l',
            InputOption::VALUE_OPTIONAL,
            'Limit the number of attachments to process in one run (leave empty to process all)'
        );

        $this->addOption(
            'type',
            't',
            InputOption::VALUE_REQUIRED,
            'Entity type for the attachments to be migrated'
        );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $attachments = $this->fetchAttachments(
            (int) $input->getOption('limit'),
            $input->getOption('type')
        );
        $output->writeln(__('Found %1 attachments to process', count($attachments)));

        foreach ($attachments as $attachment) {
            if (!$this->getSourceFilePath($attachment)) {
                $output->writeln(
                    __(
                        'No source file found for attachment %1',
                        $attachment['attachment_id']
                    )
                );

                continue;
            }

            $output->writeln(
                __(
                    'Processing attachment %1 for entity type %2',
                    $attachment['attachment_id'],
                    $attachment['entity_type']
                )
            );

            $this->processAttachment($attachment);
        }

        return Cli::RETURN_SUCCESS;
    }

    private function processAttachment(array $attachment): void
    {
                /** @var AttachmentInterface $entity */
        $entity = $this->attachmentFactory->create();
        $entity->setParentId((int) $attachment['magento_entity_id'])
            ->setEntityTypeId($attachment['entity_type'])
            ->setFileName($attachment['file']);

        $this->attachmentRepository->save($entity);

        $this->moveAttachmentToSecureFolder($entity);
    }

    private function fetchAttachments(int $limit, string $entityType): array
    {
        $connection = $this->resourceConnection->getConnection();
        $query      = $connection->select()
            ->from(
                ['dsa' => $connection->getTableName('dealer4dealer_substituteorders_attachment')]
            );

        switch ($entityType) {
            case 'order':
                $query
                    ->joinInner(
                        ['do' => $connection->getTableName('dealer4dealer_order')],
                        'do.order_id = dsa.entity_type_identifier',
                        null
                    )
                    ->joinInner(
                        ['se' => $connection->getTableName('sales_order')],
                        'se.entity_id = do.magento_order_id OR se.increment_id = do.magento_increment_id',
                        ['magento_entity_id' => 'entity_id']
                    );
                break;

            case 'invoice':
                $query
                    ->joinLeft(
                        ['di' => $connection->getTableName('dealer4dealer_invoice')],
                        'di.invoice_id = dsa.entity_type_identifier',
                        null
                    )
                    ->joinLeft(
                        ['se' => $connection->getTableName('sales_invoice')],
                        'se.entity_id = di.magento_invoice_id OR se.increment_id = di.magento_increment_id',
                        null
                    );
                break;

            case 'shipment':
                $query
                    ->joinLeft(
                        ['ds' => $connection->getTableName('dealer4dealer_shipment')],
                        'ds.shipment_id = dsa.entity_type_identifier'
                    )
                    ->joinLeft(
                        ['se' => $connection->getTableName('sales_shipment')],
                        'se.increment_id = ds.increment_id',
                        null
                    );
                break;
        }

        $query->joinLeft(
            ['sea' => $connection->getTableName('sales_exact_attachment')],
            'sea.entity_id = se.entity_id',
            null
        )
        ->where('se.entity_id IS NOT NULL')
        ->where('sea.entity_id IS NULL')
            ->where('dsa.entity_type = ?', $entityType);

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $connection->fetchAll($query);
    }

   private function moveAttachmentToSecureFolder(
       AttachmentInterface $attachment
   ): void {
        $destinationFile = $this->filesystem
           ->getDirectoryRead(DirectoryList::VAR_DIR)
           ->getAbsolutePath(
               sprintf(
                   'customer/substitute_order/files/%d/%s/%s',
                   $attachment->getParentEntity()->getCustomerId(),
                   $attachment->getEntityTypeId(),
                   $attachment->getFileName()
               )
           );

        rename(
            $this->getSourceFilePath($attachment),
            $destinationFile
        );
    }

    private function getSourceFilePath(array $attachment): ?string
    {
        $sourceFile = $this->filesystem
            ->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath(
                sprintf(
                    'customer/substitute_order/files/%d/%s/%s',
                    $attachment['magento_customer_identifier'],
                    $attachment['entity_type'],
                    $attachment['file']
                )
            );

        return file_exists($sourceFile) ? $sourceFile : null;
    }
}
