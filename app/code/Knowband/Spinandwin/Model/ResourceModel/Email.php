<?php

namespace Knowband\Spinandwin\Model\ResourceModel;

class Email extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(\Magento\Framework\Model\ResourceModel\Db\Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('vss_spinandwin_email', 'template_id');
    }

}
