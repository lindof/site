<?php

namespace Orange35\ImageConstructor\Model\Order\Item;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Image as MagentoImage;
use Magento\Framework\Image\Adapter\AbstractAdapter as AbstractImageAdapter;
use Magento\Sales\Model\Order\Item;
use Orange35\ImageConstructor\Model\Order\Media\ConfigInterface as MediaConfigInterface;

class Image extends \Magento\Framework\Model\AbstractModel
{
    private $keepTransparency = false;
    private $quality = 100;
    private $backgroundColor = [255, 255, 255];

    /** @var \Magento\Framework\Filesystem\Directory\WriteInterface */
    private $mediaDirectory;

    /** @var \Magento\Framework\Image\Factory */
    private $imageFactory;

    /** @var MagentoImage */
    private $processor;

    /** @var MediaConfigInterface  */
    private $mediaConfig;

    /** @var \Magento\Catalog\Model\Product\Media\Config  */
    private $productMediaConfig;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\Factory $imageFactory,
        MediaConfigInterface $mediaConfig,
        \Magento\Catalog\Model\Product\Media\Config $productMediaConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageFactory = $imageFactory;
        $this->mediaConfig = $mediaConfig;
        $this->productMediaConfig = $productMediaConfig;
    }

    public function create(Item $item)
    {
        $layers = $this->getItemLayers($item);
        $image = $this->createImageName($item, $layers);
        $imagePath = $this->mediaConfig->getBaseMediaPath() . $image;

        $productImageRelativePath = $this->productMediaConfig->getBaseMediaPath() . $item->getProduct()->getImage();
        $productImageAbsolutePath = $this->mediaDirectory->getAbsolutePath($productImageRelativePath);

        if ($this->mediaDirectory->isExist($imagePath)) {
            return $image;
        }
        if (!$this->mediaDirectory->copyFile($productImageAbsolutePath, $imagePath)) {
            throw new Exception('Can not create order item image');
        }

        $processor = $this->imageFactory->create($productImageAbsolutePath);
        $processor->keepTransparency($this->isKeepTransparency());
        $processor->backgroundColor($this->getBackgroundColor());
        $processor->setWatermarkImageOpacity($this->getQuality());
        $processor->setWatermarkHeight($processor->getOriginalHeight());
        $processor->setWatermarkWidth($processor->getOriginalWidth());
        $processor->setWatermarkImageOpacity(100);
        $processor->setWatermarkPosition(AbstractImageAdapter::POSITION_TOP_LEFT);

        foreach ($layers as $layer) {
            $this->verifyLayer($layer);
            $processor->watermark($layer['image']);
        }
        $orderImageAbsolutePath = $this->mediaDirectory->getAbsolutePath($imagePath);
        $processor->save($orderImageAbsolutePath);

        return $image;
    }

    private function createImageName(Item $item, array $layers = [])
    {
        $info = pathinfo($item->getProduct()->getImage());
        $ids = [];
        foreach ($layers as $layer) {
            $ids = array_merge($ids, explode(',', $layer['id']));
        }
        return '/' . $item->getOrder()->getRealOrderId()
            . '/' . $info['filename']
            . ($layers ? '-' . implode('-', $ids) : '')
            . '.' . $info['extension'];
    }

    private function getItemLayers(Item $item)
    {
        $layers = [];
        if (!($options = $item->getProductOptions()) || empty($options['options'])) {
            return $layers;
        }
        $options = $options['options'];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $item->getProduct();
        $layerPath = $this->mediaConfig->getLayerBasePath();
        foreach ($options as $optionData) {
            if (!($option = $product->getOptionById($optionData['option_id']))) {
                continue;
            }
            foreach (explode(',', $optionData['option_value']) as $valueId) {
                $value = $option->getValueById($valueId);
                if (($layerImage = $value->getData('layer'))) {
                    $layers[] = [
                        'id'    => $optionData['option_value'],
                        'image' => $this->mediaDirectory->getAbsolutePath($layerPath . $layerImage),
                    ];
                }
            }
        }
        return $layers;
    }

    public function verifyLayer(array $layer)
    {
        if (!is_array($layer)) {
            throw new Exception('Layers are in wrong format, array expected');
        }
        if (empty($layer['id'])) {
            throw new Exception('Layer are in wrong format, no layer id');
        }
        if (empty($layer['image'])) {
            throw new Exception('Layer are in wrong format, no layer image');
        }
        if (!file_exists($layer['image'])) {
            throw new Exception('Layer image does not exist: ' . $layer['image']);
        }
        return $this;
    }

    public function setBackgroundColor(array $rgbArray)
    {
        $this->_backgroundColor = $rgbArray;
        return $this;
    }

    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    public function isKeepTransparency()
    {
        return $this->keepTransparency;
    }

    public function setKeepTransparency($keepTransparency)
    {
        $this->keepTransparency = $keepTransparency;
        return $this;
    }

    public function getQuality()
    {
        return $this->quality;
    }

    public function setQuality($quality)
    {
        $this->quality = $quality;
        return $this;
    }

    public function resize($image, $width, $height)
    {
        $info = pathinfo($image);
        $thumbnail = $info['dirname'] . '/'. $info['filename'] . '_' . $width . 'x' . $height . '.' . $info['extension'];
        $thumbnailPath = $this->mediaConfig->getBaseMediaThumbnailPath() . $thumbnail;

        if ($this->mediaDirectory->isExist($thumbnail)) {
            return $thumbnail;
        }

        $imageAbsolutePath = $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getBaseMediaPath() . $image);
        $processor = $this->imageFactory->create($imageAbsolutePath);
        $processor->backgroundColor([255, 255, 255]);
        $processor->keepAspectRatio(true);
        $processor->quality(100);
        $processor->keepFrame(true);
        $processor->resize($width, $height);
        $destinationAbsolutePath = $this->mediaDirectory->getAbsolutePath($thumbnailPath);
        $processor->save($destinationAbsolutePath);

        return $thumbnail;
    }
}
