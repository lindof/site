<?php

namespace IWD\StoreLocator\Model\Config\Source;

/**
 * Class Metric
 * @package IWD\StoreLocator\Model\Config\Source
 */
class Metric implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Km')], ['value' => 2, 'label' => __('Miles')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [1 => __('Km'), 2 => __('Miles')];
    }
}
