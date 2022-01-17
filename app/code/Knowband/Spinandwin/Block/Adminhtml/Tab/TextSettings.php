<?php

namespace Knowband\Spinandwin\Block\Adminhtml\Tab;

class TextSettings extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        return __('Text Settings');
    }

    public function getTabTitle()
    {
        return __('Text Settings');
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
    
    public function getDefaultSettings()
    {
        $text_settings = array(
            'title_text_1' => __('Special bonus unlocked'),
            'title_text_2' => __('You have a chance to win a nice big fat discount. Are you ready?'),
            'title_text_3' => __('You can spin the wheel only once. If you win, you can claim your coupon for 1 day only!')
        );
        return $text_settings;
    }
}
