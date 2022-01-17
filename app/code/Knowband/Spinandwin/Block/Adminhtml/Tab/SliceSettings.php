<?php

namespace Knowband\Spinandwin\Block\Adminhtml\Tab;

class SliceSettings extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        return __('Slice Settings');
    }

    public function getTabTitle()
    {
        return __('Slice Settings');
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
        $slices = array(
            'slice_1' => array(
                'coupon_type' => 'P',
                'label' => __('10% OFF'),
                'coupon_value' => '10',
                'gravity' => '20',
            ),
            'slice_2' => array(
                'coupon_type' => 'F',
                'label' => __('Not Lucky Today'),
                'coupon_value' => '0',
                'gravity' => '5',
            ),
            'slice_3' => array(
                'coupon_type' => 'P',
                'label' => __('35% OFF'),
                'coupon_value' => '35',
                'gravity' => '0',
            ),
            'slice_4' => array(
                'coupon_type' => 'F',
                'label' => __('Opps! Sorry'),
                'coupon_value' => '0',
                'gravity' => '5',
            ),
            'slice_5' => array(
                'coupon_type' => 'P',
                'label' => __('15% OFF'),
                'coupon_value' => '15',
                'gravity' => '10',
            ),
            'slice_6' => array(
                'coupon_type' => 'F',
                'label' => __('Better Luck Next Time'),
                'coupon_value' => '0',
                'gravity' => '10',
            ),
            'slice_7' => array(
                'coupon_type' => 'P',
                'label' => __('50% OFF'),
                'coupon_value' => '50',
                'gravity' => '0',
            ),
            'slice_8' => array(
                'coupon_type' => 'F',
                'label' => __('Try Next Time'),
                'coupon_value' => '0',
                'gravity' => '10',
            ),
            'slice_9' => array(
                'coupon_type' => 'P',
                'label' => __('12% OFF'),
                'coupon_value' => '12',
                'gravity' => '10',
            ),
            'slice_10' => array(
                'coupon_type' => 'F',
                'label' => __('Come Again'),
                'coupon_value' => '0',
                'gravity' => '10',
            ),
            'slice_11' => array(
                'coupon_type' => 'P',
                'label' => __('5% OFF'),
                'coupon_value' => '5',
                'gravity' => '10',
            ),
            'slice_12' => array(
                'coupon_type' => 'F',
                'label' => __('Try Again'),
                'coupon_value' => '0',
                'gravity' => '10',
            )
        );
        return $slices;
    }
}
