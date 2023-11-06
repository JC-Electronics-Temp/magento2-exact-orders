<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model;

use Magento\Directory\Model\Currency;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class Config
{
    private const XML_PATH_ORDER_STATUS_MAPPING = 'exact_orders/orders/default_statuses',
        XML_PATH_EXTERNAL_ORDER_INCREMENT_ID = 'exact_orders/orders/external_order_increment_id';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly Json $serializer
    ) {
    }

    public function getOrderStatuses(): array
    {
        return array_column(
            $this->serializer->unserialize(
                $this->scopeConfig->getValue(
                    self::XML_PATH_ORDER_STATUS_MAPPING,
                    ScopeInterface::SCOPE_STORE
                ) ?? '{}'
            ),
                'status',
                'state'
        );
    }

    public function useExternalIncrementId(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EXTERNAL_ORDER_INCREMENT_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getGlobalCurrencyCode(): string
    {
        return $this->scopeConfig->getValue(
            Currency::XML_PATH_CURRENCY_BASE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function getBaseCurrencyCode(Store $store): string
    {
        return $store->getBaseCurrency()->getCode();
    }
}
