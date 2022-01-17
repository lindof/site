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

class Month implements ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Jan')],
            ['value' => '2', 'label' => __('Feb')],
            ['value' => '3', 'label' => __('Mar')],
            ['value' => '4', 'label' => __('Apr')],
            ['value' => '5', 'label' => __('May')],
            ['value' => '6', 'label' => __('Jun')],
            ['value' => '7', 'label' => __('Jul')],
            ['value' => '8', 'label' => __('Aug')],
            ['value' => '9', 'label' => __('Sep')],
            ['value' => '10', 'label' => __('Oct')],
            ['value' => '11', 'label' => __('Nov')],
            ['value' => '12', 'label' => __('Dec')],
        ];
    }
}
