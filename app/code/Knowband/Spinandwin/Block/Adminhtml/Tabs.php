<?php

namespace Knowband\Spinandwin\Block\Adminhtml;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    private $coreRegistry = null;
    private $storeManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $jsonEncoder, $authSession, $data);
        $this->coreRegistry = $registry;
        $this->storeManager = $context->getStoreManager();
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('spinandwin_view_tabs');
        $this->setDestElementId('spinandwin_view');
    }
}
