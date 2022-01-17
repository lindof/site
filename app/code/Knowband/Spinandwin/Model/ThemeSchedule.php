<?php

namespace Knowband\Spinandwin\Model;

class ThemeSchedule extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'vss_spin_theme_scheduling';
    protected $_cacheTag = 'vss_spin_theme_scheduling';
    protected $_eventPrefix = 'vss_spin_theme_scheduling';

    protected function _construct()
    {
        $this->_init('Knowband\Spinandwin\Model\ResourceModel\ThemeSchedule');
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
