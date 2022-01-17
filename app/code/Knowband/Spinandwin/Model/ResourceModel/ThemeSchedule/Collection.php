<?php

namespace Knowband\Spinandwin\Model\ResourceModel\ThemeSchedule;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'schedule_id';
    protected $_eventPrefix = 'vss_spin_theme_scheduling_collection';
    protected $_eventObject = 'vss_spin_theme_scheduling_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Spinandwin\Model\ThemeSchedule', 'Knowband\Spinandwin\Model\ResourceModel\ThemeSchedule');
    }

}
