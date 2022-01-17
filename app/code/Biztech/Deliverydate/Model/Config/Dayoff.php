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

namespace Biztech\Deliverydate\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class Dayoff implements ArrayInterface
{

    public function toOptionArray()
    {
        $test_array[7] = [
            'value' => '',
            'label' => __('No Day'),
        ];
        $test_array[0] = [
            'value' => 0,
            'label' => __('Sunday'),
        ];
        $test_array[1] = [
            'value' => 1,
            'label' => __('Monday'),
        ];
        $test_array[2] = [
            'value' => 2,
            'label' => __('Tuesday'),
        ];
        $test_array[3] = [
            'value' => 3,
            'label' => __('Wedenesday'),
        ];
        $test_array[4] = [
            'value' => 4,
            'label' => __('Thursday'),
        ];
        $test_array[5] = [
            'value' => 5,
            'label' => __('Friday'),
        ];
        $test_array[6] = [
            'value' => 6,
            'label' => __('Saturday'),
        ];

        return $test_array;
    }

    public function toDays()
    {
        $test_array[0] = [
            'value' => 0,
            'label' => __('Sunday'),
        ];
        $test_array[1] = [
            'value' => 1,
            'label' => __('Monday'),
        ];
        $test_array[2] = [
            'value' => 2,
            'label' => __('Tuesday'),
        ];
        $test_array[3] = [
            'value' => 3,
            'label' => __('Wedenesday'),
        ];
        $test_array[4] = [
            'value' => 4,
            'label' => __('Thursday'),
        ];
        $test_array[5] = [
            'value' => 5,
            'label' => __('Friday'),
        ];
        $test_array[6] = [
            'value' => 6,
            'label' => __('Saturday'),
        ];

        return $test_array;
    }
}
