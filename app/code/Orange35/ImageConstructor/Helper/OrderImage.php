<?php

namespace Orange35\ImageConstructor\Helper;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Sales\Model\Order\Item;
use Orange35\ImageConstructor\Model\Order\Item\Image as ImageModel;
use Orange35\ImageConstructor\Model\Order\Media\ConfigInterface as MediaConfigInterface;

class OrderImage extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /** @var MediaConfigInterface */
    private $mediaConfig;

    /** @var Item */
    private $item;

    /** @var ImageModel */
    protected $model;

    public function __construct(
        Context $context,
        Filesystem $filesystem,
        ImageModel $model,
        MediaConfigInterface $mediaConfig
    ) {
        parent::__construct($context);
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->mediaConfig = $mediaConfig;
        $this->model = $model;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function setItem(Item $item)
    {
        $this->item = $item;
        return $this;
    }

    public function create()
    {
        try {
            return $this->model->create($this->getItem());
        } catch (Exception $e) {
            // failed to create image, return null;
        }
        return null;
    }

    public function getUrl()
    {
        return $this->mediaConfig->getBaseMediaUrl() . $this->getItem()->getData('image');
    }

    public function getThumbnailUrl($width, $height)
    {
        if (!($thumbnail = $this->model->resize($this->getItem()->getData('image'), $width, $height))) {
            return null;
        }
        return $this->mediaConfig->getBaseMediaThumbnailUrl() . $thumbnail;
    }

    public function getThumbnail()
    {
        if (!($image = $this->getItem()->getData('image'))) {
            return [];
        }
        $width = $this->scopeConfig->getValue('orange35_image_constructor/order/image/width');
        $height = $this->scopeConfig->getValue('orange35_image_constructor/order/image/height');
        if (!($thumbnailUrl = $this->getThumbnailUrl($width, $height))) {
            return [];
        }
        return [
            'image_url'        => $this->getUrl(),
            'thumbnail_url'    => $thumbnailUrl,
            'thumbnail_width'  => $width,
            'thumbnail_height' => $height,
        ];
    }

}
