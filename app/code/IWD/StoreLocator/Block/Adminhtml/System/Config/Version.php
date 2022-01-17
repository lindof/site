<?php

namespace IWD\StoreLocator\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Backend\Block\Template\Context;

/**
 * Class Version
 * @package IWD\StoreLocator\Block\Adminhtml\System\Config
 */
class Version extends Field
{
    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * {@inheritdoc}
     */
    public function __construct(Context $context, ComponentRegistrar $componentRegistrar, array $data = [])
    {
        $this->componentRegistrar = $componentRegistrar;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $nameSpace = explode("\\", __NAMESPACE__);
        $moduleName = $nameSpace[0] . '_' . $nameSpace[1];
        $configFile = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $moduleName)
            . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'module.xml';
        $xml = new \SimpleXMLElement(file_get_contents($configFile));
        return "<span style='margin-bottom:-8px; display:block;'>" . $xml->module[0]->attributes()->setup_version . "</span>";
    }
}