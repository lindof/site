<?php

namespace Orange35\Colorpickercustom\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ChoiceMethod implements ArrayInterface
{
    const TOGGLE = 'toggle';
    const SELECT = 'select';

    public function toOptionArray()
    {
        return [
            ['value' => self::TOGGLE, 'label' => __('Toggle')],
            ['value' => self::SELECT, 'label' => __('Select')],
        ];
    }
}
