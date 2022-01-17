<?php

namespace Knowband\Spinandwin\Block\Adminhtml\Tab;

class EmailSettings extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    private $sp_helper;
    protected $wysiwyg;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Knowband\Spinandwin\Helper\Data $helper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwyg,
        array $data = []
    ) {
        $this->sp_helper = $helper;
        $this->wysiwyg = $wysiwyg;
        parent::__construct($context, $data);
    }

    
    public function getTabLabel()
    {
        return __('Email Settings');
    }

    public function getTabTitle()
    {
        return __('Email Settings');
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
    
    public function getCouponDisplayOptions()
    {
        return array(
            '1' => __('Only on wheel'),
            '2' => __('Email Only'),
            '3' => __('Email & Wheel'),
        );
    }
    public function getEmailTemplates()
    {
        return array(
            'fire' => __('Fire'),
            'aqua' => __('Aqua'),
            'wind' => __('Wind'),
            'void' => __('Void'),
            'earth' => __('Earth'),
        );
    }
    
    public function getStoreDetails()
    {
        $store_details = $this->sp_helper->getStoreIdDetails();
        if ($store_details[2] == 'websites' || $store_details[2] == 'groups') {
            $scopeId = $store_details[1];
        } else {
            $scopeId = $store_details[0];
        }
        return ["scope_id" => $scopeId, "scope" => $store_details[2]];
    }
    
    public function getVersion() {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $version = $productMetadata->getVersion();
        return $version;
    }
}
