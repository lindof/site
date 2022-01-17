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
use Magento\Cms\Model\Wysiwyg\Config;

class DaysAndTime extends Generic implements TabInterface
{

    /**
     * @var \Cynoinfotech\PaymentRestrictions\Model\Source\Days
     */
        
    protected $days;
 
   /**
    * @param Context $context
    * @param Registry $registry
    * @param FormFactory $formFactory
    * @param Status $shippingrestrictStatus
    * @param array $data
    */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Cynoinfotech\PaymentRestrictions\Model\Source\Days $days,
        array $data = []
    ) {
        $this->days = $days;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('paymentrestrictions');
                
        if ($this->_isAllowedAction('Cynoinfotech_PaymentRestrictions::save')) {
            $isElementDisabled = 0;
        } else {
            $isElementDisabled = 1;
        }
 
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('paymentrestrictions_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Payment Restrictions Days And Time')]);
        
        if ($model->getId()) {
            $fieldset->addField('pr_id', 'hidden', ['name' => 'pr_id']);
        }
        
        $daysOptions = $this->days->toOptionArray();
        
        $fieldset->addField(
            'days',
            'multiselect',
            [
                'name'     => 'days[]',
                'label'    => __('Days of the week'),
                'title' => __('Days of the week'),
                'values' => $daysOptions,
                'disabled' => $isElementDisabled,
                'required' => true,
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
        return __("Days Info");
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __("Days Info");
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
