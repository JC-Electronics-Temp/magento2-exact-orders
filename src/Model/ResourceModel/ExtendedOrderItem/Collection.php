<?php

namespace JcElectronics\ExactOrders\Model\ResourceModel\ExtendedOrderItem;

use JcElectronics\ExactOrders\Model\ExtendedOrderItem;
use JcElectronics\ExactOrders\Model\ResourceModel\ExtendedOrderItem as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(ExtendedOrderItem::class, ResourceModel::class);
    }
}