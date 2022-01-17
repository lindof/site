<?php

namespace Biztech\Deliverydate\Model\Config;

use Magento\Framework\Option\ArrayInterface;

class Animation implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            "fadeIn" => __('Fade in'),
            "slideDown" => __('Slide down'),
            /*"blind" => __('Blind'),*/
            /*"bounce" => __('Bounce'),*/
            "clip" => __('Clip'),
            /*"drop" => __('Drop'),*/
            /*"fold" => __('Fold'),*/
            /*"slide" => __('Slide'),*/
            "show" => __('Show'),
            "" => __('None'),
        ];
    }
}
