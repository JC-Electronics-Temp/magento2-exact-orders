<?php

namespace JcElectronics\ExactOrders\Api;

use JcElectronics\ExactOrders\Api\Data\ExtendedOrderItemInterface;

interface ExtendedOrderItemRepositoryInterface
{
    public function get(int $id): ExtendedOrderItemInterface;

    public function getByOrderItemId(int $orderItemId): ExtendedOrderItemInterface;

    public function save(ExtendedOrderItemInterface $orderItem): ExtendedOrderItemInterface;

    public function delete(ExtendedOrderItemInterface $orderItem): void;
}