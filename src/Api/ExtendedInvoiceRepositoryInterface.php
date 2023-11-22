<?php

namespace JcElectronics\ExactOrders\Api;

use JcElectronics\ExactOrders\Api\Data\ExtendedInvoiceInterface;

interface ExtendedInvoiceRepositoryInterface
{
    public function get(int $id): ExtendedInvoiceInterface;

    public function getByInvoiceId(int $invoiceId): ExtendedInvoiceInterface;

    public function save(ExtendedInvoiceInterface $invoice): ExtendedInvoiceInterface;

    public function delete(ExtendedInvoiceInterface $invoice): void;
}