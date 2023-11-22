<?php

namespace JcElectronics\ExactOrders\Api\Data;

interface ExtendedOrderItemInterface
{
    public const KEY_ID            = 'id',
        KEY_ORDER_ITEM_ID          = 'order_item_id',
        KEY_SERIAL_NUMBER          = 'serial_number',
        KEY_EXPECTED_DELIVERY_DATE = 'expected_delivery_date';

    /**
     * @return string|int
     */
    public function getId();

    public function getOrderItemId(): int;

    public function setOrderItemId(int $orderItemId): self;

    public function getSerialNumber(): ?string;

    public function setSerialNumber(string $serialNumber): self;

    public function getExpectedDeliveryDate(): ?string;

    public function setExpectedDeliveryDate(string $expectedDeliveryDate): self;
}
