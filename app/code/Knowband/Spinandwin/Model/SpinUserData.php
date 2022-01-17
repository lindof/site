<?php

namespace Knowband\Spinandwin\Model;

class SpinUserData extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'vss_spin_user_data';
    protected $_cacheTag = 'vss_spin_user_data';
    protected $_eventPrefix = 'vss_spin_user_data';

    protected function _construct()
    {
        $this->_init('Knowband\Spinandwin\Model\ResourceModel\SpinUserData');
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
