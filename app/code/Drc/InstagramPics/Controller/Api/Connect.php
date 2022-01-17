<?php

namespace Drc\InstagramPics\Controller\Api;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Drc\InstagramPics\Helper\Config;
use Drc\InstagramPics\Model\Instagram\Api;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Session\Generic;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\UrlInterface;

class Connect extends Action
{
    protected $_api = null;
    protected $_configHelper;
    protected $_urlBuilder;
    protected $_authSession;
    protected $_genericSession;
    protected $_messageManager;

    public function __construct(
        Context $context,
        Config $configHelper,
        Api $instaApi,
        UrlInterface $urlBuilder,
        Session $authSession,
        Generic $genericSession,
        ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->_configHelper   = $configHelper;
        $this->_api            = $instaApi;
        $this->_urlBuilder     = $urlBuilder;
        $this->_authSession    = $authSession;
        $this->_genericSession = $genericSession;
        $this->_messageManager = $messageManager;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $code = $this->getRequest()->getParam('code');

        if ($code === null) {
            //echo 'Incorrect Instagram authorization code.';
            $this->_messageManager->addSuccess(__('Incorrect Instagram authorization code.'));
        }
        $accessToken = $this->_getApi()->getOAuthToken($code);
        $this->_configHelper->connect($accessToken);
        //echo 'Instagram connect is successful.';
        $this->_messageManager->addSuccess(__('Instagram connect is successful.'));

        if ($accessToken === 400) {
            //echo '400 not found';
            $this->_messageManager->addSuccess(__('400 not found.'));
        }

        if (!$accessToken) {
            //echo "Incorrect Instagram authorization code.";
            $this->_messageManager->addSuccess(__('Incorrect Instagram authorization code.'));
        }
        
        if ($accessToken) {
            $this->_configHelper->connect($accessToken);
            echo '<script>window.close();</script>';
        }

?>
        <script>
            (function() {
                window.onunload = refreshParent;
                function refreshParent() {
                    window.opener.location.reload();
                    window.close();
                }
            })();
        </script>
        
<?php
    }

    protected function _getApi()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $parameters =   [
                            'apiKey'      => $this->_configHelper->getClientId(),
                            'apiSecret'   => $this->_configHelper->getClientSecret(),
                            'apiCallback' => $this->_configHelper->getRedirectUrl()
                        ];

        $this->_api = $objectManager->create('Drc\InstagramPics\Model\Instagram\Api');
        $this->_api->setConfig($parameters);

        return $this->_api;
    }

    protected function _getAdminSession()
    {
        return $this->_authSession;
    }
}
