<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Block\Adminhtml\Paymentrestrictions\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('paymentrestrictions_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Information'));
    }
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'restrictions_info',
            [
                'label' => __('Restrictions'),
                'title' => __('Restrictions'),
                'content' => $this->getLayout()->createBlock(
                    'Cynoinfotech\PaymentRestrictions\Block\Adminhtml\Paymentrestrictions\Edit\Tab\Maintab'
                )->toHtml(),
                'active' => true
            ]
        );
        
        $this->addTab(
            'paymentrestrictions_conditions',
            [
                'label' => __('Conditions'),
                'title' => __('Conditions'),
                'content' => $this->getLayout()->createBlock(
                    'Cynoinfotech\PaymentRestrictions\Block\Adminhtml\Paymentrestrictions\Edit\Tab\Conditions'
                )->toHtml()
            ]
        );
        
        $this->addTab(
            'days_and_time',
            [
                'label' => __('Days Info'),
                'title' => __('Days Info'),
                'content' => $this->getLayout()->createBlock(
                    'Cynoinfotech\PaymentRestrictions\Block\Adminhtml\Paymentrestrictions\Edit\Tab\DaysAndTime'
                )->toHtml()
            ]
        );
        
        $this->addTab(
            'paymentrestrictions_stores_and_customer_groups',
            [
                'label' => __('Stores & Customer Groups'),
                'title' => __('Stores & Customer Groups'),
                'content' => $this->getLayout()->createBlock(
                    'Cynoinfotech\PaymentRestrictions\Block\Adminhtml\Paymentrestrictions\Edit\Tab\CustomerGroups'
                )->toHtml()
            ]
        );
        return parent::_beforeToHtml();
    }
}
