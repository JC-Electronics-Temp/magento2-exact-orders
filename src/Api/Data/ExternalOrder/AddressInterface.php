<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.nl
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data\ExternalOrder;

interface AddressInterface
{
    public const KEY_ORDER_ADDRESS_ID = 'orderaddress_id',
        KEY_FIRSTNAME                 = 'firstname',
        KEY_LASTNAME                  = 'lastname',
        KEY_COMPANY                   = 'company',
        KEY_STREET                    = 'street',
        KEY_POSTCODE                  = 'postcode',
        KEY_CITY                      = 'city',
        KEY_COUNTRY                   = 'country',
        KEY_TELEPHONE                 = 'telephone',
        KEY_ADDITIONAL_DATA           = 'additional_data';

    /**
     * @return string|int|null
     */
    public function getOrderaddressId(): string|int|null;

    /**
     * @param string|int $orderAddressId
     *
     * @return self
     */
    public function setOrderaddressId(string|int $orderAddressId): self;

    /**
     * @return string|null
     */
    public function getFirstname(): ?string;

    /**
     * @param string $firstname
     *
     * @return self
     */
    public function setFirstname(string $firstname): self;

    /**
     * @return string|null
     */
    public function getLastname(): ?string;

    /**
     * @param string $lastname
     *
     * @return self
     */
    public function setLastname(string $lastname): self;

    /**
     * @return string|null
     */
    public function getCompany(): ?string;

    /**
     * @param string $company
     *
     * @return self
     */
    public function setCompany(?string $company): self;

    /**
     * @return string|null
     */
    public function getStreet(): ?string;

    /**
     * @param string $street
     *
     * @return self
     */
    public function setStreet(string $street): self;

    /**
     * @return string|null
     */
    public function getPostcode(): ?string;

    /**
     * @param string|null $postcode
     *
     * @return self
     */
    public function setPostcode(?string $postcode): self;

    /**
     * @return string|null
     */
    public function getCity(): ?string;

    /**
     * @param string $city
     *
     * @return self
     */
    public function setCity(string $city): self;

    /**
     * @return string|null
     */
    public function getCountry(): ?string;

    /**
     * @param string $country
     *
     * @return self
     */
    public function setCountry(string $country): self;

    /**
     * @return string|null
     */
    public function getTelephone(): ?string;

    /**
     * @param string $telephone
     *
     * @return self
     */
    public function setTelephone(string $telephone): self;

    /**
     * @return \JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface[]
     */
    public function getAdditionalData(): array;

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\AdditionalDataInterface[] $additionalData
     *
     * @return self
     */
    public function setAdditionalData(array $additionalData): self;
}
