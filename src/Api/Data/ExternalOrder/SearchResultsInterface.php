<?php

/**
 * Copyright Youwe. All rights reserved.
 * https://www.youweagency.com
 */

declare(strict_types=1);

namespace JcElectronics\ExactOrders\Api\Data\ExternalOrder;

interface SearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface[]
     */
    public function getItems();

    /**
     * @param \JcElectronics\ExactOrders\Api\Data\ExternalOrderInterface[] $items
     *
     * @return SearchResultsInterface
     */
    public function setItems(array $items);
}
