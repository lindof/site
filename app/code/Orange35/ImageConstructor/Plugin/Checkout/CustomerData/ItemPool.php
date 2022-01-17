<?php

namespace Orange35\ImageConstructor\Plugin\Checkout\CustomerData;

use Magento\Quote\Model\Quote\Item;
use Orange35\ImageConstructor\Helper\Image as ImageHelper;

class ItemPool
{
    private $imageHeper;

    public function __construct(ImageHelper $imageHeper)
    {
        $this->imageHeper = $imageHeper;
    }

    public function aroundGetItemData($subject, callable $proceed, Item $item)
    {
        $result = $proceed($item);
        if (empty($result['product_image'])) {
            return $result;
        }
        $result['product_image']['layers'] = $this->imageHeper->getLayers($item, 'mini_cart_product_thumbnail');
        return $result;
    }
}
