<?php

namespace Knowband\Spinandwin\Model\ResourceModel\Email;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'template_id';
    protected $_eventPrefix = 'vss_spinandwin_email_collection';
    protected $_eventObject = 'email_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Knowband\Spinandwin\Model\Email', 'Knowband\Spinandwin\Model\ResourceModel\Email');
    }

}
