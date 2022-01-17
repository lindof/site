<?php

namespace Knowband\Spinandwin\Model\ResourceModel\SpinUserData;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'vss_spin_user_data_collection';
    protected $_eventObject = 'user_data_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Spinandwin\Model\SpinUserData', 'Knowband\Spinandwin\Model\ResourceModel\SpinUserData');
    }

}
