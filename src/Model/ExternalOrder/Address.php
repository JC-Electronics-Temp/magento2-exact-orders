<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Model\ExternalOrder;

use JcElectronics\ExactOrders\Api\Data\ExternalOrder\AddressInterface;
use Magento\Framework\DataObject;

class Address extends DataObject implements AddressInterface
{
    public function getOrderaddressId(): string|int|null
    {
        return $this->_getData(self::KEY_ORDER_ADDRESS_ID);
    }

    public function setOrderaddressId(string|int $orderAddressId): self
    {
        $this->setData(self::KEY_ORDER_ADDRESS_ID, $orderAddressId);

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->_getData(self::KEY_FIRSTNAME);
    }

    public function setFirstname(string $firstname): self
    {
        $this->setData(self::KEY_FIRSTNAME, $firstname);

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->_getData(self::KEY_LASTNAME);
    }

    public function setLastname(string $lastname): self
    {
        $this->setData(self::KEY_LASTNAME, $lastname);

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->_getData(self::KEY_COMPANY);
    }

    public function setCompany(?string $company): self
    {
        $this->setData(self::KEY_COMPANY, $company);

        return $this;
    }

    public function getStreet(): ?string
    {
        $street = $this->_getData(self::KEY_STREET);

        return is_array($street) ? implode(' ', $street) : $street;
    }

    public function setStreet(string $street): self
    {
        $this->setData(self::KEY_STREET, $street);

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->_getData(self::KEY_POSTCODE);
    }

    public function setPostcode(?string $postcode): self
    {
        $this->setData(self::KEY_POSTCODE, $postcode);

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->_getData(self::KEY_CITY);
    }

    public function setCity(string $city): self
    {
        $this->setData(self::KEY_CITY, $city);

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->_getData(self::KEY_COUNTRY);
    }

    public function setCountry(string $country): self
    {
        $this->setData(self::KEY_COUNTRY, $country);

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->_getData(self::KEY_TELEPHONE);
    }

    public function setTelephone(string $telephone): self
    {
        $this->setData(self::KEY_TELEPHONE, $telephone);

        return $this;
    }

    public function getAdditionalData(): array
    {
        return $this->_getData(self::KEY_ADDITIONAL_DATA) ?? [];
    }

    public function setAdditionalData(array $additionalData): self
    {
        $this->setData(self::KEY_ADDITIONAL_DATA, $additionalData);

        return $this;
    }
}
