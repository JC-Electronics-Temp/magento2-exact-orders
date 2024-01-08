<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\SearchResultsInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrder\SearchResultsInterfaceFactory;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Api\OrderRepositoryInterface;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly MagentoOrderRepositoryInterface $orderRepository,
        private readonly SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly OrderManagementInterface $orderManagement,
        private readonly SearchResultsInterfaceFactory $searchResultsFactory,
        private readonly array $modifiers = []
    ) {
    }

    public function getById(string $id): ExternalOrderInterface
    {
        return $this->processModifiers(
            $this->orderRepository->get($id)
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByIncrementId(string $id): ExternalOrderInterface
    {
        $collection = $this->getList(
            $this->searchCriteriaBuilder
                ->addFilter(OrderInterface::INCREMENT_ID, $id)
                ->create()
        )
        ->getItems();

        if (count($collection) === 0) {
            throw NoSuchEntityException::singleField(OrderInterface::INCREMENT_ID, $id);
        }

        return $this->processModifiers(
            current($collection)
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getByExternalId(string $id): ExternalOrderInterface
    {
        $collection = $this->getList(
            $this->searchCriteriaBuilder
                ->addFilter(OrderInterface::EXT_ORDER_ID, $id)
                ->create()
        )
        ->getItems();

        if (count($collection) === 0) {
            throw NoSuchEntityException::singleField(OrderInterface::EXT_ORDER_ID, $id);
        }

        return $this->processModifiers(
            current($collection)
        );
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->orderRepository->getList($searchCriteria);

        /** @var SearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems(
            array_map(
                fn (OrderInterface $item) => $this->processModifiers($item),
                $collection->getItems()
            )
        );
        $searchResults->setTotalCount($collection->getTotalCount());

        return $searchResults;
    }

    public function save(ExternalOrderInterface $order): int
    {
        /** @var OrderInterface $order */
        $order  = $this->processModifiers($order);
        $result = $order->getEntityId()
            ? $this->orderRepository->save($order)
            : $this->orderManagement->place($order);

        return (int) $result->getEntityId();
    }

    private function processModifiers(mixed $order): mixed
    {
        $result = null;

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiers as $modifier) {
            if (!$modifier->supports($order)) {
                continue;
            }

            $result = $modifier->process($order, $result);
        }

        return $result;
    }
}
