<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\Config;
use JcElectronics\ExactOrders\Model\Payment\ExternalPayment;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterfaceFactory;
use Magento\Sales\Api\Data\ShippingInterfaceFactory;
use Magento\Store\Model\ScopeInterface;

class SetOrderTotals implements ModifierInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly OrderExtensionFactory $extensionFactory,
        private readonly ShippingAssignmentInterfaceFactory $shippingAssignmentFactory,
        private readonly ShippingInterfaceFactory $shippingFactory
    ) {
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface         $result
     *
     * @return OrderInterface
     */
    public function process($model, $result)
    {
        $numberOfItems =  array_sum(
            array_column($model->getItems(), 'qty')
        );

        $result->setBaseDiscountAmount($model->getBaseDiscountAmount() ?: $model->getDiscountAmount())
            ->setBaseGrandTotal($model->getBaseGrandtotal() ?: $model->getGrandtotal())
            ->setBaseShippingAmount($model->getBaseShippingAmount() ?: $model->getShippingAmount())
            ->setBaseShippingInclTax($model->getBaseShippingAmount() ?: $model->getShippingAmount())
            ->setBaseShippingTaxAmount(0)
            ->setBaseSubtotal($model->getBaseSubtotal() ?: $model->getSubtotal())
            ->setBaseSubtotalInclTax($model->getBaseSubtotal() ?: $model->getSubtotal())
            ->setBaseTaxAmount($model->getBaseTaxAmount() ?: $model->getTaxAmount())
            ->setBaseTotalDue(0)
            ->setBaseTotalPaid($model->getGrandtotal())
            ->setBaseTotalQtyOrdered($numberOfItems)
            ->setDiscountAmount($model->getDiscountAmount())
            ->setGrandTotal($model->getGrandtotal())
            ->setShippingAmount($model->getShippingAmount())
            ->setShippingInclTax($model->getShippingAmount())
            ->setShippingTaxAmount(0)
            ->setSubtotal($model->getSubtotal())
            ->setSubtotalInclTax($model->getSubtotal())
            ->setTaxAmount($model->getTaxAmount())
            ->setTotalItemCount($numberOfItems)
            ->setTotalPaid($model->getGrandtotal())
            ->setTotalQtyOrdered($numberOfItems)
            ->setBaseCurrencyCode($this->config->getBaseCurrencyCode($result->getStore()))
            ->setGlobalCurrencyCode($this->config->getGlobalCurrencyCode())
            ->setOrderCurrencyCode($this->config->getBaseCurrencyCode($result->getStore()));

        return $result;
    }

    public function supports($entity): bool
    {
        return $entity instanceof ExternalOrderInterface;
    }
}
