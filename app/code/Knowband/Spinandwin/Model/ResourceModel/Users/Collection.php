<?php

namespace Knowband\Spinandwin\Model\ResourceModel\Users;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'id_user_list';
    protected $_eventPrefix = 'vss_spinandwin_user_list_collection';
    protected $_eventObject = 'users_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Spinandwin\Model\Users', 'Knowband\Spinandwin\Model\ResourceModel\Users');
    }

}
