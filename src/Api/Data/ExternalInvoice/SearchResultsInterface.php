<?php

/**
 * Copyright Jc-Electronics. All rights reserved.
 * https://www.jc-electronics.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data\ExternalInvoice;

interface SearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface[]
     */
    public function getItems();

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface[] $items
     *
     * @return SearchResultsInterface
     */
    public function setItems(array $items);
}
