<?php

namespace Knowband\Spinandwin\Model\ResourceModel;

class ThemeSchedule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('vss_spin_theme_scheduling', 'schedule_id');
    }

}
