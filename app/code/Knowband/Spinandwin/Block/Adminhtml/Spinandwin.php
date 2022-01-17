<?php
namespace Knowband\Spinandwin\Block\Adminhtml;

class Spinandwin extends \Magento\Backend\Block\Template
{
    const DEFAULT_SECTION_BLOCK = 'Magento\Config\Block\System\Config\Form';

    private $storeManager;
    private $urlInterface;
    private $request;
    private $scopeConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $context->getStoreManager();
        $this->urlInterface = $context->getUrlBuilder();
        $this->scopeConfig = $context->getScopeConfig();
        $this->request = $request;
    }

    protected function _construct()
    {
        $this->_controller = 'adminhtml_spinandwin';
        $this->_blockGroup = 'Knowband_Spinandwin';
        parent::_construct();
    }

    protected function _prepareLayout()
    {
        $this->_formBlockName = self::DEFAULT_SECTION_BLOCK;
        $this->getToolbar()->addChild(
            'save_button', 'Magento\Backend\Block\Widget\Button', [
                'id' => 'save-spinandwin',
                'label' => __('Save Settings'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#spinandwin_view']],
                ]
            ]
        );
        $block = $this->getLayout()->createBlock($this->_formBlockName);
        $this->setChild('form', $block);
        return parent::_prepareLayout();
    }
    
}
