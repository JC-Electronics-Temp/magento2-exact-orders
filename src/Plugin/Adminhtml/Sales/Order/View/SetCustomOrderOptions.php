<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Plugin\Adminhtml\Sales\Order\View;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn;
use Youwe\RepairOrders\Api\Data\RepairInterface;

class SetCustomOrderOptions
{
    public function __construct(
        private readonly TimezoneInterface $timezone
    ) {
    }

    public function afterGetOrderOptions(
        DefaultColumn $subject,
        array $result
    ): array {
        $item                 = $subject->getItem();
        $expectedDeliveryDate = $item->getExtensionAttributes()?->getExpectedDeliveryDate();
        $serialNumber         = $item->getExtensionAttributes()?->getSerialNumber();

        if (!empty($expectedDeliveryDate)) {
            $result[] = [
                'label' => __('Expected Delivery Date'),
                'value' => $this->timezone->formatDate($expectedDeliveryDate, \IntlDateFormatter::LONG, false)
            ];
        }

        if (!empty($serialNumber)) {
            $result[] = [
                'label' => __('Serial Number'),
                'value' => $serialNumber
            ];
        }

        return $result;
    }
}
