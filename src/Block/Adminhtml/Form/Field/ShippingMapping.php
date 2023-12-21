<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Block\Adminhtml\Form\Field;

use JcElectronics\ExactOrders\Block\Adminhtml\Form\Field\Renderer\ShippingMethod;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\BlockInterface;

class ShippingMapping extends AbstractFieldArray
{
    private BlockInterface $shippingMethodRenderer;

    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'value',
            [
                'label' => __('Exact Order Shipment'),
            ]
        );
        $this->addColumn(
            'shipping_method',
            [
                'label' => __('Shipping Method'),
                'renderer' => $this->getShippingMethodRenderer()
            ]
        );
        $this->_addAfter       = false;
        $this->_addButtonLabel = 'Add';
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options        = [];
        $shippingMethod = $row->getData('shipping_method');

        /** @var ShippingMethod $shippingMethodRenderer */
        $shippingMethodRenderer = $this->getShippingMethodRenderer();

        $options['option_' . $shippingMethodRenderer->calcOptionHash($shippingMethod)] =
            'selected="selected"';

        $row->setData('option_extra_attrs', $options);
    }

    private function getShippingMethodRenderer(): BlockInterface
    {
        return $this->shippingMethodRenderer ??= $this->getLayout()->createBlock(
            ShippingMethod::class,
            '',
            ['data' => ['is_render_to_js_template' => true]]
        );
    }
}
