<?php

namespace Knowband\Spinandwin\Block\Adminhtml\Tab;

class General extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    private $sp_helper;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Knowband\Spinandwin\Helper\Data $helper,
        array $data = []
    ) {
        $this->sp_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getTabLabel()
    {
        return __('General Settings');
    }

    public function getTabTitle()
    {
        return __('General Settings');
    }

    public function canShowTab()
    {
        return true;
    }
    
    public function getMediaUrl()
    {
        return $this->sp_helper->getMediaUrl();
    }

    public function isHidden()
    {
        return false;
    }

    public function getSettings($key = 'knowband/spinandwin/settings')
    {
        return $this->sp_helper->getSavedSettings($key);
    }
}
