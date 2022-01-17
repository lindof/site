<?php

namespace Orange35\Colorpickercustom\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;

class Image extends AbstractHelper
{
    const IMAGE_TMP_PATH    = 'tmp/catalog/o35/colorpicker';
    const IMAGE_CACHE_PATH  = 'catalog/o35/colorpicker/cache';
    const IMAGE_PATH        = 'catalog/o35/colorpicker';
    const IMAGE_IMPORT_PATH = 'import/catalog/orange35/color-picker';

    /** Temporary path to upload swatches */
    public $baseTmpPath = self::IMAGE_TMP_PATH;

    /** Image path for resized swatches */
    public $baseCachePath = self::IMAGE_CACHE_PATH;

    /** image path for color swatches */
    public $basePath = self::IMAGE_PATH;

    /** Source path for images to import */
    private $importPath = self::IMAGE_IMPORT_PATH;
    private $filesystem;
    private $storeManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $directory;
    private $imageFactory;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        AdapterFactory $imageFactory
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory = $imageFactory;
    }

    /**
     * @param $image
     * @param null $width
     * @param null $height
     * @param null $keepFrame
     * @return string
     */
    public function getImageUrl($image, $width = null, $height = null, $keepFrame = null)
    {
        if (null === $width && null === $height) {
            return $this->getBaseUrl() . $this->getBasePath() . $image;
        }

        $resizedImagePath = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($this->getCachePath($width, $height)) . $image;

        if (file_exists($resizedImagePath)) {
            return $this->getBaseUrl() . $this->getCachePath($width, $height) . $image;
        }

        return $this->resizeImage($image, $resizedImagePath, $width, $height, (bool) $keepFrame);
    }

    private function resizeImage($image, $resizedImagePath, $width = 200, $height = null, $keepFrame = false)
    {
        $imagePath = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath() . $this->getBasePath() . $image;

        $imageResize = $this->imageFactory->create();
        $imageResize->open($imagePath);
        $imageResize->backgroundColor([255, 255, 255]);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame($keepFrame);
        $imageResize->keepAspectRatio(true);
        $imageResize->resize($width, $height);
        $imageResize->save($resizedImagePath);

        $resizedURL = $this->getBaseUrl() . $this->getCachePath($width, $height) . $image;

        return $resizedURL;
    }

    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
        return $this;
    }

    public function getBaseTmpPath()
    {
        return $this->baseTmpPath;
    }
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
    }

    public function getBaseUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(
                UrlInterface::URL_TYPE_MEDIA
            );
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function setBaseCachePath($baseCachePath)
    {
        $this->baseCachePath = $baseCachePath;
        return $this;
    }

    public function getBaseCachePath()
    {
        return $this->baseCachePath;
    }

    public function getCachePath($width, $height)
    {
        return $this->baseCachePath . '/' . $width . 'x' . $height;
    }

    public function getImportPath()
    {
        return $this->importPath;
    }

    public function setImportPath($importPath)
    {
        $this->importPath = $importPath;
        return $this;
    }

    public function getFilePath($path, $name)
    {
        return rtrim($path, '/') . '/' . ltrim($name, '/');
    }
}
