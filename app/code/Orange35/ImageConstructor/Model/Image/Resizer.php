<?php

namespace Orange35\ImageConstructor\Model\Image;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File as FileIo;
use Magento\Framework\Image\Factory as ImageFactory;
use Magento\Framework\View\ConfigInterface;
use Orange35\ImageConstructor\Helper\ConfigHelper;

class Resizer
{
    private $imageFactory;
    private $viewConfig;
    private $configHelper;
    private $mediaDirectory;
    private $fileIo;
    private $imageHelper;

    public function __construct(
        ImageFactory $imageFactory,
        ConfigInterface $viewConfig,
        ConfigHelper $configHelper,
        FileIo $fileIo,
        Filesystem $filesystem,
        ImageHelper $imageHelper
    ) {
        $this->imageFactory = $imageFactory;
        $this->viewConfig = $viewConfig;
        $this->configHelper = $configHelper;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fileIo = $fileIo;
        $this->imageHelper = $imageHelper;
    }

    /**
     * Resize image in pub/media directory
     * @param string $image image name related to media directory (pub/media)
     * @param string $imageId an imageId in the view.xml theme file
     * @param array $attributes width, height and frame attributes to override
     * @return string
     * @throws LocalizedException
     */
    public function resize(string $image, $imageId, array $attributes = [])
    {
        $themeAttribs = $this->viewConfig->getViewConfig()->getMediaAttributes(
            'Magento_Catalog',
            Image::MEDIA_TYPE_CONFIG_NODE,
            $imageId
        );
        $attributes = array_merge($themeAttribs, $attributes);
        $absoluteImagePath = $this->mediaDirectory->getAbsolutePath($image);

        $width = empty($attributes['width']) ? 0 : $attributes['width'];
        $height = empty($attributes['height']) ? 0 : $attributes['height'];
        $frame = empty($attributes['frame']) || 'false' !== $attributes['frame'];

        if (empty($width) || empty($height)) {
            if (false === ($size = getimagesize($absoluteImagePath))) {
                throw new LocalizedException(__('Can not determine image size: ' . $absoluteImagePath));
            }
            $width = $size[0];
            $height = $size[1];
        }

        $thumbnail = $this->getThumbnailName($image, $width, $height, $frame);
        $absoluteThumbnailPath = $this->mediaDirectory->getAbsolutePath($thumbnail);

        if ($this->mediaDirectory->isExist($thumbnail)) {
            return $thumbnail;
        }

        $processor = $this->imageFactory->create($absoluteImagePath);
        $processor->backgroundColor([255, 255, 255]);
        $processor->keepAspectRatio(true);
        $processor->quality(100);
        $processor->keepFrame(empty($attributes['frame']));
        $processor->resize($width, $height);
        $processor->save($absoluteThumbnailPath);

        return $thumbnail;
    }

    public function getThumbnailUrl(string $image, $imageId, array $attributes = [])
    {
        $image = $this->resize($image, $imageId, $attributes);
        $baseUrl = $this->configHelper->getMediaBaseUrl();
        return rtrim($baseUrl, '/') . $image;
    }

    private function getThumbnailName(string $image, $width, $height, $frame)
    {
        $info = $this->fileIo->getPathInfo($image);
        $name = !empty($info['dirname']) ? $info['dirname'] : '';
        $name .= !empty($name) ? '/' : '';
        $name .= $info['filename'];
        $name .= '_' . $width . 'x' . $height . ($frame ? 'f' : '');
        $name .= !empty($info['extension']) ? '.' . $info['extension'] : '';
        return '/' . ltrim($name, '/');
    }
}
