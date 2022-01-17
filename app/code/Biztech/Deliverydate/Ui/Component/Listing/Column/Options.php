<?php

namespace Biztech\Deliverydate\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __(' '),
                'value' => 0
            ],
            [
                'label' => __('Yes'),
                'value' => 1
            ]
        ];
        return $options;
    }
}
