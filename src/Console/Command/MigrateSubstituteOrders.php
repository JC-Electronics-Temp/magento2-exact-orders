<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command;

use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\OrderRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalOrderFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\ItemFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateSubstituteOrders extends Command
{
    private const COMMAND_NAME = 'exact:migrate:orders',
        COMMAND_DESCRIPTION    = 'Migrate all order from the original Dealer4Dealer ' .
            'substitute module that do not exist in Magento.';

    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ExternalOrderFactory $externalOrderFactory,
        private readonly OrderResourceModel $orderResourceModel,
        private readonly ItemFactory $externalOrderItemFactory,
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
            'Limit the number of orders to process in one run (leave empty to process all)'
        );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $substituteOrders = $this->fetchAllSubstituteOrders(
            (int) $input->getOption('limit')
        );
        $output->writeln(__('Found %1 orders to process', count($substituteOrders)));

        foreach ($substituteOrders as $orderData) {
            $output->writeln(
                __('Processing order %1', $orderData['magento_increment_id'])
            );

            $this->processExternalOrder($orderData);
        }

        return Cli::RETURN_SUCCESS;
    }

    private function processExternalOrder(array $orderData): void
    {
        $externalOrder = $this->externalOrderFactory
            ->create(['data' => $orderData]);

        $this->orderRepository->save($externalOrder);
    }

    private function fetchAllSubstituteOrders(int $limit): array
    {
        $connection = $this->orderResourceModel->getConnection();
        $query      = $connection->select()
            ->from(
                ['do' => $this->orderResourceModel->getTable('dealer4dealer_order')]
            )
            ->joinLeft(
                ['so' => $this->orderResourceModel->getTable('sales_order')],
                'so.increment_id = do.magento_increment_id OR so.entity_id = do.magento_order_id',
                null
            )
        ->where('so.increment_id IS NULL AND so.entity_id IS NULL')
            ->where('do.magento_customer_id IS NOT NULL');

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
                            'items' => $this->getOrderItems((int)$entity['order_id']),
                            'billing_address' => $this->getOrderAddress((int)$entity['billing_address_id']),
                            'shipping_address' => $this->getOrderAddress((int)$entity['shipping_address_id']),
                            'payment_method' => $entity['payment_method'] ?? 'Unknown',
                            'attachments' => $this->getOrderAttachments((int)$entity['order_id'])
                        ]
                    )
                ]
            ),
            []
        );
    }

    private function getOrderItems(int $orderId): array
    {
        $connection = $this->orderResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->orderResourceModel->getTable('dealer4dealer_orderitem'))
            ->where('order_id = ?', $orderId);

        return array_reduce(
            $connection->fetchAll($query),
            fn (array $carry, array $entity) => array_merge(
                $carry,
                [
                    $this->externalOrderItemFactory->create(
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

    private function getOrderAddress(int $addressId): AddressInterface
    {
        $connection = $this->orderResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->orderResourceModel->getTable('dealer4dealer_orderaddress'))
            ->where('orderaddress_id = ?', $addressId);

        return $this->externalOrderAddressFactory->create(
            ['data' => $connection->fetchAll($query)]
        );
    }

    private function getOrderAttachments(int $entityId): array
    {
        $connection = $this->orderResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->orderResourceModel->getTable('dealer4dealer_substituteorders_attachment'))
            ->where('entity_type_identifier = ?', $entityId)
            ->where('entity_type = ?', AttachmentInterface::ENTITY_TYPE_ORDER);

        return array_map(
            function (array $attachment) {
                return [
                    'file_data' => $this->getFileContent($attachment['file']),
                    'name' => $attachment['file']
                ];
            },
            $connection->fetchAll($query)
        );
    }

    private function getFileContent(string $fileName): string
    {
        $content = file_get_contents(
            $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath(
                    sprintf(
                        'substitute_order/%s/%s',
                        AttachmentInterface::ENTITY_TYPE_ORDER,
                        $fileName
                    )
                )
        );

        return base64_encode($content);
    }
}
