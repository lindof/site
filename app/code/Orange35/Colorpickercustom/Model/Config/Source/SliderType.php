<?php

namespace Orange35\Colorpickercustom\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class SliderType implements ArrayInterface
{
    const NONE     = 0;
    const CAROUSEL = 1;
    const SLIDER   = 2;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NONE, 'label' => __('None')],
            ['value' => self::SLIDER, 'label' => __('Slider')],
            ['value' => self::CAROUSEL, 'label' => __('Carousel')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::NONE     => __('None'),
            self::CAROUSEL => __('Carousel'),
            self::SLIDER   => __('Slider'),
        ];
    }
}
