<?php

namespace Drc\InstagramPics\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\Action;
use Drc\InstagramPics\Helper\Config;
use Drc\InstagramPics\Model\Instagram\Api;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Action\Context;

class Disconnect extends Action
{
    protected $_api = null;
    protected $_configHelper;
    protected $_adminSession;
    protected $_messageManager;
    protected $_objectManager;

    public function __construct(
        Api $instaApi,
        Config $configHelper,
        Session $adminSession,
        ManagerInterface $messageManager,
        Context $context
    ) {
        $this->_api            = $instaApi;
        $this->_configHelper   = $configHelper;
        $this->_adminSession   = $adminSession;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $this->_configHelper->disconnect();
        $this->_messageManager->addSuccess('Instagram disconnected is successful.');
        return $this->_redirect('adminhtml/system_config/edit', ['section' => 'instagrampics']);
    }

    protected function _getApi()
    {
        if ($this->_api === null) {
            $this->_api =
                [
                    'apiKey'      => $this->_configHelper->getClientId(),
                    'apiSecret'   => $this->_configHelper->getClientSecret(),
                    'apiCallback' => $this->_configHelper->getRedirectUrl(),
                ];
        }
        return $this->_api;
    }

    protected function _isAllowed()
    {
        return $this->_configHelper->isEnabled();
    }
}
