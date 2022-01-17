<?php

namespace Orange35\Colorpickercustom\Plugin;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Catalog\Model\Product\Image;
use Magento\Framework\Filesystem;
use Orange35\Colorpickercustom\Helper\Image as ImageHelper;

class CacheCleaning
{
    private $coreFileStorageDatabase;
    private $mediaDirectory;
    private $imageHelper;

    public function __construct(Database $coreFileStorageDatabase, Filesystem $filesystem, ImageHelper $imageHelper)
    {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->imageHelper = $imageHelper;
    }

    //phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
    public function aroundClearCache(Image $subject, callable $proceed)
    {
        $dirs = [
            $this->imageHelper->getBaseCachePath(),
            $this->imageHelper->getBaseTmpPath(),
        ];

        foreach ($dirs as $dir) {
            //phpcs:ignore MEQP1.Performance.Loop.ModelLSD
            $this->mediaDirectory->delete($dir);
            $this->coreFileStorageDatabase->deleteFolder($this->mediaDirectory->getAbsolutePath($dir));
        }

        $proceed();
    }
}
