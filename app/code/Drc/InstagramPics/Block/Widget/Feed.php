<?php

namespace Drc\InstagramPics\Block\Widget;

use Drc\InstagramPics\Model\Instagram\Api;
use Drc\InstagramPics\Model\Instagram;
use Drc\InstagramPics\Helper\Config;
use Magento\Framework\View\Element\Template\Context;

class Feed extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_api = null;
    protected $_configHelper;
    protected $_instagram;

    public function __construct(
        Config $configHelper,
        Api $instaApi,
        Instagram $instagram,
        Context $context,
        array $data = []
    ) {
        $this->_configHelper = $configHelper;
        $this->_api = $instaApi;
        $this->_instagram = $instagram;
        parent::__construct($context, $data);
        $this->setTemplate('instagram/widget/feed.phtml');
    }

    public function canShow()
    {
        return $this->_configHelper->isConnected() && ($this->getIsEnabled() || $this->getIsEnabled() === null);
    }

    public function getTitle()
    {
        $title = $this->getData('title');

        $hashtag = $this->getData('hashtag');
        if ($hashtag) {
            $title = str_replace('%s', $hashtag, $title);
        }
        return $title;
    }

    public function getDescription()
    {
        $description = $this->getData('description');
        $hashtag = $this->getData('hashtag');
        if ($hashtag) {
            $description = str_replace('%s', $hashtag, $description);
        }
        return $description;
    }

    public function getImageList()
    {
        
        switch ($this->getData('mode')) {
            case \Drc\InstagramPics\Model\Source\Mode::BY_USER_ID_CODE:
                return $this->_getUserMediaById();
            case \Drc\InstagramPics\Model\Source\Mode::BY_HASHTAG_CODE:
                return $this->_getTagMedia();
            case \Drc\InstagramPics\Model\Source\Mode::BY_PRODUCT_HASHTAG_CODE:
                return $this->_getProductTagMedia();
            case \Drc\InstagramPics\Model\Source\Mode::BY_USER_NAME_CODE:
                return $this->_getUserMediaByName();
            default:
                $imageList = [];
                break;
        }
        return $this->_getUserMediaById();
    }

    protected function _getUserMediaById()
    {
        return $this->_instagram->getUserMediaById(
            $this->getData('user_id'),
            $this->getData('limit_items')
        );
    }

    protected function _getTagMedia()
    {
        if (!$this->getData('hashtag')) {
            return [];
        }
        return $this->_getApi()->getTagMedia(
            $this->getData('hashtag'),
            $this->getData('limit_items')
        );
    }

    protected function _getProductTagMedia()
    {
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        //get current product
        $product        = $objectManager->get('Magento\Framework\Registry')->registry('current_product');
        if (!$product || !$product->getId() || !$product->getInstagramHashtag()) {
            return [];
        }
        $this->setHashtag($product->getInstagramHashtag());
        return $this->_getApi()->getTagMedia(
            $this->getData('hashtag'),
            $this->getData('limit_items')
        );
    }

    protected function _getUserMediaByName()
    {
        return $this->_getApi()->getUserMediaByName(
            $this->getData('user_name'),
            $this->getData('limit_items')
        );
    }

    protected function _getApi()
    {
        if ($this->_api === null) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $object = $objectManager->create('Drc\InstagramPics\Model\Instagram');
            $this->_api = $object;
        }
        return $this->_api;
    }
    
    public function getImageWidth()
    {
        $imageWidth = $this->getData('image_width');
        return $imageWidth;
    }
    public function getImageHeight()
    {
        $imageHeight = $this->getData('image_height');
        return $imageHeight;
    }
}
