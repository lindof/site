<?php
/**
 * @category Cminds
 * @package  Marketplace
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Block\Adminhtml\Customer\Edit\Tab\Tabs\Supplierprofile\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Cminds\Marketplace\Helper\Data;
use Cminds\Marketplace\Model\Fields;

/**
 * Class Form
 * @package Cminds\Marketplace\Block\Adminhtml\Customer\Edit\Tab\Tabs\Supplierprofile\Edit
 */
class Form extends Generic
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var YesnoFactory
     */
    protected $_yesnoFactory;

    /**
     * @var Fields
     */
    protected $_fields;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * Form constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param YesnoFactory $yesnoFactory
     * @param Data $helper
     * @param Fields $fields
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        YesnoFactory $yesnoFactory,
        Data $helper,
        Fields $fields
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory
        );

        $this->_registry = $registry;
        $this->_yesnoFactory = $yesnoFactory;
        $this->_helper = $helper;
        $this->_fields = $fields;
    }

    /**
     * Pseudo construct
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('marketplace/customer/tab/view/profile.phtml');
    }

    /**
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'method' => 'post',
                    'enctype'=>'multipart/form-data',
                ],
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $changedDataExists = false;
        $customer = $this->_registry->registry('current_customer');
        $customFieldsValues = $this->getCustomFieldsValues(true, true);

        if( $customer->getData('supplier_name_new') 
            || $customer->getData('supplier_description_new') 
            || count( $customFieldsValues ) 
        ) {
            $changedDataExists = true;
        }

        $fieldset = $form->addFieldset(
            'customer_profile_data_new',
            []
        );

        $supplierNameNew = '';
        $supplierDescriptionNew = '';
        if( $changedDataExists ) {
            $supplierNameNew = $customer->getData('supplier_name_new') ? 
                $customer->getData('supplier_name_new') : $customer->getData('supplier_name');
            $supplierDescriptionNew = $customer->getData('supplier_description_new') ? 
                $customer->getData('supplier_description_new') : $customer->getData('supplier_description');
        }

        $fieldset->addField(
            'supplier_profile_name_new',
            'text',
            [
                'label' => __('Name'),
                'name' => 'supplier_name_new',
                'data-form-part' => "customer_form",
                'value' => $supplierNameNew,
            ]
        );
        $fieldset->addField(
            'supplier_profile_description_new',
            'textarea',
            [
                'label' => __('Description'),
                'name' => 'supplier_description_new',
                'value' => $supplierDescriptionNew,
                'wysiwyg' => true,
                'data-form-part' => "customer_form",
            ]
        );

        $customFieldsCollection = $this->_fields->getCollection();
        $customFieldsValues = $this->getNewCustomFieldsValues(true);
        foreach ($customFieldsCollection AS $customField) {
            $fieldConfig['label'] = __($customField->getLabel());
            $fieldConfig['data-form-part'] = "customer_form";
            $fieldConfig['name'] = $customField->getName() . '_new';
            $fieldConfig['value'] = $this
                ->_findValue(
                    $customField->getName(),
                    $customFieldsValues
                );

            if ($customField->getType() == 'textarea'
                && $customField->getWysiwyg()
            ) {
                $fieldConfig['wysiwyg'] = true;
            }

            if ($customField->getType() == 'date') {
                $fieldConfig['date_format'] = 'M/d/Y';
                $fieldConfig['time_format'] = '';
            }

            $fieldset->addField(
                $customField->getName() . '_new',
                $customField->getType(),
                $fieldConfig
            );
        }

        return parent::_prepareForm();
    }

    /**
     * @param $name
     * @param $data
     * @return bool
     */
    private function _findValue($name, $data)
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data AS $value) {
            if ($value['name'] == $name) {
                return $value['value'];
            }
        }

        return false;
    }

    /**
     * @param $param
     * @return mixed
     */
    public function getRegistry($param)
    {
        return $this->_registry->registry($param);
    }

    /**
     * @return Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @param bool $skipSystem
     * @param bool $new
     * @return array
     */
    public function getCustomFieldsValues($skipSystem = false, $new = false)
    {
        $customer = $this->getRegistry('current_customer');
        $ret = [];

        if( $new ) {
            if (!$customer->getNewCustomFieldsValues()) {
                return $ret;
            }
            $dbValues = unserialize($customer->getNewCustomFieldsValues());
        } else {
            if (!$customer->getCustomFieldsValues()) {
                return $ret;
            }
            $dbValues = unserialize($customer->getCustomFieldsValues());
        }

        if (!$dbValues) {
            return $ret;
        }

        foreach ($dbValues AS $value) {
            $v = $this->_fields->load($value['name'], 'name');
            if ($skipSystem) {
                if ($v->getData('is_system')) {
                    continue;
                }
            }
            if (isset($v)) {
                $ret[] = $value;
            }
        }

        return $ret;
    }

    /**
     * @param bool $skipSystem
     * @return array
     */
    public function getNewCustomFieldsValues($skipSystem = false)
    {
        $customer = $this->getRegistry('current_customer');

        $ret = [];

        if (!$customer->getNewCustomFieldsValues()) {
            return $ret;
        }

        $dbValues = unserialize($customer->getNewCustomFieldsValues());

        if (!$customer->getNewCustomFieldsValues()) {
            return $ret;
        }

        foreach ($dbValues AS $value) {
            $v = $this->_fields->load($value['name'], 'name');
            if ($skipSystem) {
                if ($v->getData('is_system')) {
                    continue;
                }
            }
            if (isset($v)) {
                $ret[] = $value;
            }
        }

        return $ret;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getFieldLabel($name)
    {
        $label = $this->_fields->load($name, 'name')->getData('label');

        return $label;
    }

    /**
     * @return Data
     */
    public function getMarketplaceHelper()
    {
        return $this->_helper;
    }
}
