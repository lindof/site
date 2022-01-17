<?php

namespace IWD\StoreLocator\Block\Adminhtml\Item\Edit\Tab;

use Magento\Framework\Convert\ConvertArray;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class Main
 * @package IWD\StoreLocator\Block\Adminhtml\Item\Edit\Tab
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * Main constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Customer\Model\Metadata\FormFactory $customerFormFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\Metadata\FormFactory $customerFormFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_customerFormFactory = $customerFormFactory;
        $this->directoryHelper = $directoryHelper;
        $this->_jsonEncoder = $jsonEncoder;
        $this->dataObjectProcessor = $dataObjectProcessor;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('storelocator_store');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('IWD_StoreLocator::Item')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $this->_form = $this->_formFactory->create();

        $this->_form->setHtmlIdPrefix('page_');

        $mainFieldset = $this->_form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Store Information')
            ]
        );

        if ($model->getId()) {
            $mainFieldset->addField(
                'item_id',
                'hidden',
                [
                    'name' => 'item_id'
                ]
            );
        }

        $mainFieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        
        $mainFieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $mainFieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Store Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $note = 'Specify a value greater than 0 to control the order this location is displayed in the search results.';
        $mainFieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'note' => $note
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $mainFieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $mainFieldset->addField(
                'store_id',
                'hidden',
                [
                    'name' => 'stores[]',
                    'value' => $this->_storeManager->getStore(true)->getId()
                ]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $addressFieldset = $this->_form->addFieldset(
            'addresss_fieldset',
            [
                'legend' => __('Store Address'),
                'class' => 'fieldset-wide',
                'disabled' => $isElementDisabled
            ]
        );
        
        $addressFieldset->addField(
            'street',
            'text',
            [
                'name' => 'street',
                'label' => __('Street'),
                'title' => __('Street'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        
        $addressFieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $addressForm = $this->_customerFormFactory->create('customer_address', 'adminhtml_customer_address');
        $attributes = $addressForm->getAttributes();
        $this->_addAttributesToForm($attributes, $addressFieldset);

        $regionElement = $this->_form->getElement('region_id');
        if ($regionElement) {
            $regionElement->setNoDisplay(true);
        }

        $this->_form->setValues($model->getData());

        $countryIdElement = $this->_form->getElement('country_id');
        if ($countryIdElement->getValue()) {
            $countryId = $countryIdElement->getValue();

            $countryIdElement->setValue(null);
            foreach ($countryIdElement->getValues() as $country) {
                if ($country['value'] == $countryId) {
                    $countryIdElement->setValue($countryId);
                }
            }
        }
        
        if ($countryIdElement->getValue() === null) {
            $countryIdElement->setValue(
                $this->directoryHelper->getDefaultCountry($this->getStore())
            );
        }

        $addressFieldset->addField(
            'postal_code',
            'text',
            [
                'name' => 'postal_code',
                'label' => __('Zip'),
                'title' => __('Zip'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        
        $addressFieldset->addField(
            'phone',
            'text',
            [
                'name' => 'phone',
                'label' => __('Phone Number'),
                'title' => __('Phone Number'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        
        $addressFieldset->addField(
            'website',
            'text',
            [
                'name' => 'website',
                'label' => __('Website'),
                'title' => __('Website'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        
        $imangeFieldset = $this->_form->addFieldset('image_fieldset', ['legend' => __('Image & Icon')]);

        $imangeFieldset->addType('image', '\IWD\StoreLocator\Block\Adminhtml\Item\Helper\Image');
        
        $imangeFieldset->addField(
            'icon',
            'image',
            [
                'name' => 'icon',
                'label' => __('Icon'),
                'title' => __('Icon'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        
        $imangeFieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $this->_form->setValues($model->getData());
        $this->setForm($this->_form);

        return parent::_prepareForm();
    }

    /**
     * @return array
     */
    protected function _getAdditionalFormElementTypes()
    {
        return [
            'file' => 'Magento\Customer\Block\Adminhtml\Form\Element\File',
            'image' => 'Magento\Customer\Block\Adminhtml\Form\Element\Image',
            'boolean' => 'Magento\Customer\Block\Adminhtml\Form\Element\Boolean'
        ];
    }
    
    /**
     * @return array
     */
    protected function _getAdditionalFormElementRenderers()
    {
        return [
            'region' => $this->getLayout()
                ->createBlock('Magento\Customer\Block\Adminhtml\Edit\Renderer\Region')
        ];
    }
    
    protected function _addAttributesToForm($attributes, \Magento\Framework\Data\Form\AbstractForm $form)
    {
        // add additional form types
        $types = $this->_getAdditionalFormElementTypes();
        foreach ($types as $type => $className) {
            $form->addType($type, $className);
        }
        $elementRenderers = $this->_getAdditionalFormElementRenderers();
    
        $list = ['country_id', 'region', 'region_id' ];
        
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if (!in_array($code, $list)) {
                continue;
            }
            $inputType = $attribute->getFrontendInput();
    
            if ($inputType) {
                $element = $form->addField(
                    $attribute->getAttributeCode(),
                    $inputType,
                    [
                        'name' => $attribute->getAttributeCode(),
                        'label' => __($attribute->getStoreLabel()),
                        'class' => $attribute->getFrontendClass(),
                        'required' => $attribute->isRequired()
                    ]
                );
                
                $element->setEntityAttribute($attribute);
                if ($element->getId() == 'region_id') {
                    $element->setNoDisplay(true);
                }

                if (!empty($elementRenderers[$attribute->getAttributeCode()])) {
                    $element->setRenderer($elementRenderers[$attribute->getAttributeCode()]);
                }
    
                if ($inputType == 'multiselect' || $inputType == 'select') {
                    $options = [];
                    foreach ($attribute->getOptions() as $optionData) {
                        $outputDataArra = $this->dataObjectProcessor->buildOutputDataArray(
                            $optionData,
                            '\Magento\Customer\Api\Data\OptionInterface'
                        );
                        $options[] = ConvertArray::toFlatArray($outputDataArra);
                    }
                    $element->setValues($options);
                } elseif ($inputType == 'date') {
                    $format = $this->_localeDate
                        ->getDateFormat(\IntlDateFormatter::SHORT);
                    $element->setDateFormat($format);
                }
            }
        }
    
        return $this;
    }
    
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Store Information');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Store Information');
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

    /**
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
