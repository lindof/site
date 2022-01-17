<?php

namespace Knowband\Spinandwin\Block\Adminhtml\Tab;

class Statistics extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        return __('Statistics');
    }

    public function getTabTitle()
    {
        return __('Statistics');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getSettings($key)
    {
        return $this->sp_helper->getSavedSettings($key);
    }
    
    public function getDevicePieChartsData()
    {
        return $this->sp_helper->getDevicePieChartsData();
    }
    
    public function getCountStatsData()
    {
        return $this->sp_helper->getCountStatsData();
    }
    
    public function getCountryPieChartsData()
    {
        return $this->sp_helper->getCountryPieChartsData();
    }
    
    public function getMediaUrl()
    {
        return $this->sp_helper->getMediaUrl();
    }
    
    
}
