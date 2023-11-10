<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command;

use Exception;
use Generator;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\InvoiceRepositoryInterface as ExternalInvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Model\AttachmentFactory;
use JcElectronics\ExactOrders\Model\ExternalInvoiceFactory;
use JcElectronics\ExactOrders\Model\ExternalInvoice\ItemFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceResourceModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class MigrateSubstituteInvoices extends Command
{
    private const COMMAND_NAME = 'exact:migrate:invoices',
        COMMAND_DESCRIPTION    = 'Migrate all invoices from the original Dealer4Dealer ' .
            'substitute module that do not exist in Magento.';

    public function __construct(
        private readonly ExternalInvoiceRepositoryInterface $externalInvoiceRepository,
        private readonly ExternalInvoiceFactory $externalInvoiceFactory,
        private readonly ItemFactory $externalInvoiceItemFactory,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly InvoiceResourceModel $invoiceResourceModel,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly AddressFactory $externalOrderAddressFactory,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
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
            'Limit the number of invoices to process in one run (leave empty to process all)'
        );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $counter          = 0;
        $errors = [];
        $progressBar = new ProgressBar($output);

        $substituteInvoices = $this->fetchAllSubstituteInvoices(
            (int) $input->getOption('limit')
        );

        foreach ($substituteInvoices as $invoiceData) {
            try {
                /** @var Invoice $magentoInvoice */
                $magentoInvoice = $this->getMagentoInvoice($invoiceData['magento_increment_id']);

                if (!$magentoInvoice instanceof InvoiceInterface) {
                    $magentoInvoice = $this->processExternalInvoice($invoiceData);
                }

                if ($magentoInvoice->getData('ext_invoice_id') === null) {
                    $this->updateExistingInvoice($magentoInvoice, $invoiceData);
                }
            } catch (Throwable $e) {
                $errors[] =  $e->getMessage();
            }

            $counter++;
            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln("\n");
        $output->writeln($errors);
        $output->writeln(
            __(
                "\n%1 of %2 order imports failed", count($errors), $counter
            )
        );

        return Cli::RETURN_SUCCESS;
    }

    private function processExternalInvoice(array $invoiceData): Invoice
    {
        $externalInvoice = $this->externalInvoiceFactory
            ->create(['data' => $invoiceData]);

        return $this->invoiceRepository->get(
            $this->externalInvoiceRepository->save($externalInvoice)
        );
    }

    private function getMagentoInvoice(string $incrementId): ?InvoiceInterface
    {
        $collection = $this->invoiceRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter('increment_id', $incrementId)
                    ->create()
            );

        return $collection->getTotalCount() > 0 ? current($collection->getItems()) : null;
    }

    private function fetchAllSubstituteInvoices(int $limit): Generator
    {
        $connection = $this->invoiceResourceModel->getConnection();
        $query      = $connection->select()
            ->from(
                ['di' => $this->invoiceResourceModel->getTable('dealer4dealer_invoice')]
            )
            ->joinLeft(
                ['si' => $connection->getTableName('sales_invoice')],
                'si.increment_id = di.magento_increment_id',
                null
            )
            ->where('di.magento_customer_id IS NOT NULL')
            ->where('si.ext_invoice_id IS NULL');

        if ($limit > 0) {
            $query->limit($limit);
        }

        yield from array_reduce(
            $connection->fetchAll($query),
            fn (array $carry, array $entity) => array_merge(
                $carry,
                [
                    array_merge(
                        $entity,
                        [
                            'items' => $this->getInvoiceItems((int)$entity['invoice_id']),
                            'billing_address' => $this->getInvoiceAddress((int)$entity['billing_address_id']),
                            'shipping_address' => $this->getInvoiceAddress((int)$entity['shipping_address_id']),
                            'order_ids' => $this->getOrderIdsByInvoice((int) $entity['invoice_id']),
                            'attachments' => $this->getInvoiceAttachments((int)$entity['invoice_id'])
                        ]
                    )
                ]
            ),
            []
        );
    }

    private function getInvoiceItems(int $invoiceId): array
    {
        $connection = $this->invoiceResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->invoiceResourceModel->getTable('dealer4dealer_invoice_item'))
            ->where('invoice_id = ?', $invoiceId);

        return array_reduce(
            $connection->fetchAll($query),
            fn (array $carry, array $entity) => array_merge(
                $carry,
                [
                    $this->externalInvoiceItemFactory->create(
                        [
                            'data' => array_merge(
                                $entity,
                                [
                                    'additional_data' => empty($entity['additional_data'])
                                        ? []
                                        : json_decode($entity['additional_data'])
                                ]
                            )
                        ]
                    )
                ]
            ),
            []
        );
    }

    private function getInvoiceAddress(int $addressId): AddressInterface
    {
        $connection = $this->invoiceResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->invoiceResourceModel->getTable('dealer4dealer_orderaddress'))
            ->where('orderaddress_id = ?', $addressId);

        return $this->externalOrderAddressFactory->create(
            ['data' => $connection->fetchAll($query)]
        );
    }

    private function getInvoiceAttachments(int $entityId): array
    {
        $connection = $this->invoiceResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->invoiceResourceModel->getTable('dealer4dealer_substituteorders_attachment'))
            ->where('entity_type_identifier = ?', $entityId)
            ->where('entity_type = ?', AttachmentInterface::ENTITY_TYPE_INVOICE);

        return array_reduce(
            $connection->fetchAll($query),
            function (array $carry, array $attachment) {
                $fileContent = $this->getFileContent(
                    $attachment['file'],
                    (int) $attachment['magento_customer_identifier']
                );

                if ($fileContent !== null) {
                    $carry[] = [
                        'file_data' => $fileContent,
                        'name' => basename($attachment['file'])
                    ];
                }

                return $carry;
            },
            []
        );
    }

    private function getFileContent(string $fileName, int $customerId): ?string
    {
        try {
            $content = file_get_contents(
                $this->filesystem
                    ->getDirectoryRead(DirectoryList::MEDIA)
                    ->getAbsolutePath(
                        sprintf(
                            'customer/substitute_order/files/%d/%s/%s',
                            $customerId,
                            AttachmentInterface::ENTITY_TYPE_INVOICE,
                            $fileName
                        )
                    )
            );
        } catch (Exception) {
            return null;
        }

        return base64_encode($content);
    }

    private function updateExistingInvoice(
        Invoice $magentoInvoice,
        array $invoiceData
    ): void {
        /** @var AttachmentInterface[] $attachments */
        $attachments = [];
        $magentoInvoice->setData('ext_invoice_id', $invoiceData['ext_invoice_id']);

        /** @var array $attachmentData */
        foreach ($invoiceData['attachments'] as $attachmentData) {
            $attachments[] = $this->attachmentFactory->create()
                ->setEntityTypeId(AttachmentInterface::ENTITY_TYPE_ORDER)
                ->setParentId((int) $magentoInvoice->getEntityId())
                ->setFileName($attachmentData['name'])
                ->setFileContent($attachmentData['file_data']);
        }

        $magentoInvoice->setData('attachments', $attachments);

        $this->invoiceRepository->save($magentoInvoice);
    }

    private function getOrderIdsByInvoice(int $invoiceId): array
    {
        $connection = $this->invoiceResourceModel->getConnection();
        $query = $connection->select()
            ->from(
                ['doir' => $this->invoiceResourceModel->getTable('dealer4dealer_orderinvoicerelation')],
                null
            )
            ->joinLeft(
                ['do' => $this->invoiceResourceModel->getTable('dealer4dealer_order')],
                'doir.order_id = do.order_id',
                null
            )
            ->joinLeft(
                ['so' => $this->invoiceResourceModel->getTable('sales_order')],
                'so.increment_id = do.magento_increment_id',
                'so.entity_id'
            )
            ->where('doir.invoice_id = ?', $invoiceId);

        return $connection->fetchCol($query);
    }
}
