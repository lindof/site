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

class Timeformat implements ArrayInterface
{

    /**
     * Get possible sharing configuration options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'g:i a' => __('g:i a'),
            'H:i:s' => __('H:i:s'),
        ];
    }
}
