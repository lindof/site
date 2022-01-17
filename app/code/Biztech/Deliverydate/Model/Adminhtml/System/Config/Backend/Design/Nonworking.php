<?php

namespace Biztech\Deliverydate\Model\Adminhtml\System\Config\Backend\Design;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class Nonworking extends ArraySerialized
{
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
            if (count($value)) {
                $value = DeliveryDateNonworkingOrderBy($value, 'sort');
                $keys = [];
                for ($i = 0; $i < count($value); ++$i) {
                    $keys[] = $i;
                    if ($value[$i]) {
                        if (checkdate($value[$i]['deliverymonth'], $value[$i]['deliveryday'], $value[$i]['deliveryyear']) === false) {
                            $message = __('Invalid Single Day Off ') . " " . $value[$i]['deliveryday'] . "-" . $value[$i]['deliverymonth'] . "-" . $value[$i]['deliveryyear'];
                            throw new \Exception($message);
                        }
                    }
                }
                $value = array_combine($keys, array_values($value));
            }
        }

        $this->setValue($value);
        parent::beforeSave();
    }
}

function DeliveryDateNonworkingOrderBy($data, $field)
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
