<?php

namespace Knowband\Spinandwin\Block\Adminhtml\Tab;

class EmailMarketing extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        return __('Email Marketing');
    }

    public function getTabTitle()
    {
        return __('Email Marketing');
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
    
//    public function getLists()
//    {
//        return $this->sp_helper->constantContactGetLists('cud9wf99hk3kujgwxpb3dukt', 'cud9wf99hk3kujgwxpb3dukt');
//    }
}
