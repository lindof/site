<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Block\Adminhtml\Paymentrestrictions;
 
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
 
class Edit extends Container
{
   /**
    * Core registry
    *
    * @var \Magento\Framework\Registry
    */
    protected $_coreRegistry = null;
 
    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'pr_id';
        $this->_controller = 'adminhtml_paymentrestrictions';
        $this->_blockGroup = 'Cynoinfotech_PaymentRestrictions';
 
        parent::_construct();
 
        $this->buttonList->update('save', 'label', __('Save'));
                
        if ($this->_isAllowedAction('Cynoinfotech_PaymentRestrictions::delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Preferences'));
        } else {
            $this->buttonList->remove('delete');
        }
        
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
 
    /**
     * Retrieve text for header element depending on loaded faqs
     *
     * @return string
     */
    public function getHeaderText()
    {
        $producttabsRegistry = $this->_coreRegistry->registry('paymentrestrictions');
        if ($producttabsRegistry->getPrId()) {
            $name = $this->escapeHtml($producttabsRegistry->getName());
            return __("Edit Payment Restrictions's '%1'", $name);
        } else {
            return __("Add Payment Restrictions's");
        }
    }
 
    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('post_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'post_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'post_content');
                }
            };
        ";
 
        return parent::_prepareLayout();
    }
}
