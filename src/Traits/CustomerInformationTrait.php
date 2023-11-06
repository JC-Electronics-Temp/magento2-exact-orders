<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Traits;

use Magento\Company\Api\Data\CompanyInterface;
use Magento\Customer\Api\Data\CustomerInterface;

trait CustomerInformationTrait
{
    public function getCustomerById(int $customerId): CustomerInterface
    {
        return $this->customerRepository->getById($customerId);
    }

    public function getCompanyByCustomerId(int $customerId): ?CompanyInterface
    {
        return $this->companyManagement->getByCustomerId($customerId);
    }
}
