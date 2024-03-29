<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Invoice;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice\ItemCreationFactory;

class InvoiceOrder extends AbstractModifier
{
    public function __construct(
        private readonly InvoiceOrderInterface $invoiceOrder,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ItemCreationFactory $itemCreationFactory
    ) {
    }

    /**
     * @param ExternalInvoiceInterface $model
     * @param InvoiceInterface|null    $result
     *
     * @return InvoiceInterface
     */
    public function process(mixed $model, mixed $result): InvoiceInterface
    {
        if ($result instanceof InvoiceInterface) {
            return $result;
        }

        $orderId     = current($model->getOrderIds());
        $order       = $this->orderRepository->get($orderId);
        $orderStatus = $order->getStatus();

        $invoiceId = $this->invoiceOrder->execute(
            $orderId,
            true,
            $this->formatInvoiceItems($order->getItems())
        );

        /* If the original order state was 'processing', revert the status after creating the invoice */
        if ($order->getState() === Order::STATE_PROCESSING) {
            $order->setStatus($orderStatus);
            $this->orderRepository->save($order);
        }

        return $this->invoiceRepository->get($invoiceId);
    }

    private function formatInvoiceItems(array $items): array
    {
        return array_reduce(
            $items,
            function (array $carry, OrderItemInterface $item) {
                $invoiceItem = $this->itemCreationFactory->create();
                $invoiceItem->setOrderItemId($item->getItemId());
                $invoiceItem->setQty($item->getQtyOrdered());

                $carry[] = $invoiceItem;

                return $carry;
            },
            []
        );
    }
}
