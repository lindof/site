<?php


namespace Magesales\Converge\Data\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PaymentAction
 */
class PaymentAction implements ArrayInterface
{
    /**#@+
     * Config names constants
     */
    const AUTHORIZE = 'authorize';
    const CAPTURE = 'authorize_capture';
    /**#@-*/

    /**
     * Possible actions on order place
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::AUTHORIZE,
                'label' => __('Authorize Only'),
            ],
            [
                'value' => self::CAPTURE,
                'label' => __('Authorize and Capture (Sale)'),
            ]
        ];
    }
}
