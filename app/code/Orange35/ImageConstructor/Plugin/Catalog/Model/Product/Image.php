<?php

namespace Orange35\ImageConstructor\Plugin\Catalog\Model\Product;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Orange35\ImageConstructor\Helper\ConfigHelper;
use Orange35\ImageConstructor\Helper\Image as ImageHelper;

class Image
{
    private $coreFileStorageDatabase;
    private $helper;
    private $mediaDirectory;
    private $configHelper;

    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        ImageHelper $helper,
        ConfigHelper $configHelper
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->helper = $helper;
        $this->configHelper = $configHelper;
    }

    public function aroundClearCache(\Magento\Catalog\Model\Product\Image $subject, callable $proceed)
    {
        $dirsToClear = [
            $this->helper->getBaseCachePath(),
            $this->helper->getBaseTmpPath(),
            $this->configHelper->getMergedImagePath(),
        ];

        foreach ($dirsToClear as $dir) {
            $this->mediaDirectory->delete($dir);
            $this->coreFileStorageDatabase->deleteFolder($this->mediaDirectory->getAbsolutePath($dir));
        }
        $proceed();
    }
}
