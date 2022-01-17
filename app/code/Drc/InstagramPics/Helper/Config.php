<?php

namespace Drc\InstagramPics\Helper;

use Drc\InstagramPics\Helper\Data as HelperData;

class Config extends HelperData
{
    const API_ACCESS_TOKEN                 = 'instagrampics/api/access_token';
    const XML_PATH_INSTAGRAM_ENABLED       = 'instagrampics/general/is_enabled';
    const XML_PATH_INSTAGRAM_STATUS        = 'instagrampics/api/status';
    const XML_PATH_INSTAGRAM_CLIENT_ID     = 'instagrampics/api/client_id';
    const XML_PATH_INSTAGRAM_CLIENT_SECRET = 'instagrampics/api/client_secret';
    const XML_PATH_INSTAGRAM_OAUTH         = 'instagrampics/api/oauth';

    public function connect($accessToken)
    {
        //echo $accessToken->access_token;
        //exit();
        $encryptedAccessToken = $this->_crypt->encrypt($accessToken->access_token);
        $this->_saveConfig(self::API_ACCESS_TOKEN, $encryptedAccessToken);

        $this->_reinit->reinit();
    }
    
    public function disconnect()
    {
        $this->_saveConfig(self::API_ACCESS_TOKEN, '');

        $this->_reinit->reinit();
    }
    
    public function isConnected()
    {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getValue(self::API_ACCESS_TOKEN, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($conf) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getAccessToken($storeId = null)
    {
        return $this->_crypt->decrypt($this->getConfigValue(self::API_ACCESS_TOKEN, $storeId));
    }
    
    public function isEnabled($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_INSTAGRAM_ENABLED, $storeId);
    }

    public function getStatus($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_INSTAGRAM_STATUS, $storeId);
    }
    
    public function saveClientId($clientId)
    {
        $this->_saveConfig(self::XML_PATH_INSTAGRAM_CLIENT_ID, $clientId);

        // reinit configuration cache
        $this->_reinit->reinit();
    }
    
    public function getClientId($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_INSTAGRAM_CLIENT_ID, $storeId);
    }
    
    public function saveClientSecret($clientSecret)
    {
        $this->_saveConfig(self::XML_PATH_INSTAGRAM_CLIENT_SECRET, $clientSecret);

        // reinit configuration cache
        $this->_reinit->reinit();
    }
    
    public function getClientSecret($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_INSTAGRAM_CLIENT_SECRET, $storeId);
    }
    
    public function getRedirectUrl($storeId = null)
    {
        $url = $this->getUrl('instagrampics/api/connect');

        if (stripos($url, 'index.php')) {
            return $url;
        }
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        
        $baseUrl = str_ireplace('index.php', '', $baseUrl);
        $url = str_ireplace($baseUrl, '', $url);
        $returnUrl = $baseUrl . 'index.php/' . $url;
        //return $returnUrl;
        $temp = explode("?", $returnUrl);
        return $temp[0];
    }

    public function getAuthUrl()
    {
        return $this->_getUrl('instagrampics/api/callback', ['_secure' => $this->isSecure(), 'auth' => 1]);
    }

    public function getUrl($path)
    {
        return $this->_getUrl($path);
    }
}
