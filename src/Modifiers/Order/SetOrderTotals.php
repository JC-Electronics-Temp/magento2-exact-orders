<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\ItemInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterfaceFactory;
use Magento\Sales\Api\Data\ShippingInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class SetOrderTotals extends AbstractModifier
{
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        private readonly Config $config
    ) {
        parent::__construct(
            $orderRepository,
            $searchCriteriaBuilder
        );
    }

    // phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface&Order   $result
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $numberOfItems = array_reduce(
            $model->getItems(),
            static fn (int $carry, ItemInterface $item) => $carry += $item->getQty(),
            0
        );

        $result->setBaseDiscountAmount($model->getBaseDiscountAmount() ?: $model->getDiscountAmount() ?: 0)
            ->setBaseGrandTotal($model->getBaseGrandtotal() ?: $model->getGrandtotal())
            ->setBaseShippingAmount($model->getBaseShippingAmount() ?: $model->getShippingAmount() ?: 0)
            ->setBaseShippingInclTax($result->getBaseShippingAmount())
            ->setBaseShippingTaxAmount(0)
            ->setBaseSubtotal($model->getBaseSubtotal() ?: $model->getSubtotal())
            ->setBaseSubtotalInclTax($result->getBaseSubtotal())
            ->setBaseTaxAmount($model->getBaseTaxAmount() ?: $model->getTaxAmount() ?: 0)
            ->setBaseTotalDue(0)
            ->setBaseTotalPaid($model->getGrandtotal())
            ->setBaseTotalQtyOrdered($numberOfItems)
            ->setDiscountAmount($model->getDiscountAmount() ?: 0)
            ->setGrandTotal($model->getGrandtotal())
            ->setShippingAmount($model->getShippingAmount() ?: 0)
            ->setShippingInclTax($result->getShippingAmount())
            ->setShippingTaxAmount(0)
            ->setSubtotal($model->getSubtotal())
            ->setSubtotalInclTax($model->getSubtotal())
            ->setTaxAmount($model->getTaxAmount() ?: 0)
            ->setTotalItemCount($numberOfItems)
            ->setTotalPaid($model->getGrandtotal())
            ->setTotalQtyOrdered($numberOfItems)
            ->setBaseCurrencyCode($this->config->getBaseCurrencyCode($result->getStore()))
            ->setGlobalCurrencyCode($this->config->getGlobalCurrencyCode())
            ->setStoreCurrencyCode($this->config->getBaseCurrencyCode($result->getStore()))
            ->setOrderCurrencyCode($this->config->getBaseCurrencyCode($result->getStore()));

        return $result;
    }

    // phpcs:enable
}
