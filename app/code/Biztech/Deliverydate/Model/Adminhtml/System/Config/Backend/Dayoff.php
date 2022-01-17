<?php

namespace Biztech\Deliverydate\Model\Adminhtml\System\Config\Backend;

class Dayoff extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        $label = $this->getData('field_config/label');
        
        $search_this = [
            0 => null,
            1 => 0,
            2 => 1,
            3 => 2,
            4 => 3,
            5 => 4,
            6 => 5,
            7 => 6,
        ];
        $also_search_this = [
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
            6 => 6,
        ];
        if (count(array_intersect($search_this, $this->getValue())) == count($search_this)) {
            throw new \Magento\Framework\Exception\ValidatorException(__($label . ': Available All DayOff can not be selected.'));
        }
        if (count(array_intersect($also_search_this, $this->getValue())) == count($also_search_this)) {
            throw new \Magento\Framework\Exception\ValidatorException(__($label . ': Available All DayOff can not be selected.'));
        }

        $this->setValue($this->getValue());

        parent::beforeSave();
    }
}
