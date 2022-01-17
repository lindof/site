<?php

namespace Knowband\Spinandwin\Model\ResourceModel\Coupons;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'coupon_id';
    protected $_eventPrefix = 'vss_spinandwin_coupons_collection';
    protected $_eventObject = 'coupons_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Spinandwin\Model\Coupons', 'Knowband\Spinandwin\Model\ResourceModel\Coupons');
    }

}
