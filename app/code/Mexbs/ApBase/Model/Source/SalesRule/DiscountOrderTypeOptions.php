<?php
namespace Mexbs\ApBase\Model\Source\SalesRule;

use Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product\Combine;
use Magento\Framework\Data\OptionSourceInterface;

class DiscountOrderTypeOptions implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => Combine::DISCOUNT_PRICE_TYPE_CHEAPEST,
                'label' => __('Cheapest')
            ],
            [
                'value' => Combine::DISCOUNT_PRICE_TYPE_MOST_EXPENSIVE,
                'label' => __('Most Expensive')
            ]
        ];
    }
}
