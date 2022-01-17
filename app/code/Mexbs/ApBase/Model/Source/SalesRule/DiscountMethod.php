<?php
namespace Mexbs\ApBase\Model\Source\SalesRule;

use Magento\Framework\Data\OptionSourceInterface;

class DiscountMethod implements OptionSourceInterface
{
    const SPREADOUT = "spreadout";
    const EDGE = "edge";

    public function toOptionArray()
    {
        return [
            [
                'value' => self::SPREADOUT,
                'label' => __('Spread out')
            ],
            [
                'value' => self::EDGE,
                'label' => __('Edge')
            ]
        ];
    }
}
