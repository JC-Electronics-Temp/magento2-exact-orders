<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command;

use JcElectronics\ExactOrders\Model\ResourceModel\Invoice\Attachment;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateAttachments extends Command
{
    private const COMMAND_NAME = 'exact:migrate:attachments',
        COMMAND_DESCRIPTION    = 'Migrate all attachments from the original Dealer4Dealer substitute module.';

    public function __construct(
        private readonly Attachment $attachmentResourceModel,
        private readonly array $entityTypes,
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
            'entity-type',
            null,
            InputOption::VALUE_OPTIONAL,
            'Entity types (comma separated) for the attachments to be migrated (leave empty to process for all)'
        );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $attachments = $this->fetchAttachments(
            (int) $input->getOption('limit'),
            explode(',', $input->getOption('entity-type'))
        );
        $output->writeln(__('Found %1 attachments to process', count($attachments)));

        foreach ($attachments as $attachment) {
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
        $entityId = $this->getEntityIdByType($attachment);
    }

    private function fetchAttachments(int $limit, array $entityTypes): array
    {
        $connection = $this->attachmentResourceModel->getConnection();
        $query      = $connection->select()
            ->from(
                ['di' => $this->attachmentResourceModel->getTable('dealer4dealer_substituteorders_attachment')]
            );

        if ($limit > 0) {
            $query->limit($limit);
        }

        if (!empty($entityTypes)) {
            $query->where('entity_type IN (?)', $entityTypes);
        }

        return $connection->fetchAll($query);
    }

    /**
     * @throws LocalizedException
     */
    private function getEntityIdByType(array $attachment): string
    {
        switch ($attachment['entity_type']) {
            case 'invoice':
                $entityTable     = 'sales_invoice';
                $d4dTable        = 'dealer4dealer_invoice';
                $idColumn        = 'invoice_id';
                $incrementColumn = 'magento_order_id';

                break;

            case 'order':
                $entityTable     = 'sales_order';
                $d4dTable        = 'dealer4dealer_order';
                $idColumn        = 'order_id';
                $incrementColumn = 'magento_order_id';

                break;

            case 'shipment':
                $entityTable     = 'sales_shipment';
                $d4dTable        = 'dealer4dealer_shipment';
                $idColumn        = 'shipment_id';
                $incrementColumn = 'increment_id';

                break;

            default:
                throw new LocalizedException(__('Undefined entity type %1', $attachment['entity_type']));
        }

        $connection = $this->attachmentResourceModel->getConnection();
        $query      = $connection->select()
            ->from(['d4d' => $d4dTable], null)
            ->joinLeft(['et' => $entityTable], sprintf('et.increment_id = d4d.%d', $incrementColumn), 'entity_id')
            ->where(
                sprintf('d4d.%s = ?', $idColumn),
                $attachment['entity_type_identifier']
            );

        return $connection->fetchOne($query);
    }
}
