<?php

namespace Drc\InstagramPics\Block\Adminhtml\System\Config\Frontend\Oauth;

use Magento\Backend\Block\Template;
use Drc\InstagramPics\Helper\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\UrlInterface;

class Connect extends Template
{
    protected $_api = null;
    protected $_configHelper;
    protected $_urlBuilder;

    public function __construct(
        Config $configHelper,
        UrlInterface $urlBuilder,
        Context $context,
        array $data = []
    ) {
        $this->_configHelper = $configHelper;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $arguments = [
                    'apiKey'      => $this->_configHelper->getClientId(),
                    'apiSecret'   => $this->_configHelper->getClientSecret(),
                    'apiCallback' => $this->_configHelper->getRedirectUrl(),
                ];
        $this->_api = $objectManager->create('Drc\InstagramPics\Model\Instagram\Api');
        $this->_api->setConfig($arguments);
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    public function getButton()
    {
        return '<button type="button" 
					id="instagrampics_oauth" 
					class="scalable " 
					onclick="" 
					style="width:280px" 
					title="Connect">
				<span>Connect</span>
				</button>';
    }

    /**
     * @return string
     */
    public function getButtonHtml()
    {
        return '<button type="button" 
					id="instagrampics_oauth" 
					class="scalable " 
					style="width:280px" 
					onclick="" 
					title="Connect">
					<span>Connect</span>
				</button>';
    }

    /**
     * @return string
     */
    public function getContainerId()
    {
        return parent::getContainerId();
    }

    public function getLoginUrl()
    {
        return $this->_getApi()->getLoginUrl(array('basic', 'public_content'));
    }

    public function getSaveConfigUrl()
    {
        return $this->_urlBuilder->getUrl('drc_instagrampics/index/save');
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
}
