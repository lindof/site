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

class Year implements ArrayInterface
{

    public function toOptionArray()
    {
        $data = [];
        $currentYear = date('Y', time());
        for ($i = $currentYear; $i <= 2050; ++$i) {
            $data[] = ['value' => $i, 'label' => $i];
        }

        return $data;
    }
}
