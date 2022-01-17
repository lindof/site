<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Block\Adminhtml\Paymentrestrictions\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

class Maintab extends Generic implements TabInterface
{
    /**
     * @var \Cynoinfotech\PaymentRestrictions\Model\Source\AllPaymentMethods
     */
    protected $apm;
    
   /**
    * @param Context $context
    * @param Registry $registry
    * @param FormFactory $formFactory
    * @param array $data
    */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Cynoinfotech\PaymentRestrictions\Model\Source\AllPaymentMethods $allPaymentMethods,
        array $data = []
    ) {
        $this->apm = $allPaymentMethods;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
       /** @var $model \Cynoinfotech\Shippingrestrict\Model\Shippingrestrict */
        $model = $this->_coreRegistry->registry('paymentrestrictions');
                
        if ($this->_isAllowedAction('Cynoinfotech_PaymentRestrictions::save')) {
            $isElementDisabled = 0;
        } else {
            $isElementDisabled = 1;
        }
 
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('paymentrestrictions_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Payment Restrictions Information')]);
        
        if ($model->getId()) {
            $fieldset->addField('pr_id', 'hidden', ['name' => 'pr_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Name'),
                'title'    => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
    
        $fieldset->addField(
            'status',
            'select',
            [
                'name'     => 'status',
                'label'    => __('Status'),
                'options'  => ["0" => "Disable","1" => "Enable"],
                'disabled' => $isElementDisabled,
                'note'     => 'Payment Restrictions status active/Inactive',
            ]
        );
        $paymentOptions = $this->apm->toOptionArray();
        $fieldset->addField(
            'methods',
            'multiselect',
            [
                'name'     => 'methods[]',
                'label'    => __('Methods'),
                'title' => __('Methods'),
                'values' => $paymentOptions,
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
            
         $fieldset->addField(
             'priority',
             'text',
             [
                'name'     => 'priority',
                'label'    => __('Priority'),
                'disabled' => $isElementDisabled,
                'note'     => 'Like 0 OR 1',
             ]
         );
        
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }
        
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __("Maintab Tabs Info");
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __("Maintab Tabs Info");
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
    
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
