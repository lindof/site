<?php

namespace Knowband\Spinandwin\Model;

class Coupons extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'vss_spinandwin_coupons';
    protected $_cacheTag = 'vss_spinandwin_coupons';
    protected $_eventPrefix = 'vss_spinandwin_coupons';

    protected function _construct()
    {
        $this->_init('Knowband\Spinandwin\Model\ResourceModel\Coupons');
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
