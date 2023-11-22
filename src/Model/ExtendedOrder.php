<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExtendedOrderInterface;
use JcElectronics\ExactOrders\Model\ResourceModel\ExtendedOrder as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class ExtendedOrder extends AbstractModel implements ExtendedOrderInterface
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }
    
    public function getId()
    {
        return $this->_getData(self::KEY_ID);
    }

    public function getOrderId(): int
    {
        return (int) $this->_getData(self::KEY_ORDER_ID);
    }

    public function setOrderId(int $orderId): ExtendedOrderInterface
    {
        return $this->setData(self::KEY_ORDER_ID, $orderId);
    }

    public function getIsExternalOrder(): bool
    {
        return (bool) $this->_getData(self::KEY_IS_EXTERNAL_ORDER);
    }

    public function setIsExternalOrder(bool $isExternalOrder): ExtendedOrderInterface
    {
        return $this->setData(self::KEY_IS_EXTERNAL_ORDER, $isExternalOrder);
    }
}