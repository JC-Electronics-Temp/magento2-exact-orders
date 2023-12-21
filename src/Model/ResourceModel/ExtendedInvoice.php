<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ExtendedInvoice extends AbstractDb
{
    private const MAIN_TABLE = 'jcelectronics_exact_orders_sales_invoice',
        ID_FIELD_NAME        = 'id';

    protected function _construct(): void
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}