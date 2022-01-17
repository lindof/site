<?php

namespace Knowband\Spinandwin\Model;

class Email extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'vss_spinandwin_email';
    protected $_cacheTag = 'vss_spinandwin_email';
    protected $_eventPrefix = 'vss_spinandwin_email';

    protected function _construct()
    {
        $this->_init('Knowband\Spinandwin\Model\ResourceModel\Email');
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
