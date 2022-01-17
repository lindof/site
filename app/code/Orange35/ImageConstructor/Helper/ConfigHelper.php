<?php

namespace Orange35\ImageConstructor\Helper;

use Magento\Catalog\Model\Product\Media\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConfigHelper
{
    const BASE_PATH_MERGED      = 'orange35_image_constructor/base_media_path/merged';
    const BASE_PATH_LAYER       = 'orange35_image_constructor/base_media_path/layer';
    const LAYER_IMAGE_MAP       = 'orange35_image_constructor/layer_image_map';
    const SINGLE_IMAGE_STRATEGY = 'orange35_image_constructor/general/single_image_strategy';

    private $scopeConfig;
    private $mediaDirectory;
    private $productMediaConfig;
    private $storeManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $productMediaConfig,
        Filesystem $fs,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productMediaConfig = $productMediaConfig;
        $this->mediaDirectory = $fs->getDirectoryWrite(DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
    }

    private function getPath($configPath)
    {
        return '/' . trim($this->scopeConfig->getValue($configPath), '/');
    }

    public function getMergedImagePath($path = null)
    {
        return $this->getPath(self::BASE_PATH_MERGED) . $path;
    }

    public function getLayerBasePath()
    {
        return $this->scopeConfig->getValue(self::BASE_PATH_LAYER);
    }

    public function getLayerAbsolutePath($path = null)
    {
        return $this->mediaDirectory->getAbsolutePath($this->getLayerBasePath() . $path);
    }

    public function getProductImageAbsolutePath($path = null)
    {
        return $this->mediaDirectory->getAbsolutePath($this->productMediaConfig->getBaseMediaPath() . $path);
    }

    public function getMergedImageAbsolutePath($path = null)
    {
        return $this->mediaDirectory->getAbsolutePath($this->getMergedImagePath($path));
    }

    public function getLayerImageMap()
    {
        return $this->scopeConfig->getValue(self::LAYER_IMAGE_MAP);
    }

    public function getMediaBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    public function isEnabledSingleImageStrategy()
    {
        return (bool) $this->scopeConfig->getValue(self::SINGLE_IMAGE_STRATEGY);
    }
}
