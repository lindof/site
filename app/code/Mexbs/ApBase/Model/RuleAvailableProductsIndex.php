<?php
namespace Mexbs\ApBase\Model;

use Magento\Framework\Model\AbstractModel;

class RuleAvailableProductsIndex extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Mexbs\ApBase\Model\ResourceModel\RuleAvailableProductsIndex');
    }
}