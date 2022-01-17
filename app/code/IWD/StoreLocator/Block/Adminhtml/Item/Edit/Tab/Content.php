<?php

namespace IWD\StoreLocator\Block\Adminhtml\Item\Edit\Tab;

class Content extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('storelocator_store');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->isAllowedAction('IWD_StoreLocator::Item')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset(
            'content_fieldset',
            ['legend' => __('Store Coordinates'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'lat',
            'text',
            [
                'name' => 'lat',
                'label' => __('Latitude'),
                'title' => __('Latitude'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'lng',
            'text',
            [
                'name' => 'lng',
                'label' => __('Longitude'),
                'title' => __('Longitude'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $form->setValues($model->getData());
        
        $fieldset->addField(
            'get_location',
            'button',
            [
                'label' => '',
                'title' => __('Get Location'),
                'name' => 'get_location',
                'value' => 'Get Location',
                'class' => 'action-default'
            ]
        );
        
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Store Coordinates');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Store Coordinates');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    private function isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
