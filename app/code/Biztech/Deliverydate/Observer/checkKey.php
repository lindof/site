<?php

namespace Biztech\Deliverydate\Observer;

use Biztech\Deliverydate\Helper\Data;
use Magento\Config\Model\Config\Factory;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Zend\Json\Json;

class checkKey implements ObserverInterface
{

    const XML_PATH_ACTIVATIONKEY = 'deliverydate/activation/key';
    const XML_PATH_DATA = 'deliverydate/activation/data';
    const XML_PATH_STORE = 'deliverydate/activation/store';

    protected $scopeConfig;
    protected $encryptor;
    protected $configFactory;
    protected $helper;
    protected $objectManager;
    protected $request;
    protected $resourceConfig;
    protected $configValueFactory;
    protected $zend;
    protected $cacheTypeList;
    protected $cacheFrontendPool;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param Factory $configFactory
     * @param Data $helper
     * @param ObjectManagerInterface $objectmanager
     * @param RequestInterface $request
     * @param Json $zend
     * @param Config $resourceConfig
     * @param ValueFactory $configValueFactory
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        Factory $configFactory,
        Data $helper,
        ObjectManagerInterface $objectmanager,
        RequestInterface $request,
        Json $zend,
        Config $resourceConfig,
        ValueFactory $configValueFactory,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_encryptor = $encryptor;
        $this->_configFactory = $configFactory;
        $this->_helper = $helper;
        $this->_objectManager = $objectmanager;
        $this->_request = $request;
        $this->_zend = $zend;
        $this->_resourceConfig = $resourceConfig;
        $this->_configValueFactory = $configValueFactory;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $params = $this->_request->getParam('groups');

        if(isset($params['activation']['fields']['key']['value'])){
            $k = $params['activation']['fields']['key']['value'];
        }else{
            $k = $this->_scopeConfig->getValue(
                self::XML_PATH_ACTIVATIONKEY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        $s = "";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, sprintf('https://www.appjetty.com/extension/licence.php'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'key=' . urlencode($k) . '&domains=' . urlencode(implode(',', $this->_helper->getAllStoreDomains())) . '&sec=magento2-deliverydatepro');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $content = curl_exec($ch);
        $res1 = $this->_zend->decode($content);
        $res = (array)$res1;
        $moduleStatus = $this->_resourceConfig;
        if (empty($res)) {
            $moduleStatus->saveConfig('deliverydate/activation/key', "");
            $moduleStatus->saveConfig('deliverydate/deliverydate_general/enabled', 0);
            $data = $this->_scopeConfig('deliverydate/activation/data');
            $this->_resourceConfig->saveConfig('deliverydate/activation/data', $data, 'default', 0);
            $this->_resourceConfig->saveConfig('deliverydate/activation/websites', '', 'default', 0);
            $this->_resourceConfig->saveConfig('deliverydate/activation/store', '', 'default', 0);
            return;
        }
        $data = '';
        $web = '';
        $en = '';
        if (isset($res['dom']) && intval($res['c']) > 0 && intval($res['suc']) == 1) {
            $data = $this->_encryptor->encrypt(base64_encode($this->_zend->encode($res1)));
            if (!$s) {
                if (isset($params['activation']['fields']['store']['value'])) {
                    $s = $params['activation']['fields']['store']['value'];
                }else{
                    $s = $this->_scopeConfig->getValue(
                        self::XML_PATH_STORE,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );
                }
            }
            $en = $res['suc'];
            if (isset($s) && $s != null) {
                if(is_array($s)){
                    $web = $this->_encryptor->encrypt($data . implode(',', $s) . $data);
                }else{
                    $web = $this->_encryptor->encrypt($data . $s . $data);
                }
            } else {
                $web = $this->_encryptor->encrypt($data . $data);
            }
        } else {
            $moduleStatus->saveConfig('deliverydate/activation/key', "", 'default', 0);
            $moduleStatus->saveConfig('deliverydate/deliverydate_general/enabled', 0, 'default', 0);
            $this->_resourceConfig->saveConfig('deliverydate/activation/store', '', 'default', 0);
        }

        $this->_resourceConfig->saveConfig('deliverydate/activation/data', $data, 'default', 0);
        $this->_resourceConfig->saveConfig('deliverydate/activation/websites', $web, 'default', 0);
        $this->_resourceConfig->saveConfig('deliverydate/activation/en', $en, 'default', 0);
        $this->_resourceConfig->saveConfig('deliverydate/activation/installed', 1, 'default', 0);

        //refresh config cache after save
        $types = ['config', 'full_page'];
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
