<?php

namespace Biztech\Deliverydate\Model\Adminhtml\System\Config\Backend\Design;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class Disabletimeslot extends ArraySerialized
{

    public function beforeSave()
    {
        $value1 = $this->getValue();
        
        if (is_array($value1)) {
            unset($value1['__empty']);
            if (count($value1)) {
                $keys1 = [];
                for ($i = 0; $i < count($value1); ++$i) {
                    $keys1[] = 'disableslot_' . $i;
                }
                $value1 = array_combine($keys1, array_values($value1));
            }
        }
        $this->setValue($value1);
        parent::beforeSave();
    }
}
