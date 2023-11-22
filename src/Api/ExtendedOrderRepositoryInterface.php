<?php

namespace JcElectronics\ExactOrders\Api;

use JcElectronics\ExactOrders\Api\Data\ExtendedOrderInterface;

interface ExtendedOrderRepositoryInterface
{
    public function get(int $id): ExtendedOrderInterface;

    public function getByOrderId(int $orderId): ExtendedOrderInterface;

    public function save(ExtendedOrderInterface $order): ExtendedOrderInterface;

    public function delete(ExtendedOrderInterface $order): void;
}