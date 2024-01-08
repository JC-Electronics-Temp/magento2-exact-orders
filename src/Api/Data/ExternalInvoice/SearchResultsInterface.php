<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data\ExternalInvoice;

use JcElectronics\ExactOrders\Api\Data\ExternalInvoiceInterface;

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
