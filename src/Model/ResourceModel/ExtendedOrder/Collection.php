<?php

namespace JcElectronics\ExactOrders\Model\ResourceModel\ExtendedOrder;

use JcElectronics\ExactOrders\Model\ExtendedOrder;
use JcElectronics\ExactOrders\Model\ResourceModel\ExtendedOrder as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(ExtendedOrder::class, ResourceModel::class);
    }
}