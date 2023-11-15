<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;

class AddImportedProcessingOrderStatus implements DataPatchInterface
{
    private const ORDER_STATUS_IMPORTED = 'external',
        ORDER_STATUS_IMPORTED_LABEL = 'External Order';

    public function __construct(
        private readonly StatusFactory $statusFactory,
        private readonly StatusResource $statusResource
    ) {
    }

    public function apply(): self
    {
        /** @var Status $status */
        $status = $this->statusFactory->create();
        $status->setStatus(self::ORDER_STATUS_IMPORTED)
            ->setData('label', self::ORDER_STATUS_IMPORTED_LABEL);
        $this->statusResource->save($status);

        $status->assignState(
            Order::STATE_PROCESSING,
            false,
            true
        );

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
