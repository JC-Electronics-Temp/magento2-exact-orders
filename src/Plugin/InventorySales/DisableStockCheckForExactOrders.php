<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\InventorySales;

use Magento\InventorySales\Model\AppendReservations;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory;
use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Store\Api\WebsiteRepositoryInterface;

class DisableStockCheckForExactOrders
{
    public function __construct(
        private readonly OrderExtensionFactory $extensionFactory,
        private readonly WebsiteRepositoryInterface $websiteRepository,
        private readonly SalesEventExtensionFactory $salesEventExtensionFactory,
        private readonly SalesChannelInterfaceFactory $salesChannelFactory
    ) {
    }

    /**
     * If an order is created using the API (Exact Order), the stock check should
     * be skipped.
     *
     * @see \Magento\InventorySales\Model\AppendReservations::reserve()
     */
    public function aroundReserve(
        AppendReservations $subject,
        callable $proceed,
        $websiteId,
        $itemsBySku,
        $order,
        $itemsToSell
    ): array {
        /** @var OrderExtension $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes() ?: $this->extensionFactory->create();

        if (!$extensionAttributes->getIsExternalOrder()) {
            return $proceed($websiteId, $itemsBySku, $order, $itemsToSell);
        }

        $websiteCode         = $this->websiteRepository->getById($websiteId)->getCode();
        $salesEventExtension = $this->salesEventExtensionFactory->create(
            [
                'data' => ['objectIncrementId' => (string) $order->getIncrementId()]
            ]
        );

        $salesChannel = $this->salesChannelFactory->create(
            [
                'data' => [
                    'type' => SalesChannelInterface::TYPE_WEBSITE,
                    'code' => $websiteCode
                ]
            ]
        );

        return [$salesChannel, $salesEventExtension];
    }
}
