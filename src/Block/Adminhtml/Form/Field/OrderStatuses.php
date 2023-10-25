<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Block\Adminhtml\Form\Field;

use JcElectronics\ExactOrders\Block\Adminhtml\Form\Field\Renderer\OrderState;
use JcElectronics\ExactOrders\Block\Adminhtml\Form\Field\Renderer\OrderStatus;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\BlockInterface;

class OrderStatuses extends AbstractFieldArray
{
    private BlockInterface $orderStateRenderer;

    private BlockInterface $orderStatusRenderer;

    protected function _prepareToRender(): void
    {
        $this->addColumn(
            'state',
            [
                'label' => __('State'),
                'renderer' => $this->getOrderStateRenderer()
            ]
        );
        $this->addColumn(
            'status',
            [
                'label' => __('Status'),
                'renderer' => $this->getOrderStatusRenderer()
            ]
        );
        $this->_addAfter       = false;
        $this->_addButtonLabel = 'Add';
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $state   = $row->getData('state');
        $status  = $row->getData('status');

        /** @var OrderState $orderStateRenderer */
        $orderStateRenderer = $this->getOrderStateRenderer();

        /** @var OrderStatus $orderStatusRenderer */
        $orderStatusRenderer = $this->getOrderStatusRenderer();

        $options['option_' . $orderStateRenderer->calcOptionHash($state)] =
            'selected="selected"';

        $options['option_' . $orderStatusRenderer->calcOptionHash($status)] =
            'selected="selected"';

        $row->setData('option_extra_attrs', $options);
    }

    private function getOrderStateRenderer(): BlockInterface
    {
        return $this->orderStateRenderer ??= $this->getLayout()->createBlock(
            OrderState::class,
            '',
            ['data' => ['is_render_to_js_template' => true]]
        );
    }

    private function getOrderStatusRenderer(): BlockInterface
    {
        return $this->orderStatusRenderer ??= $this->getLayout()->createBlock(
            OrderStatus::class,
            '',
            ['data' => ['is_render_to_js_template' => true]]
        );
    }
}
