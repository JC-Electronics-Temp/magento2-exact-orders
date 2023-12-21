<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Company\Api\Data\CompanyInterface;
use Magento\Company\Api\Data\CompanyOrderInterfaceFactory;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterfaceFactory;
use Magento\Sales\Api\Data\ShippingInterfaceFactory;

class SetCompanyInformation extends AbstractModifier
{
    public function __construct(
        private readonly OrderExtensionFactory $extensionFactory,
        private readonly CompanyManagementInterface $companyManagement,
        private readonly CompanyOrderInterfaceFactory $companyOrderFactory
    ) {
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface         $result
     *
     * @return OrderInterface
     */
    public function process(mixed $model, mixed $result): mixed
    {
        $company  = $this->companyManagement->getByCustomerId($result->getCustomerId());

        if (!$company instanceof CompanyInterface) {
            return $result;
        }

        $companyOrder = $this->companyOrderFactory->create();
        $companyOrder->setCompanyId($company->getId())
            ->setCompanyName($company->getCompanyName());

        /** @var OrderExtension $extensionAttributes */
        $extensionAttributes = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttributes->setCompanyOrderAttributes($companyOrder);

        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
