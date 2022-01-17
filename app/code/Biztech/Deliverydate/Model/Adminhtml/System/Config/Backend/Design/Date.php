<?php

namespace Biztech\Deliverydate\Model\Adminhtml\System\Config\Backend\Design;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class Date extends ArraySerialized
{
    public function beforeSave()
    {
        $value1 = $this->getValue();
        if (is_array($value1)) {
            unset($value1['__empty']);
            if (count($value1)) {
                $value1 = $this->deliveryDateOrderBy($value1, 'sort_period');
                $keys1 = [];
                for ($i = 0; $i < count($value1); $i++) {
                    $keys1[] = 'nonworking_period_' . uniqid();

                    if ($value1[$i]) {
                        $fromDate = $value1[$i]['from_day'] . "-" . $value1[$i]['from_month'] . "-" . $value1[$i]['from_year'];
                        $toDate = $value1[$i]['to_day'] . "-" . $value1[$i]['to_month'] . "-" . $value1[$i]['to_year'];

                        if (checkdate($value1[$i]['from_month'], $value1[$i]['from_day'], $value1[$i]['from_year']) === false || checkdate($value1[$i]['to_month'], $value1[$i]['to_day'], $value1[$i]['to_year']) === false) {
                            $message = sprintf('Invalid Period From %s and To %s Day Off', $fromDate, $toDate);
                            throw new \Exception($message);
                        }

                        if (strtotime($toDate) <= strtotime($fromDate)) {
                            $message = sprintf('Invalid Period From %s and To %s Day Off', $fromDate, $toDate);
                            throw new \Exception($message);
                        }
                    }
                }
                // exit;
                $value1 = array_combine($keys1, array_values($value1));
            }
        }
        $this->setValue($value1);

        parent::beforeSave();
    }

    public function deliveryDateOrderBy($data, $field)
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
}
