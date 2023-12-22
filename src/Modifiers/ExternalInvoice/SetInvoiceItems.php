<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\ExternalInvoice;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoice\ItemInterface;
use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use JcElectronics\ExactOrders\Model\ExternalInvoice\ItemFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order\Invoice;

class SetInvoiceItems extends AbstractModifier
{
    public function __construct(
        private readonly ItemFactory $itemFactory
    ) {
    }

    /**
     * @param InvoiceInterface&Invoice $model
     * @param ExternalInvoiceInterface $result
     *
     * @return ExternalInvoiceInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $result->setItems(
            $this->formatInvoiceItems($model)
        );

        return $result;
    }

    private function formatInvoiceItems(InvoiceInterface $model): array
    {
        return array_reduce(
            $model->getItems(),
            function (array $carry, Invoice\Item $item) {
                /** @var ItemInterface $invoiceItem */
                $invoiceItem = $this->itemFactory->create();
                $invoiceItem->setInvoiceitemId($item->getOrderItemId())
                    ->setInvoiceId($item->getInvoice()->getEntityId())
                    ->setOrderId($item->getInvoice()->getOrderId())
                    ->setName($item->getName())
                    ->setSku($item->getSku())
                    ->setQty($item->getQty())
                    ->setBasePrice($item->getBasePrice())
                    ->setBaseRowTotal($item->getBaseRowTotal() ?: $item->getRowTotal())
                    ->setBaseTaxAmount($item->getBaseTaxAmount() ?: $item->getTaxAmount() ?: 0)
                    ->setBaseDiscountAmount($item->getBaseDiscountAmount() ?: $item->getDiscountAmount() ?: 0)
                    ->setPrice($item->getPrice())
                    ->setRowTotal($item->getRowTotal())
                    ->setTaxAmount($item->getTaxAmount() ?: 0)
                    ->setDiscountAmount($item->getDiscountAmount() ?: 0);

                $carry[] = $invoiceItem;

                return $carry;
            },
            []
        );
    }
}
