<?php


namespace Magesales\Converge\Ui\Data;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magesales\Converge\Gateway\Config\Config;

/**
 * Class ChangelogProvider.
 */
class ChangelogProvider
{
    const CHANGELOG_FILE_NAME = 'CHANGELOG.md';

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * @var string
     */
    private $modulePath;

    /**
     * ChangelogProvider constructor.
     * @param ReadFactory $readFactory
     * @param ComponentRegistrarInterface $componentRegistrar
     */
    public function __construct(
        ReadFactory $readFactory,
        ComponentRegistrarInterface $componentRegistrar
    ) {
        $this->readFactory = $readFactory;
        $this->modulePath = $componentRegistrar->getPath(
            ComponentRegistrar::MODULE,
            Config::MODULE_NAME
        );
    }

    /**
     * @return string
     */
    public function get()
    {
        $dir = $this->readFactory->create($this->modulePath);
        $content = $dir->readFile(self::CHANGELOG_FILE_NAME);

        return $content;
    }
}
