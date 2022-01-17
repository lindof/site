<?php

namespace IWD\StoreLocator\Model\Config\Source;

use \Magento\Framework\Option\ArrayInterface;

class MapsApi implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'here', 'label' => __('Here WeGo')],
            ['value' => 'google', 'label' => __('Google Maps Api')],
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
            'here' => __('Here WeGo'),
            'google' => __('Google Maps Api'),
        ];
    }
}
