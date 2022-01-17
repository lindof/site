<?php

namespace Orange35\ImageConstructor\Helper;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Image\NotLoadInfoImageException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;

class Image extends ImageHelper
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $directory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $imageFactory;

    public function __construct(
        Context $context,
        \Magento\Catalog\Model\Product\ImageFactory $productImageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Catalog\Model\View\Asset\PlaceholderFactory $placeholderFactory = null,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        AdapterFactory $imageFactory
    ) {
        parent::__construct($context, $productImageFactory, $assetRepo, $viewConfig, $placeholderFactory);
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory = $imageFactory;
    }

    /**
     * @param $layer
     * @param int|null $width
     * @param int|null $height
     * @param bool $keepFrame
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLayerUrl($layer, $width = null, $height = null, $keepFrame = null)
    {
        if (null === $width && null === $height) {
            return $this->getBaseUrl() . $this->getBasePath() . $layer;
        }
        $resizedImagePath = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($this->getCachePath($width, $height)) . $layer;

        if (file_exists($resizedImagePath)) {
            return $this->getBaseUrl() . $this->getCachePath($width, $height) . $layer;
        }
        return $this->resizeImage($layer, $resizedImagePath, $width, $height, (bool) $keepFrame);
    }

    public function getLayers(ItemInterface $item, $imageId)
    {
        $layers = [];
        if (!($optionIds = $item->getOptionByCode('option_ids'))) {
            return $layers;
        }
        $product = $item->getProduct();
        $this->init($product, $imageId);
        foreach (explode(',', $optionIds->getValue()) as $optionId) {
            if (!($option = $product->getOptionById($optionId))) {
                continue;
            }
            $itemOption = $item->getOptionByCode('option_' . $option->getId());
            foreach (explode(',', $itemOption->getValue()) as $optionValueId) {
                if (!($value = $option->getValueById($optionValueId))) {
                    continue;
                }
                $layer = $value->getData('layer');
                if ($layer) {
                    $layers[] = $this->getLayerImage($layer);
                }
            }
        }
        return $layers;
    }

    /**
     * @param string $image
     * @param null $field
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLayerImage($image, $field = null)
    {
        try {
            $imagesize = $this->getResizedImageInfo();
        } catch (NotLoadInfoImageException $exception) {
            $imagesize = [$this->getWidth(), $this->getHeight()];
        }

        $keepFrame = $this->getFrame();

        $width = $keepFrame ? $this->getWidth() : $imagesize[0];
        $height = $keepFrame ? $this->getHeight() : $imagesize[1];

        $src = $this->getLayerUrl($image, $width, $height, $keepFrame);

        $item = [
            'src'    => $src,
            'width'  => $width,
            'height' => $height,
        ];

        return null === $field ? $item : (array_key_exists($field, $item) ? $item[$field] : null);
    }

    /**
     * Fix issue when $frame === "0" it is not passed to model in
     * vendor/magento/module-catalog/Helper/Image.php:215
     * @return bool|false|string
     */
    public function getFrame()
    {
        $frame = $this->getAttribute('frame');
        if (is_bool($frame)) {
            return $frame;
        }
        if ('false' === $frame) {
            return false;
        }
        return true;
    }

    /**
     * @param $layer string layer value
     * @param $resizedImagePath string
     * @param int $width
     * @param int $height
     * @param bool $keepFrame
     * @return string
     * @throws \Exception
     */
    private function resizeImage(
        $layer,
        $resizedImagePath,
        $width = 200,
        $height = null,
        $keepFrame = false
    ) {
        $imagePath = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath() . $this->getBasePath() . $layer;

        /** @var \Magento\Framework\Image\Adapter\Gd2 $imageResize */
        $imageResize = $this->imageFactory->create();
        $imageResize->open($imagePath);
        $imageResize->backgroundColor([255, 255, 255]);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame($keepFrame);
        $imageResize->keepAspectRatio(true);
        $imageResize->resize($width, $height);
        $imageResize->save($resizedImagePath);

        $resizedURL = $this->getBaseUrl() . $this->getCachePath($width, $height) . $layer;
        return $resizedURL;
    }

    /**
     * Retrieve base tmp path
     *
     * @return string
     */
    public function getBaseTmpPath()
    {
        return $this->scopeConfig->getValue('orange35_image_constructor/base_media_path/layer_tmp');
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Retrieve base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->scopeConfig->getValue('orange35_image_constructor/base_media_path/layer');
    }

    public function getImportPath()
    {
        return $this->scopeConfig->getValue('orange35_image_constructor/base_media_path/import');
    }

    /**
     * Retrieve base cache path
     *
     * @return string
     */
    public function getBaseCachePath()
    {
        return $this->scopeConfig->getValue('orange35_image_constructor/base_media_path/layer_cache');
    }

    /**
     * @param $width integer
     * @param $height integer
     * @return string
     */
    public function getCachePath($width, $height)
    {
        return $this->getBaseCachePath() . '/' . $width . 'x' . $height;
    }

    /**
     * Retrieve path
     *
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    public function getFilePath($path, $name)
    {
        return rtrim($path, '/') . '/' . ltrim($name, '/');
    }
}
