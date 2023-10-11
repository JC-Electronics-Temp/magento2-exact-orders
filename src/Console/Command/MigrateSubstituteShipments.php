<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Console\Command;

use JcElectronics\ExactOrders\Api\Data\ExternalShipmentInterface;
use JcElectronics\ExactOrders\Api\ShipmentRepositoryInterface;
use JcElectronics\ExactOrders\Model\ExternalShipmentFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Console\Cli;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface as MagentoShipmentRepositoryInterface;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\AddressFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResourceModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateSubstituteShipments extends Command
{
    private const COMMAND_NAME = 'exact:migrate:shipments',
        COMMAND_DESCRIPTION    = 'Migrate all shipments from the original Dealer4Dealer ' .
            'substitute module that do not exist in Magento.';

    public function __construct(
        private readonly ShipmentRepositoryInterface $shipmentRepository,
        private readonly ExternalShipmentFactory $externalShipmentFactory,
        private readonly ShipmentResourceModel $shipmentResourceModel,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly MagentoShipmentRepositoryInterface $magentoShipmentRepository,
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
        foreach ($this->fetchAllSubstituteShipments() as $shipmentData) {
            if ($this->hasMagentoShipment($shipmentData)) {
                $output->writeln(
                    __('The shipment with ID %1 already exists', $shipmentData['increment_id'])
                );

                continue;
            }

            /** @var ExternalShipmentInterface $externalShipment */
            $externalShipment = $this->externalShipmentFactory->create($shipmentData);
            $this->shipmentRepository->save($externalShipment);
        }

        return Cli::RETURN_SUCCESS;
    }

    private function fetchAllSubstituteShipments(): array
    {
        $connection = $this->shipmentResourceModel->getConnection();
        $query      = $connection->select()
            ->from($this->shipmentResourceModel->getTable('dealer4dealer_shipment'));

        return $connection->fetchAll($query);
    }

    private function hasMagentoShipment(array $substituteShipment): bool
    {
        if (!$substituteShipment['magento_increment_id']) {
            return false;
        }

        $collection = $this->magentoShipmentRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilter(
                    ShipmentInterface::INCREMENT_ID,
                    $substituteShipment['increment_id']
                )
                ->create()
        );

        return count($collection->getItems()) > 0;
    }
}
