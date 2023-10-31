<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Traits;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;

trait FormatExternalOrderAddressTrait
{
    private function formatExternalOrderAddress(OrderAddressInterface $address): AddressInterface
    {
        return $this->externalOrderAddressFactory->create(
            [
                'data' => [
                    'orderaddress_id' => $address->getEntityId(),
                    'firstname' => $address->getFirstname(),
                    'middlename' => $address->getMiddlename(),
                    'lastname' => $address->getLastname(),
                    'prefix' => $address->getPrefix(),
                    'suffix' => $address->getSuffix(),
                    'company' => $address->getCompany(),
                    'street' => $address->getStreet(),
                    'postcode' => $address->getPostcode(),
                    'city' => $address->getCity(),
                    'country' => $address->getCountryId(),
                    'telephone' => $address->getTelephone(),
                    'fax' => $address->getFax(),
                    'additional_data' => []
                ]
            ]
        );
    }
}
