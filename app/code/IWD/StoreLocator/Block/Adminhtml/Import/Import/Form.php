<?php

namespace IWD\StoreLocator\Block\Adminhtml\Import\Import;

/**
 * Class Form
 * @package IWD\StoreLocator\Block\Adminhtml\Import\Import
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/importSave'),
                    'method' => 'post', 'enctype'=>'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
