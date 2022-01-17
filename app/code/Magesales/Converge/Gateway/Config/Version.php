<?php


namespace Magesales\Converge\Gateway\Config;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class Version
 */
class Version
{
    /**
     * Extension version
     */
    const EXTENSION_VERSION = 'Magesales Converge v%s';

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * Version constructor.
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList
    ) {
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return sprintf(self::EXTENSION_VERSION, $this->getModuleVersion());
    }

    /**
     * @return string
     */
    public function getProductVersion()
    {
        return sprintf(
            __('%s %s ver.%s'),
            $this->productMetadata->getName(),
            $this->productMetadata->getEdition(),
            $this->productMetadata->getVersion()
        );
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        $moduleInfo = $this->moduleList->getOne(Config::MODULE_NAME);
        return isset($moduleInfo['setup_version']) ? $moduleInfo['setup_version'] : '1.0.0';
    }
}
