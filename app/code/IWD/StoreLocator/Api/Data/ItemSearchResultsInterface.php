<?php

namespace IWD\StoreLocator\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;


/**
 * Interface for storelocator item search results.
 * @api
 */
interface ItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blocks list.
     *
     * @return \IWD\StoreLocator\Api\Data\StoreLocatorInterface[]
     */
    public function getItems();

    /**
     * Set blocks list.
     *
     * @param \IWD\StoreLocator\Api\Data\StoreLocatorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
    
    
}
