<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command;

use Exception;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\ShipmentRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use JcElectronics\ExactOrders\Model\ExternalShipmentFactory;
use JcElectronics\ExactOrders\Model\ExternalShipment\ItemFactory;
use JcElectronics\ExactOrders\Model\ExternalShipment\TrackFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResourceModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class MigrateSubstituteShipments extends Command
{
    private const COMMAND_NAME = 'exact:migrate:shipments',
        COMMAND_DESCRIPTION    = 'Migrate all shipments from the original Dealer4Dealer ' .
            'substitute module that do not exist in Magento.';

    public function __construct(
        private readonly ShipmentRepositoryInterface $shipmentRepository,
        private readonly ExternalShipmentFactory $externalShipmentFactory,
        private readonly ShipmentResourceModel $shipmentResourceModel,
        private readonly ItemFactory $externalShipmentItemFactory,
        private readonly AddressFactory $externalOrderAddressFactory,
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
        $errors = [];
        $substituteShipments = $this->fetchAllSubstituteShipments(
            (int) $input->getOption('limit')
        );
        $progressBar = new ProgressBar($output, count($substituteShipments));

        foreach ($substituteShipments as $shipmentData) {
            try {
                $this->processExternalShipment($shipmentData);
            } catch (Throwable $e) {
                $errors[] = __(
                    'An error occured while processing shipment %1: %2',
                    $shipmentData['increment_id'],
                    $e->getMessage()
                );
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $output->writeln(
            __(
                '%1 of %2 shipment imports failed',
                count($errors),
                count($substituteShipments)
            )
        );
        $output->writeln($errors);

        return Cli::RETURN_SUCCESS;
    }

    private function processExternalShipment(array $shipmentData): void
    {
        $externalShipment = $this->externalShipmentFactory
            ->create(['data' => $shipmentData]);

        $this->shipmentRepository->save($externalShipment);
    }

    private function fetchAllSubstituteShipments(int $limit): array
    {
        $connection = $this->shipmentResourceModel->getConnection();
        $query      = $connection->select()
            ->from(
                ['ds' => $this->shipmentResourceModel->getTable('dealer4dealer_shipment')]
            )
            ->joinLeft(
                ['ss' => $this->shipmentResourceModel->getTable('sales_shipment')],
                'ss.increment_id = ds.increment_id',
                null
            )
            ->where('ss.increment_id IS NULL AND ss.entity_id IS NULL')
            ->where('ds.customer_id IS NOT NULL')
            ->where('ds.order_id IS NOT NULL');

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
                            'items' => $this->getShipmentItems((int)$entity['shipment_id']),
                            'billing_address' => $this->getShipmentAddress((int)$entity['billing_address_id']),
                            'shipping_address' => $this->getShipmentAddress((int)$entity['shipping_address_id']),
                            'order_id' => $this->getShipmentOrderId((int) $entity['order_id']),
                            'attachments' => $this->getShipmentAttachments((int)$entity['shipment_id'])
                        ]
                    )
                ]
            ),
            []
        );
    }

    private function getShipmentItems(int $shipmentId): array
    {
        $connection = $this->shipmentResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->shipmentResourceModel->getTable('dealer4dealer_shipmentitem'))
            ->where('shipment_id = ?', $shipmentId);

        return array_reduce(
            $connection->fetchAll($query),
            fn (array $carry, array $entity) => array_merge(
                $carry,
                [
                    $this->externalShipmentItemFactory->create(
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

    private function getShipmentAddress(int $addressId): AddressInterface
    {
        $connection = $this->shipmentResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->shipmentResourceModel->getTable('dealer4dealer_orderaddress'))
            ->where('orderaddress_id = ?', $addressId);

        return $this->externalOrderAddressFactory->create(
            ['data' => $connection->fetchAll($query)]
        );
    }

    private function getShipmentAttachments(int $entityId): array
    {
        $connection = $this->shipmentResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->shipmentResourceModel->getTable('dealer4dealer_substituteorders_attachment'))
            ->where('entity_type_identifier = ?', $entityId)
            ->where('entity_type = ?', AttachmentInterface::ENTITY_TYPE_SHIPMENT);

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
                        'name' => $attachment['file']
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
                            AttachmentInterface::ENTITY_TYPE_SHIPMENT,
                            $fileName
                        )
                    )
            );
        } catch (Exception) {
            return null;
        }

        return base64_encode($content);
    }

    private function getShipmentOrderId(int $orderId): string
    {
        $connection = $this->shipmentResourceModel->getConnection();
        $query = $connection->select()
            ->from(
                ['do' => $this->shipmentResourceModel->getTable('dealer4dealer_order')],
                null
            )
            ->joinLeft(
                ['so' => $this->shipmentResourceModel->getTable('sales_order')],
                'so.increment_id = do.magento_increment_id OR so.entity_id = do.magento_order_id',
                'so.entity_id'
            )
            ->where('do.order_id = ?', $orderId);

        return current($connection->fetchCol($query));
    }
}
