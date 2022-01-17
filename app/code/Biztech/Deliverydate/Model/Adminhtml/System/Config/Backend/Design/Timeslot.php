<?php

namespace Biztech\Deliverydate\Model\Adminhtml\System\Config\Backend\Design;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class Timeslot extends ArraySerialized
{

    /**
     * @throws \Exception
     */
    public function beforeSave()
    {
        $value1 = $this->getValue();

        if (is_array($value1)) {
            unset($value1['__empty']);
            if (count($value1)) {
                $value1 = DeliveryDateOrderBy1($value1, 'sort_period');
                $keys1 = [];
                for ($i = 0; $i < count($value1); ++$i) {
                    $keys1[] = 'timeslot_' . $i;
                    if (strtotime($value1[$i]['end_time']) <= strtotime($value1[$i]['start_time'])) {
                        throw new \Exception(sprintf("Invalid start time or end time"));
                    }
                }
                $value1 = array_combine($keys1, array_values($value1));
            }
        }
        $this->setValue($value1);

        parent::beforeSave();
    }
}

function DeliveryDateOrderBy1($data, $field)
{
    $code = function ($a, $b) use ($field) {
        if (array_key_exists($field, $a)) {
            return $a[$field] - $b[$field];
        } else {
            return 0;
        }
    };
    usort($data, $code);
    return $data;
}
