<?php

namespace IWD\StoreLocator\Block\Adminhtml\Item;

/**
 * Class Edit
 * @package IWD\StoreLocator\Block\Adminhtml\Item
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    private $config;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \IWD\StoreLocator\Helper\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \IWD\StoreLocator\Helper\Config $config,
        array $data = []
    )
    {
        $this->coreRegistry = $registry;
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'item_id';
        $this->_blockGroup = 'IWD_StoreLocator';
        $this->_controller = 'adminhtml_item';

        parent::_construct();

        if ($this->isAllowedAction('IWD_StoreLocator::Item')) {
            $this->buttonList->update('save', 'label', __('Save Store'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->isAllowedAction('IWD_StoreLocator::Item')) {
            $this->buttonList->update('delete', 'label', __('Delete Store'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->coreRegistry->registry('storelocator_store')->getId()) {
            $storeName = $this->escapeHtml($this->coreRegistry->registry('storelocator_store')->getName());
            return __("Edit Store '%1'", $storeName);
        } else {
            return __('New Store');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    private function isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('storelocator/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if ($this->config->getApiType() === 'google') {
            $key = $this->config->getGoogleServerApiKey();
            $this->_formScripts[] = "require(['jquery','IWD_StoreLocator/js/gm'],function($, StoreLocator){
            var locator = new StoreLocator();
            locator.init('$key');
        });";
        } elseif ($this->config->getApiType() === 'here') {
            $appId = $this->config->getHereAppId();
            $appCode = $this->config->getHereAppCode();
            if (isset($appId) && isset($appCode)) {
                $this->_formScripts[] = "require(['jquery','IWD_StoreLocator/js/here'],function($, StoreLocator){
                    var locator = new StoreLocator();
                    locator.init('$appId', '$appCode');
                });";
            }
        }
        return parent::_prepareLayout();
    }
}
