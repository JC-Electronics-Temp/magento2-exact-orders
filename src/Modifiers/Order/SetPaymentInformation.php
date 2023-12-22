<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\Payment\ExternalPayment;
use Magento\Payment\Helper\Data;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterfaceFactory;

class SetPaymentInformation extends AbstractModifier
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
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setPayment($this->formatPayment($model));

        return $result;
    }

    private function formatPayment(ExternalOrderInterface $order): OrderPaymentInterface
    {
        /** @var OrderPaymentInterface $payment */
        $payment = $this->paymentFactory->create();
        $payment->setAmountOrdered($order->getGrandtotal())
            ->setAmountPaid($order->getGrandtotal())
            ->setBaseAmountOrdered($order->getBaseGrandtotal() ?: $order->getGrandtotal())
            ->setBaseAmountPaid($order->getBaseGrandtotal() ?: $order->getGrandtotal())
            ->setShippingAmount($order->getShippingAmount() ?: 0)
            ->setBaseShippingAmount($order->getBaseShippingAmount() ?: $order->getShippingAmount() ?: 0)
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
