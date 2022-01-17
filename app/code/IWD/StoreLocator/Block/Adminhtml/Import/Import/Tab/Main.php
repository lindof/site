<?php

namespace IWD\StoreLocator\Block\Adminhtml\Import\Import\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class Main
 * @package IWD\StoreLocator\Block\Adminhtml\Import\Import\Tab
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Framework\File\Size
     */
    private $fileSize;

    /**
     * Main constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\File\Size $fileSize
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\File\Size $fileSize,
        array $data
    ) {
        $this->fileSize = $fileSize;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->isAllowedAction('IWD_StoreLocator::Item')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $this->_form = $this->_formFactory->create();

        $this->_form->setHtmlIdPrefix('page_');

        $imangeFieldset = $this->_form->addFieldset('file_fieldset', ['legend' => __('File to Import')]);

        $getMaxUploadSize = $this->fileSize->getMaxFileSizeInMb();
        $exampleLink = 'http://demo.iwdextensions.com/store-locator/import_example.csv';

        $imangeFieldset->addField(
            'import',
            'file',
            [
                'name' => 'import_file',
                'label' => __('Select File to Import'),
                'title' => __('Select File to Import'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'note' => "<br/>Max size of uploadable file must not exceed {$getMaxUploadSize}Mb<br/><a href='{$exampleLink}'>Download an example of import file</a>"
            ]
        );
        
        $this->setForm($this->_form);

        return parent::_prepareForm();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Import Information');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Import Information');
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
    private function isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
