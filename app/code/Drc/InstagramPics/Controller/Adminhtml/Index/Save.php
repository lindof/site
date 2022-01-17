<?php

namespace Drc\InstagramPics\Controller\Adminhtml\Index;

use Drc\InstagramPics\Helper\Config;
use Drc\InstagramPics\Model\Instagram\Api;
use Magento\Backend\App\Action\Context;

class Save extends \Drc\InstagramPics\Controller\Adminhtml\Index
{
    protected $_configHelper;
    protected $_api = null;
    
    public function __construct(
        Config $configHelper,
        Api $instaApi,
        Context $context
    ) {
        $this->_configHelper   = $configHelper;
        $this->_api            = $instaApi;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = [
            'success'   => true,
            'login_url' => null,
        ];

        $clientId     = $this->_request->getPost('client_id', null);
        $clientSecret = $this->_request->getPost('client_secret', null);

        try {
            $this->_configHelper->saveClientId($clientId);
            $this->_configHelper->saveClientSecret($clientSecret);
        } catch (\Exception $e) {
            $result['success'] = false;
        }

        $result['login_url'] = $this->_getApi()->getLoginUrl(
            ['basic', 'public_content']
        );

        $this->getResponse()->setBody(
            json_encode($result)
        );
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
    
    protected function _isAllowed()
    {
        return $this->_configHelper->isEnabled();
    }
}
