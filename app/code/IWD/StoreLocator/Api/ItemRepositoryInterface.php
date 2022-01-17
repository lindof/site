<?php

namespace IWD\StoreLocator\Api;

/**
 * Interface ItemRepositoryInterface
 * @package IWD\StoreLocator\Api
 * @api
 */
interface ItemRepositoryInterface
{
    /**
     * Retrieve blocks matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \IWD\StoreLocator\Api\Data\ItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListForBlock(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \IWD\StoreLocator\Api\Data\ItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * @return \IWD\StoreLocator\Model\ResourceModel\Item
     */
    public function getListWithEmptyLocation();
}
