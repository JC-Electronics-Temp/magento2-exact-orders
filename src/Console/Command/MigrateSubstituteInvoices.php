<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\InvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalInvoiceFactory;
use JcElectronics\ExactOrders\Model\ExternalInvoice\ItemFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use Magento\Framework\Console\Cli;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceResourceModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateSubstituteInvoices extends Command
{
    private const COMMAND_NAME = 'exact:migrate:invoices',
        COMMAND_DESCRIPTION    = 'Migrate all invoices from the original Dealer4Dealer ' .
            'substitute module that do not exist in Magento.';

    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly ExternalInvoiceFactory $externalInvoiceFactory,
        private readonly InvoiceResourceModel $invoiceResourceModel,
        private readonly ItemFactory $externalInvoiceItemFactory,
        private readonly AddressFactory $externalOrderAddressFactory,
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
        $substituteInvoices = $this->fetchAllSubstituteInvoices(
            (int) $input->getOption('limit')
        );
        $output->writeln(__('Found %1 invoices to process', count($substituteInvoices)));

        foreach ($substituteInvoices as $invoiceData) {
            $output->writeln(
                __('Processing invoice %1', $invoiceData['magento_increment_id'])
            );

            $this->processExternalInvoice($invoiceData);
        }

        return Cli::RETURN_SUCCESS;
    }

    private function processExternalInvoice(array $invoiceData): void
    {
        $externalInvoice = $this->externalInvoiceFactory
            ->create(['data' => $invoiceData]);

        $this->invoiceRepository->save($externalInvoice);
    }

    private function fetchAllSubstituteInvoices(int $limit): array
    {
        $connection = $this->invoiceResourceModel->getConnection();
        $query      = $connection->select()
            ->from(
                ['di' => $this->invoiceResourceModel->getTable('dealer4dealer_invoice')]
            )
            ->joinLeft(
                ['si' => $this->invoiceResourceModel->getTable('sales_invoice')],
                'si.increment_id = di.magento_increment_id OR si.entity_id = di.magento_invoice_id',
                null
            )
            ->where('si.increment_id IS NULL AND si.entity_id IS NULL')
            ->where('di.magento_customer_id IS NOT NULL');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return array_reduce(
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
                            'order_ids' => $this->getOrderIdsByInvoice((int) $entity['invoice_id'])
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
                'so.increment_id = do.magento_increment_id OR so.entity_id = do.magento_order_id',
                'so.entity_id'
            )
            ->where('doir.invoice_id = ?', $invoiceId);

        return $connection->fetchCol($query);
    }
}
