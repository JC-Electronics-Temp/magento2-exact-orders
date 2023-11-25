<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Block\Adminhtml\Form\Field\Renderer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Config;

class ShippingMethod extends Select
{
    public function __construct(
        Context $context,
        private readonly Config $shippingConfig,
        private readonly ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function setInputName(string $value): self
    {
        return $this->setName($value);
    }

    public function setInputId(string $value): self
    {
        return $this->setId($value);
    }

    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }

        return parent::_toHtml();
    }

    private function getSourceOptions(): array
    {
        $options = [];

        /** @var AbstractCarrierInterface&CarrierInterface $carrier */
        foreach ($this->shippingConfig->getActiveCarriers() as $key => $carrier) {
            $options[] = [
                'label' => $this->scopeConfig->getValue(
                    sprintf('carriers/%s/title', $carrier->getCarrierCode())
                ),
                'value' => array_map(
                    static fn (string $key, string $value) => [
                        'value' => sprintf('%s_%s', $carrier->getCarrierCode(), $key),
                        'label' => $value
                    ],
                    array_keys($carrier->getAllowedMethods()),
                    array_values($carrier->getAllowedMethods()),
                )
            ];
        }

        return $options;
    }
}
