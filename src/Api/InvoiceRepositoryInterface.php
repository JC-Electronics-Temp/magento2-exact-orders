<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface InvoiceRepositoryInterface
{
    /**
     * @param string $id
     *
     * @return ExternalInvoiceInterface
     */
    public function getById(string $id): ExternalInvoiceInterface;

    /**
     * @param string $id
     *
     * @return ExternalInvoiceInterface
     */
    public function getByIncrementId(string $id): ExternalInvoiceInterface;

    /**
     * @param string $id
     *
     * @return ExternalInvoiceInterface
     */
    public function getByExternalId(string $id): ExternalInvoiceInterface;

    /**
     * @param string $id
     *
     * @return ExternalInvoiceInterface[]
     */
    public function getByOrder(string $id): array;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array;

    /**
     * @param ExternalInvoiceInterface $invoice
     *
     * @return int
     */
    public function save(ExternalInvoiceInterface $invoice): int;
}
