<?php

namespace Orange35\ImageConstructor\Plugin\Checkout\Block\Cart\Item;

use Orange35\ImageConstructor\Helper\Image as ImageHelper;
use Magento\Checkout\Block\Cart\Item\Renderer as MagentoRenderer;
use Magento\Catalog\Block\Product\Image as ProductImage;

class Renderer
{
    private $imageHeper;

    public function __construct(ImageHelper $imageHeper)
    {
        $this->imageHeper = $imageHeper;
    }

    public function afterGetImage(MagentoRenderer $renderer, ProductImage $image, $product, $imageId)
    {
        $item = $renderer->getItem();
        return $image->setData('layers', $this->imageHeper->getLayers($item, $imageId));
    }
}
