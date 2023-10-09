<?php

/**
 * Copyright JC-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

interface InvoiceRepositoryInterface
{
    /**
     * @param string $id
     *
     * @return ExternalInvoiceInterface
     */
    public function getById(string $id): ExternalInvoiceInterface;

    /**
     * @param string $incrementId
     *
     * @return ExternalInvoiceInterface
     */
    public function getByIncrementId(string $incrementId): ExternalInvoiceInterface;

    /**
     * @param string $id
     *
     * @return ExternalInvoiceInterface
     */
    public function getByExternalId(string $id): ExternalInvoiceInterface;

    /**
     * @param string $id
     *
     * @return Collection
     */
    public function getByOrder(string $id): Collection;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return array
     */
    public function getList(SearchCriteriaInterface $searchCriteria): array;

    /**
     * @param ExternalInvoiceInterface $externalInvoice
     *
     * @return ExternalInvoiceInterface
     */
    public function save(ExternalInvoiceInterface $externalInvoice): ExternalInvoiceInterface;
}
