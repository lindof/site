<?php

namespace IWD\StoreLocator\Model\ResourceModel\Item;

use IWD\StoreLocator\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'item_id';

    /**
     * @return \IWD\StoreLocator\Model\ResourceModel\Item\Collection
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('storelocator_item_store', 'item_id');

        return parent::_afterLoad();
    }
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('IWD\StoreLocator\Model\Item', 'IWD\StoreLocator\Model\ResourceModel\Item');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Returns pairs item_id - name
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('item_id', 'name');
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('storelocator_item_store', 'item_id');
    }
}
