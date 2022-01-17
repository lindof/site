<?php

namespace Drc\InstagramPics\Block\Adminhtml\System\Config\Frontend;

use Magento\Backend\Block\Template\Context;
use Drc\InstagramPics\Helper\Config as helperInstagram;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\View\LayoutInterface;

class Oauth extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_instagramHelper;
    protected $_blockFactory;
    protected $_layout;

    public function __construct(
        helperInstagram $instagramHelper,
        BlockFactory $blockFactory,
        LayoutInterface $layout,
        Context $context
    ) {
        $this->_instagramHelper = $instagramHelper;
        $this->_blockFactory = $blockFactory;
        $this->_layout = $layout;
        parent::__construct($context);
    }
    
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setScope(false);
        $element->setCanUseWebsiteValue(false);
        $element->setCanUseDefaultValue(false);
        return parent::render($element);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!$this->_instagramHelper->isConnected()) {
            $button = $this->_layout->createBlock(
                'Drc\InstagramPics\Block\Adminhtml\System\Config\Frontend\Oauth\Connect',
                'instagrampics_oauth'
            )
            ->setTemplate('Drc_InstagramPics::drc/instagram/system/config/oauth/connect.phtml');
        } else {
            $button = $this->_layout->createBlock(
                'Drc\InstagramPics\Block\Adminhtml\System\Config\Frontend\Oauth\Disconnect',
                'instagrampics_oauth'
            )
            ->setTemplate('Drc_InstagramPics::drc/instagram/system/config/oauth/disconnect.phtml');
        }

        $button->setContainerId($element->getContainer()->getHtmlId());

        return $button->toHtml();
    }
}
