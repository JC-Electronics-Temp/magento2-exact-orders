<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Invoice;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Magento\Sales\Api\Data\InvoiceInterface;

class SetExtensionAttributes extends AbstractModifier
{
    public function __construct(
        private readonly InvoiceExtensionFactory $extensionFactory
    ) {
    }

    /**
     * @param ExternalInvoiceInterface $model
     * @param InvoiceInterface    $result
     *
     * @return mixed
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $extensionAttributes = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttributes->setExtInvoiceId($model->getExtInvoiceId());
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
