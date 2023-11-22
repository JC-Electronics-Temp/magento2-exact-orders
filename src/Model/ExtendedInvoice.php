<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExtendedInvoiceInterface;
use JcElectronics\ExactOrders\Model\ResourceModel\ExtendedInvoice as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class ExtendedInvoice extends AbstractModel implements ExtendedInvoiceInterface
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    public function getId()
    {
        return $this->_getData(self::KEY_ID);
    }

    public function getInvoiceId(): int
    {
        return $this->_getData(self::KEY_INVOICE_ID);
    }

    public function setInvoiceId(int $invoiceId): self
    {
        return $this->setData(self::KEY_INVOICE_ID, $invoiceId);
    }

    public function getExtInvoiceId(): string
    {
        return $this->_getData(self::KEY_EXT_INVOICE_ID);
    }

    public function setExtInvoiceId(string $extInvoiceId): self
    {
        return $this->setData(self::KEY_EXT_INVOICE_ID, $extInvoiceId);
    }
}
