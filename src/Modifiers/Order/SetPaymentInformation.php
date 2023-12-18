<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\Payment\ExternalPayment;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Payment\Helper\Data;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterfaceFactory;

class SetPaymentInformation implements ModifierInterface
{
    public function __construct(
        private readonly OrderPaymentInterfaceFactory $paymentFactory,
        private readonly Data $paymentHelper
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
        $result->setPayment($this->formatPayment($model));

        return $result;
    }

    public function supports($entity): bool
    {
        return $entity instanceof ExternalOrderInterface;
    }

    private function formatPayment(ExternalOrderInterface $order): OrderPaymentInterface
    {
        /** @var OrderPaymentInterface $payment */
        $payment = $this->paymentFactory->create();
        $payment->setAmountOrdered($order->getGrandtotal())
            ->setAmountPaid($order->getGrandtotal())
            ->setBaseAmountOrdered($order->getBaseGrandtotal() ?: $order->getGrandtotal())
            ->setBaseAmountPaid($order->getBaseGrandtotal() ?: $order->getGrandtotal())
            ->setShippingAmount($order->getShippingAmount())
            ->setBaseShippingAmount($order->getBaseShippingAmount() ?: $order->getShippingAmount())
            ->setMethod($this->getPaymentMethod($order));

        return $payment;
    }

    private function getPaymentMethod(ExternalOrderInterface $order): string
    {
        $code = $order->getPaymentMethod();

        return array_key_exists($code, $this->paymentHelper->getPaymentMethodList())
            ? $code
            : ExternalPayment::PAYMENT_METHOD_CODE;
    }
}
