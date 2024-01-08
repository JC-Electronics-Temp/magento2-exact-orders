<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Invoice;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;

class FetchExistingInvoice extends AbstractModifier
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * @param ExternalInvoiceInterface $model
     * @param InvoiceInterface|null    $result
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function process(mixed $model, mixed $result): mixed
    {
        if (!$model->getInvoiceId() && empty($model->getOrderIds())) {
            throw new LocalizedException(__('Unable to create an invoice without invoice_id or order_ids'));
        }

        if ($model->getInvoiceId()) {
            return $this->invoiceRepository->get($model->getInvoiceId());
        }

        $orderId = current($model->getOrderIds());

        return $this->getInvoiceByOrder(
            $this->orderRepository->get($orderId)
        );
    }

    private function getInvoiceByOrder(Order $order): ?int
    {
        $collection = $order->getInvoiceCollection();

        if ($collection->getTotalCount() === 0) {
            return null;
        }

        /** @var InvoiceInterface $invoice */
        $invoice = current($collection->getItems());

        return (int) $invoice->getEntityId();
    }
}
