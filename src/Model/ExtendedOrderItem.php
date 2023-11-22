<?php

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use JcElectronics\ExactOrders\Api\Data\ExtendedOrderItemInterface;
use JcElectronics\ExactOrders\Model\ResourceModel\ExtendedOrderItem as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class ExtendedOrderItem extends AbstractModel implements ExtendedOrderItemInterface
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    public function getId()
    {
        return $this->_getData(self::KEY_ID);
    }

    public function getOrderItemId(): int
    {
        return $this->_getData(self::KEY_ORDER_ITEM_ID);
    }

    public function setOrderItemId(int $orderItemId): self
    {
        return $this->setData(self::KEY_ORDER_ITEM_ID, $orderItemId);
    }

    public function getSerialNumber(): ?string
    {
        return $this->_getData(self::KEY_SERIAL_NUMBER);
    }

    public function setSerialNumber(string $serialNumber): self
    {
        return $this->setData(self::KEY_SERIAL_NUMBER, $serialNumber);
    }

    public function getExpectedDeliveryDate(): ?string
    {
        return $this->_getData(self::KEY_EXPECTED_DELIVERY_DATE);
    }

    public function setExpectedDeliveryDate(string $expectedDeliveryDate): self
    {
        return $this->setData(self::KEY_EXPECTED_DELIVERY_DATE, $expectedDeliveryDate);
    }
}
