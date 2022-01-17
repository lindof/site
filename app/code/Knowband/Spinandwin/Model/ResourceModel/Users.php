<?php

namespace Knowband\Spinandwin\Model\ResourceModel;

class Users extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('vss_spinandwin_user_list', 'id_user_list');
    }

}
