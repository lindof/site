<?php

namespace Orange35\Colorpickercustom\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TooltipType implements ArrayInterface
{
    const NONE       = 0;
    const TEXT       = 1;
    const IMAGE_TEXT = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::IMAGE_TEXT, 'label' => __('Image & Text')],
            ['value' => self::TEXT, 'label' => __('Text Only')],
            ['value' => self::NONE, 'label' => __('None')],
        ];
    }
}
