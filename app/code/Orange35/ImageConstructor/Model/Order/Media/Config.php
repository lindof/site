<?php

namespace Orange35\ImageConstructor\Model\Order\Media;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config implements ConfigInterface
{
    /** @var StoreManagerInterface  */
    protected $storeManager;

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    public function __construct(StoreManagerInterface $storeManager, ScopeConfigInterface $scopeConfig)
    {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $this->getBaseMediaPath();
    }

    public function getBaseMediaThumbnailUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . $this->getBaseMediaThumbnailPath();
    }

    public function getBaseMediaPath()
    {
        return $this->scopeConfig->getValue('orange35_image_constructor/base_media_path/order') ?: 'order';
    }

    public function getBaseMediaThumbnailPath()
    {
        return $this->scopeConfig->getValue('orange35_image_constructor/base_media_path/order_thumbnail')
            ?: 'order/thumbnail';
    }

    public function getLayerBasePath()
    {
        return $this->scopeConfig->getValue('orange35_image_constructor/base_media_path/layer');
    }
}
