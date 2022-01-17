<?php


namespace Magesales\Converge\Data\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class TransactionType
 */
class TransactionType implements ArrayInterface
{
    const CC_AUTH = 'ccauthonly';
    const CC_SALE = 'ccsale';

    /**
     * Possible actions on order place
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CC_AUTH,
                'label' => __('Credit Card Authorize Only'),
            ],
            [
                'value' => self::CC_SALE,
                'label' => __('Credit Card Sale'),
            ]
        ];
    }
}
