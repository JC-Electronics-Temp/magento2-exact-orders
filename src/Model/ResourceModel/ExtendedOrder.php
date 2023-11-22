<?php

namespace JcElectronics\ExactOrders\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ExtendedOrder extends AbstractDb
{
    private const MAIN_TABLE = 'jcelectronics_exact_orders_sales_order',
        ID_FIELD_NAME        = 'id';

    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}