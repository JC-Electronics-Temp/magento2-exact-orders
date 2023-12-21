<?php

namespace JcElectronics\ExactOrders\Model\ResourceModel\ExtendedInvoice;

use JcElectronics\ExactOrders\Model\ExtendedInvoice;
use JcElectronics\ExactOrders\Model\ResourceModel\ExtendedInvoice as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(ExtendedInvoice::class, ResourceModel::class);
    }
}