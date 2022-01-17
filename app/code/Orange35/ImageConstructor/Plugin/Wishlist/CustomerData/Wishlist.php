<?php

namespace Orange35\ImageConstructor\Plugin\Wishlist\CustomerData;

use Magento\Catalog\Helper\ImageFactory;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Item;
use Orange35\ImageConstructor\Helper\Image as ImageHelper;

class Wishlist
{
    /** @var ImageHelper */
    private $helper;
    private $wishlistHelper;

    private $imageHelperFactory;

    /**
     * @param Data $wishlistHelper
     * @param ImageFactory $imageHelperFactory
     * @param ImageHelper $helper
     */
    public function __construct(Data $wishlistHelper, ImageFactory $imageHelperFactory, ImageHelper $helper)
    {
        $this->helper = $helper;
        $this->wishlistHelper = $wishlistHelper;
        $this->imageHelperFactory = $imageHelperFactory;
    }

    public function afterGetSectionData(\Magento\Wishlist\CustomerData\Wishlist $subject, $result)
    {
        $collection = $this->wishlistHelper->getWishlistItemCollection();
        $items = [];
        foreach ($collection as $wishlistItem) {
            $items[] = $this->getItemData($wishlistItem);
        }
        $data['items'] = $items;
        return array_replace_recursive($result, $data);
    }

    private function getItemData(Item $wishlistItem)
    {
        return [
            'image'  => $this->getImageData($wishlistItem),
            'itemId' => $wishlistItem->getId(),
        ];
    }

    private function getImageData(Item $wishlistItem)
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->imageHelperFactory->create()
            ->init($wishlistItem->getProduct(), 'wishlist_sidebar_block');

        $template = $helper->getFrame()
            ? 'Orange35_ImageConstructor/product/image'
            : 'Orange35_ImageConstructor/product/image_with_borders';

        return [
            'layers'   => $this->helper->getLayers($wishlistItem, 'wishlist_sidebar_block'),
            'template' => $template,
        ];
    }
}
