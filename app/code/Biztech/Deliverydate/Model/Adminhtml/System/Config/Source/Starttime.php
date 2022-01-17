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

namespace Biztech\Deliverydate\Model\Adminhtml\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Starttime implements ArrayInterface
{

    public function toOptionArray($time = '23:30')
    {
        $timestamp = strtotime($time);
        $data = [];
        for ($i = 1; $i < 49; ++$i) {
            $timestamp = $timestamp + 30 * 60;
            $time = date('h:i A', $timestamp);
            $data[] = ['value' => $time, 'label' => $time];
        }

        return $data;
    }
}
