<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Api\OrderRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalOrderFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Console\Cli;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly MagentoOrderRepositoryInterface $magentoOrderRepository,
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
        foreach ($this->fetchAllSubstituteOrders() as $orderData) {
            if ($this->hasMagentoOrder($orderData)) {
                $output->writeln(
                    __('The order with ID %1 already exists', $orderData['magento_increment_id'])
                );

                continue;
            }

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

    private function fetchAllSubstituteOrders(): array
    {
        $connection = $this->orderResourceModel->getConnection();
        $query      = $connection->select()
            ->from($this->orderResourceModel->getTable('dealer4dealer_order'));

        return array_reduce(
            $connection->fetchAll($query),
            function (array $carry, array $entity) {
                $carry[] = array_merge(
                    $entity,
                    [
                        'items' => $this->getOrderItems($entity['order_id']),
                        'billing_address' => $this->getOrderAddress($entity['billing_address_id']),
                        'shipping_address' => $this->getOrderAddress($entity['shipping_address_id']),
                    ]
                );

                return $carry;
            },
            []
        );
    }

    private function getOrderItems(int $orderId): array
    {
        $connection = $this->orderResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->orderResourceModel->getTable('dealer4dealer_orderitem'))
            ->where('order_id = ?', $orderId);

        return $connection->fetchAll($query);
    }

    private function getOrderAddress(int $addressId): array
    {
        $connection = $this->orderResourceModel->getConnection();
        $query = $connection->select()
            ->from($this->orderResourceModel->getTable('dealer4dealer_orderaddress'))
            ->where('orderaddress_id = ?', $addressId);

        return $connection->fetchAll($query);
    }

    private function hasMagentoOrder(array $substituteOrder): bool
    {
        if (!$substituteOrder['magento_increment_id']) {
            return false;
        }

        $collection = $this->magentoOrderRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(
                    OrderInterface::INCREMENT_ID,
                    $substituteOrder['magento_increment_id']
                )
                ->create()
        );

        return count($collection->getItems()) > 0;
    }
}
