<?php


namespace Magesales\Converge\Data\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class FormType
 */
class FormType implements ArrayInterface
{
    /**
     * Form types constants
     */
    const MERCHANT = 'merchant';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::MERCHANT,
                'label' => __('Merchant Form'),
            ]
        ];
    }
}
