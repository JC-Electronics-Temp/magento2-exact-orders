<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\Payment;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Sales\Model\Order\Payment;

class ExternalPayment extends AbstractMethod
{
    public const PAYMENT_METHOD_CODE = 'external';

    protected $_code = self::PAYMENT_METHOD_CODE;

    protected $_isOffline = true;

    protected $_isInitializeNeeded = true;

    protected $_canUseCheckout = false;

    protected $_canUseInternal = true;

    /**
     * @param string     $paymentAction
     * @param DataObject $stateObject
     *
     * @return self
     * @throws LocalizedException
     */
    public function initialize(
        $paymentAction,
        $stateObject
    ) {
        $paymentInfo = $this->getInfoInstance();

        if (!$paymentInfo instanceof Payment) {
            throw new LocalizedException(
                __('No order was found for this payment')
            );
        }

        $order = $paymentInfo->getOrder();

        $stateObject->setData('state', $order->getState());
        $stateObject->setData('status', $order->getStatus());

        return $this;
    }
}
