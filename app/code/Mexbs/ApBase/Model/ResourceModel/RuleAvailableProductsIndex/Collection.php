<?php
namespace Mexbs\ApBase\Model\ResourceModel\RuleAvailableProductsIndex;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mexbs\ApBase\Model\RuleAvailableProductsIndex', 'Mexbs\ApBase\Model\ResourceModel\RuleAvailableProductsIndex');
    }
}