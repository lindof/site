<?php

namespace Knowband\Spinandwin\Model;

class Users extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'vss_spinandwin_user_list';
    protected $_cacheTag = 'vss_spinandwin_user_list';
    protected $_eventPrefix = 'vss_spinandwin_user_list';

    protected function _construct()
    {
        $this->_init('Knowband\Spinandwin\Model\ResourceModel\Users');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
