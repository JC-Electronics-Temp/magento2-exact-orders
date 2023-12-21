<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command;

use Exception;
use Generator;
use JcElectronics\ExactOrders\Api\Data\AttachmentInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use JcElectronics\ExactOrders\Api\OrderRepositoryInterface as ExternalOrderRepositoryInterface;
use JcElectronics\ExactOrders\Model\AttachmentFactory;
use JcElectronics\ExactOrders\Model\ExternalOrderFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\AddressFactory;
use JcElectronics\ExactOrders\Model\ExternalOrder\ItemFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class MigrateSubstituteOrders extends Command
{
    private const COMMAND_NAME = 'exact:migrate:orders',
        COMMAND_DESCRIPTION    = 'Migrate all order from the original Dealer4Dealer ' .
            'substitute module that do not exist in Magento.';

    public function __construct(
        private readonly ExternalOrderRepositoryInterface $externalOrderRepository,
        private readonly ExternalOrderFactory $externalOrderFactory,
        private readonly ItemFactory $externalOrderItemFactory,
        private readonly AttachmentFactory $attachmentFactory,
        private readonly OrderResourceModel $orderResourceModel,
        private readonly OrderRepositoryInterface $orderRepository,
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
            'Limit the number of orders to process in one run (leave empty to process all)'
        );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $counter          = 0;
        $errors = [];
        $progressBar = new ProgressBar($output);

        $substituteOrders = $this->fetchAllSubstituteOrders(
            (int) $input->getOption('limit')
        );

        foreach ($substituteOrders as $orderData) {
            try {
                $magentoOrder = $this->getMagentoOrder($orderData['magento_increment_id']);

                if (!$magentoOrder instanceof OrderInterface) {
                    $this->processExternalOrder($orderData);
                } else if ($magentoOrder->getExtOrderId() === null) {
                    $this->updateExistingOrder($magentoOrder, $orderData);
                }
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
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

    private function processExternalOrder(array $orderData): void
    {
        $externalOrder = $this->externalOrderFactory
            ->create(['data' => $orderData]);

        $this->externalOrderRepository->save($externalOrder);
    }

    private function getMagentoOrder(string $incrementId): ?OrderInterface
    {
        $collection = $this->orderRepository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter('increment_id', $incrementId)
                    ->create()
            );

        return $collection->getTotalCount() > 0 ? current($collection->getItems()) : null;
    }

    private function fetchAllSubstituteOrders(int $limit): Generator
    {
        $connection = $this->orderResourceModel->getConnection();
        $query      = $connection->select()
            ->from(
                ['do' => $this->orderResourceModel->getTable('dealer4dealer_order')]
            )
            ->joinLeft(
                ['so' => $connection->getTableName('sales_order')],
                'so.increment_id = do.magento_increment_id',
                null
            )
            ->where('do.magento_customer_id IS NOT NULL')
            ->where('so.ext_order_id IS NULL');

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
                            AttachmentInterface::ENTITY_TYPE_ORDER,
                            $fileName
                        )
                    )
            );
        } catch (Exception) {
            return null;
        }

        return base64_encode($content);
    }

    private function updateExistingOrder(
        Order $magentoOrder,
        array $orderData
    ): void {
        /** @var AttachmentInterface[] $attachments */
        $attachments = [];
        $magentoOrder->setExtOrderId($orderData['ext_order_id'])
            ->setExtCustomerId($orderData['ext_customer_id'] ?? null);

        /** @var array $attachmentData */
        foreach ($orderData['attachments'] as $attachmentData) {
            $attachments[] = $this->attachmentFactory->create()
                ->setEntityTypeId(AttachmentInterface::ENTITY_TYPE_ORDER)
                ->setParentId((int) $magentoOrder->getEntityId())
                ->setFileName($attachmentData['name'])
                ->setFileContent($attachmentData['file_data']);
        }

        $magentoOrder->setData('attachments', $attachments);

        $this->orderRepository->save($magentoOrder);
    }
}
