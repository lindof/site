<?php

namespace Orange35\Colorpickercustom\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CssWidth implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => '',
                'label' => __('Default'),
            ],
            [
                'value' => 1,
                'label' => __('Thin'),
            ],
            [
                'value' => 3,
                'label' => __('Medium'),
            ],
            [
                'value' => 5,
                'label' => __('Thick'),
            ],
        ];
    }
}
