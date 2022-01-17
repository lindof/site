<?php

namespace Orange35\ImageConstructor\Model\Image;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File as FileIo;
use Magento\Framework\Image\Adapter\AbstractAdapter as AbstractImageAdapter;
use Magento\Framework\Image\Factory as ImageFactory;
use Orange35\ImageConstructor\Helper\ConfigHelper;

class Merger
{
    private $imageFactory;
    private $configHelper;
    private $mediaDirectory;
    private $keepTransparency = true;
    private $quality = 100;
    private $backgroundColor = [255, 255, 255];

    public function __construct(
        ImageFactory $imageFactory,
        ConfigHelper $configHelper,
        Filesystem $filesystem,
        FileIo $fileIo
    ) {
        $this->imageFactory = $imageFactory;
        $this->configHelper = $configHelper;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fileIo = $fileIo;
    }

    /**
     * @param string $productImage product image relative to pub/media/catalog/product
     * @param array $layers a list of layers in the following format:
     * [
     *     ['id' => 1, 'image' => 'a/b/abc.png'], // image path related to pub/media/catalog/layer
     *     ['id' => 2, 'image' => '1/0/100.png'], // image path related to pub/media/catalog/layer
     * ]
     * @param string $savePath save path related to pub/media
     * @return string merged image name relative to $savePath
     * @throws LocalizedException
     */
    public function merge($productImage, array $layers, $savePath)
    {
        if (empty($layers)) {
            throw new LocalizedException(__('No Layers found'));
        }
        if (empty($productImage)) {
            throw new LocalizedException(__('Image is undefined'));
        }

        $mergedImage = $this->createImageName($productImage, $layers);
        $mergedImageRelativePath = $savePath . $mergedImage;

        if ($this->mediaDirectory->isExist($mergedImageRelativePath)) {
            return $mergedImage;
        }

        $processor = $this->imageFactory->create($this->configHelper->getProductImageAbsolutePath($productImage));
        $processor->backgroundColor($this->getBackgroundColor());
        $processor->keepTransparency($this->isKeepTransparency());
        $processor->keepAspectRatio(true);
        $processor->quality(100);
        $processor->setWatermarkHeight($processor->getOriginalHeight());
        $processor->setWatermarkWidth($processor->getOriginalWidth());
        $processor->setWatermarkImageOpacity(100);
        $processor->setWatermarkPosition(AbstractImageAdapter::POSITION_TOP_LEFT);

        foreach ($layers as $layer) {
            $layer['image'] = $this->configHelper->getLayerAbsolutePath($layer['image']);
            $this->verifyLayer($layer);
            $processor->watermark($layer['image']);
        }
        $mergedImageAbsolutePath = $this->mediaDirectory->getAbsolutePath($mergedImageRelativePath);
        $processor->save($mergedImageAbsolutePath);

        return $mergedImage;
    }

    private function verifyLayer(array $layer)
    {
        if (!is_array($layer)) {
            throw new LocalizedException(__('Layers are in wrong format, array expected'));
        }
        if (empty($layer['id'])) {
            throw new LocalizedException(__('Layer are in wrong format, no layer id'));
        }
        if (empty($layer['image'])) {
            throw new LocalizedException(__('Layer are in wrong format, no layer image'));
        }
        if (!$this->mediaDirectory->isExist($layer['image'])) {
            throw new LocalizedException(__('Layer image does not exist: ' . $layer['image']));
        }
        return $this;
    }

    public function isKeepTransparency(): bool
    {
        return $this->keepTransparency;
    }

    public function setKeepTransparency(bool $keepTransparency)
    {
        $this->keepTransparency = $keepTransparency;
        return $this;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function setQuality(int $quality)
    {
        $this->quality = $quality;
        return $this;
    }

    public function getBackgroundColor(): array
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(array $backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    private function createImageName(string $image, array $layers)
    {
        $info = $this->fileIo->getPathInfo($image);
        $name = !empty($info['dirname']) ? $info['dirname'] : '';
        $name .= !empty($name) ? '/' : '';
        $name .= $info['filename'] . '_l' . implode('-', array_column($layers, 'id'));
        $name .= !empty($info['extension']) ? '.' . $info['extension'] : '';
        return '/' . ltrim($name, '/');
    }
}
