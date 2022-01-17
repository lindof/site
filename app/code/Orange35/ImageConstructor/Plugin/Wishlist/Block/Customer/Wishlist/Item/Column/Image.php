<?php /** @noinspection PhpUnusedParameterInspection */

namespace Orange35\ImageConstructor\Plugin\Wishlist\Block\Customer\Wishlist\Item\Column;

use Magento\Wishlist\Block\Customer\Wishlist\Item\Column\Image as ColumnImage;
use Magento\Catalog\Block\Product\Image as ProductImage;
use Orange35\ImageConstructor\Helper\Image as ImageHelper;

class Image
{
    private $imageHelper;

    public function __construct(ImageHelper $imageHelper)
    {
        $this->imageHelper = $imageHelper;
    }

    /** @noinspection PhpUnused */
    public function afterGetImage(ColumnImage $column, ProductImage $image, $product, $imageId)
    {
        $item = $column->getItem();
        return $image->setData('layers', $this->imageHelper->getLayers($item, $imageId));
    }
}
