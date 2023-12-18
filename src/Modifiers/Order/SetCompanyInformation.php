<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Modifiers\Order;

use JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface;
use JcElectronics\ExactOrders\Model\Config;
use JcElectronics\ExactOrders\Model\Payment\ExternalPayment;
use JcElectronics\ExactOrders\Modifiers\ModifierInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Company\Api\Data\CompanyInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterface;
use Magento\Sales\Api\Data\ShippingAssignmentInterfaceFactory;
use Magento\Sales\Api\Data\ShippingInterfaceFactory;
use Magento\Store\Model\ScopeInterface;

class SetCompanyInformation implements ModifierInterface
{
    public function __construct(
        private readonly OrderExtensionFactory $extensionFactory,
        private readonly CompanyManagementInterface $companyManagement,
        private readonly \Magento\Company\Api\Data\CompanyOrderInterfaceFactory $companyOrderFactory
    ) {
    }

    /**
     * @param ExternalOrderInterface $model
     * @param OrderInterface         $result
     *
     * @return OrderInterface
     */
    public function process($model, $result)
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

    public function supports($entity): bool
    {
        return $entity instanceof ExternalOrderInterface;
    }
}
