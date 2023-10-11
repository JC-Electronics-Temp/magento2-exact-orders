<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Api\InvoiceRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalInvoiceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Console\Cli;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoInvoiceRepositoryInterface;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceResourceModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly MagentoInvoiceRepositoryInterface $magentoInvoiceRepository,
        string $name = null
    ) {
        parent::__construct($name ?? self::COMMAND_NAME);
    }

    protected function configure(): void
    {
        $this->setDescription(self::COMMAND_DESCRIPTION);
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        foreach ($this->fetchAllSubstituteInvoices() as $invoiceData) {
            if ($this->hasMagentoInvoice($invoiceData)) {
                $output->writeln(
                    __('The invoice with ID %1 already exists', $invoiceData['magento_invoice_id'])
                );

                continue;
            }

            /** @var ExternalInvoiceInterface $externalInvoice */
            $externalInvoice = $this->externalInvoiceFactory->create($invoiceData);
            $this->invoiceRepository->save($externalInvoice);
        }

        return Cli::RETURN_SUCCESS;
    }

    private function fetchAllSubstituteInvoices(): array
    {
        $connection = $this->invoiceResourceModel->getConnection();
        $query      = $connection->select()
            ->from($this->invoiceResourceModel->getTable('dealer4dealer_invoice'));

        return $connection->fetchAll($query);
    }

    private function hasMagentoInvoice(array $substituteInvoice): bool
    {
        if (!$substituteInvoice['magento_increment_id']) {
            return false;
        }

        $collection = $this->magentoInvoiceRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(
                    InvoiceInterface::INCREMENT_ID,
                    $substituteInvoice['magento_increment_id']
                )
                ->create()
        );

        return count($collection->getItems()) > 0;
    }
}
