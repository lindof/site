<?php

namespace Drc\InstagramPics\Block\Adminhtml\System\Config\Frontend;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field as FormField;
use Drc\InstagramPics\Helper\Config as helperInstagram;
use Magento\Backend\Block\Template\Context;

class Status extends FormField
{
    protected $_instagramHelper;
    protected $_label;
    
    public function __construct(
        helperInstagram $instagramHelper,
        \Magento\Framework\Data\Form\Element\Label $label,
        Context $context
    ) {
        $this->_instagramHelper = $instagramHelper;
        $this->_label = $label;
        parent::__construct($context);
    }
    
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setBold(true);

        if ($this->_instagramHelper->isConnected()) {
            $element->setValue(__('Connected to Instagram'));
            $element->addClass('instagram_status')->addClass('success');
        } else {
            $element->setValue(__('Not connected to Instagram'));
            $element->addClass('instagram_status')->addClass('error');
        }
        return '<p id="'. $element->getHtmlId() .
               '_label" ' . $element->serialize($element->getHtmlAttributes()) . '>' .
               parent::_getElementHtml($element) .
               '</p>
			   <input id="' . $element->getHtmlId() . '" 
			   value="' . (int)$this->_instagramHelper->isConnected() . '" 
			   type="hidden"/>';
    }

    protected function _prepareLayout()
    {
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->addItem('skin_css', 'drc/instagram/css/styles.css');
        }
        return parent::_prepareLayout();
    }
}
