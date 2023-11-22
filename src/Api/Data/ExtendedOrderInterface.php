<?php

namespace JcElectronics\ExactOrders\Api\Data;

interface ExtendedOrderInterface
{
    public const KEY_ID       = 'id',
        KEY_ORDER_ID          = 'order_id',
        KEY_IS_EXTERNAL_ORDER = 'is_external_order';

    /**
     * @return string|int
     */
    public function getId();

    public function getOrderId(): int;

    public function setOrderId(int $orderId): self;

    public function getIsExternalOrder(): bool;

    public function setIsExternalOrder(bool $isExternalOrder): self;
}