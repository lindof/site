<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Biztech\Deliverydate\Model\Adminhtml\System\Config\Backend\Design;

class Disabletimeslotdate extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{

    public function beforeSave()
    {
        $value1 = $this->getValue();

        if (is_array($value1)) {
            unset($value1['__empty']);
            if (count($value1)) {
                $keys1 = [];
                for ($i = 0; $i < count($value1); ++$i) {
                    $keys1[] = 'disableslotdate_' . $i;
                    if ($value1) {
                        foreach ($value1 as $key => $value) {
                            if (checkdate($value1[$key]['month'], $value1[$key]['day'], $value1[$key]['year']) === false) {

                                $message = __('Invalid disabled time slot of particular date ') . " " . $value1[$key]['day'] . "-" .  $value1[$key]['month'] . "-" . $value1[$key]['year'];

                                throw new \Exception($message);

                            }
                        }
                    }
                }
                $value1 = array_combine($keys1, array_values($value1));
            }
        }
        $this->setValue($value1);

        parent::beforeSave();
    }
}
