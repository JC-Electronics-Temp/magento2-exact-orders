<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoice\SearchResultsInterface;
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
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalInvoice\SearchResultsInterface
     */
    public function getByOrder(string $id): SearchResultsInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalInvoice\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param ExternalInvoiceInterface $invoice
     *
     * @return int
     */
    public function save(ExternalInvoiceInterface $invoice): int;
}
