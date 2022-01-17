<?php

namespace Knowband\Spinandwin\Model\ResourceModel;

class SpinUserData extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('vss_spin_user_data', 'id');
    }

}
