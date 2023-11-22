<?php

namespace JcElectronics\ExactOrders\Api\Data;

interface ExtendedInvoiceInterface
{
    public const KEY_ID    = 'id',
        KEY_INVOICE_ID     = 'invoice_id',
        KEY_EXT_INVOICE_ID = 'ext_invoice_id';

    /**
     * @return string|int
     */
    public function getId();

    public function getInvoiceId(): int;

    public function setInvoiceId(int $invoiceId): self;

    public function getExtInvoiceId(): string;

    public function setExtInvoiceId(string $extInvoiceId): self;
}