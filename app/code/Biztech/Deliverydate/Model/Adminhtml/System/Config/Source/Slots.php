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

class Slots implements ArrayInterface
{

    protected $_helper;

    /**
     * @param \Biztech\Deliverydate\Helper\Data $helper
     */
    public function __construct(
        \Biztech\Deliverydate\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function toOptionArray()
    {
        $timeslot = $this->_helper->getTimeSlots();
        $data = [];
        // $data[] = array('value' => '', 'label' => 'No Slots');
        for ($j = 0; $j < count($timeslot); ++$j) {
            $time = $timeslot['timeslot_' . $j]['start_time'] . ' - ' . $timeslot['timeslot_' . $j]['end_time'];
            $data[] = ['value' => $time, 'label' => $time];
        }

        return $data;
    }
}
