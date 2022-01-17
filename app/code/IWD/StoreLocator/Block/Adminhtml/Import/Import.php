<?php

namespace IWD\StoreLocator\Block\Adminhtml\Import;

/**
 * Class Import
 * @package IWD\StoreLocator\Block\Adminhtml\Import
 */
class Import extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_blockGroup = 'IWD_StoreLocator';
        $this->_controller = 'adminhtml_item';

        parent::_construct();

        if ($this->isAllowedAction('IWD_StoreLocator::Item')) {
            $this->buttonList->update('save', 'label', __('Start Import'));
            $this->buttonList->remove('reset');
        } else {
            $this->buttonList->remove('save');
            $this->buttonList->remove('reset');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Import Information');
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
