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

class Method implements ArrayInterface
{

    const TYPE_CALENDER = 1;
    const TYPE_TIMESLOT = 2;

    public function toOptionArray()
    {
        return [
            self::TYPE_CALENDER => __('Calender View'),
            self::TYPE_TIMESLOT => __('TimeSlot View'),
        ];
    }
}
