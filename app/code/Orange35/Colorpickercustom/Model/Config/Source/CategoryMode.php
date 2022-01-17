<?php

namespace Orange35\Colorpickercustom\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CategoryMode implements ArrayInterface
{
    const NONE     = '';
    const ALL      = 'all';
    const REQUIRED = 'required';
    const CUSTOM   = 'custom';

    public function toOptionArray()
    {
        return [
            ['value' => self::NONE, 'label' => __('No')],
            ['value' => self::ALL, 'label' => __('All')],
            ['value' => self::REQUIRED, 'label' => __('Required Only')],
            ['value' => self::CUSTOM, 'label' => __('Custom')],
        ];
    }
}
