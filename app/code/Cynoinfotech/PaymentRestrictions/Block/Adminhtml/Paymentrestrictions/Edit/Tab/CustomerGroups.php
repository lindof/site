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

class CustomerGroups extends Generic implements TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $customerGroup;
 
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
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerCollection,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->customerGroup = $customerCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
     
    protected function _prepareForm()
    {
       /** @var $model \Cynoinfotech\paymentrestrictions\Model\paymentrestrictions */
        $model = $this->_coreRegistry->registry('paymentrestrictions');
                
        if ($this->_isAllowedAction('Cynoinfotech_PaymentRestrictions::save')) {
            $isElementDisabled = 0;
        } else {
            $isElementDisabled = 1;
        }
 
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('paymentrestrictions_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Apply In')]);
        if ($model->getId()) {
            $fieldset->addField('pr_id', 'hidden', ['name' => 'pr_id']);
        }
        
        /**
         * Check is single store mode
         */
         
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'stores',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'values' => $this->systemStore->getStoreValuesForForm(false, true),
                    'required' => true,
                    'note'   => 'Leave empty or select all to apply the rule to any',
                    'disabled' => $isElementDisabled,
                    
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        
        /*----------------------------------------------------------*/
        
        $fieldset = $form->addFieldset(
            'apply_for',
            [
                            'legend' => __('Apply For')
                        ]
        );
        
        $groupOptions = $this->customerGroup->toOptionArray();

        $fieldset->addField(
            'customer_group',
            'multiselect',
            [
                'name'     => 'customer_group[]',
                'label'    => __('Customer Groups'),
                'title' => __('Customer Group'),
                'values' => $groupOptions,
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
        
        /*----------------------------------------------------------*/
        
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
        return __("Apply In");
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __("Apply In");
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
