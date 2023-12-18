<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalOrder;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;

class SetBaseOrderData extends AbstractModifier
{
    /**
     * @param OrderInterface&Order   $model
     * @param ExternalOrderInterface $result
     *
     * @return ExternalOrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setOrderId($model->getEntityId())
            ->setInvoiceIds($this->getInvoiceIdsByOrder($model))
            ->setMagentoOrderId($model->getEntityId())
            ->setPaymentMethod($model->getPayment()->getMethod())
            ->setShippingMethod($model->getShippingMethod())
            ->setExtOrderId($model->getExtOrderId())
            ->setState($model->getState())
            ->setOrderDate($model->getCreatedAt())
            ->setMagentoIncrementId($model->getIncrementId())
            ->setUpdatedAt($model->getUpdatedAt());

        return $result;
    }

    /**
     * @return int[]
     */
    private function getInvoiceIdsByOrder(Order $order): array
    {
        return array_map(
            static fn (InvoiceInterface $invoice) => $invoice->getEntityId(),
            $order->getInvoiceCollection()->getItems()
        );
    }
}
