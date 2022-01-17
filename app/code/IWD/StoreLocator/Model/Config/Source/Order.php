<?php

namespace IWD\StoreLocator\Model\Config\Source;

/**
 * Class Order
 * @package IWD\StoreLocator\Model\Config\Source
 */
class Order implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Distance')],
            ['value' => 2, 'label' => __('Position')],
            ['value' => 3, 'label' => __('Title')]
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
            1 => __('Distance'),
            2 => __('Position'),
            3 => __('Title')
        ];
    }
}
