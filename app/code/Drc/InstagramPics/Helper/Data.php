<?php

namespace Drc\InstagramPics\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ValueInterface;
use Magento\Framework\DB\Transaction;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\App\ReinitableConfig;

class Data extends AbstractHelper
{
    protected $_backendModel;
    protected $_transaction;
    protected $_storeManager;
    protected $_scopeConfig;
    protected $_storeId;
    protected $_storeCode;
    protected $_crypt;
    protected $_reinit;
    protected $_helperConfig;

    public function __construct(
        ValueInterface $valueInterface,
        Transaction $transaction,
        StoreManagerInterface $storeManagerInterface,
        ScopeConfigInterface $scopeConfigInterface,
        Encryptor $crypt,
        ReinitableConfig $reinit,
        Context $context
    ) {
        $this->_backendModel = $valueInterface;
        $this->_transaction  = $transaction;
        $this->_storeManager = $storeManagerInterface;
        $this->_scopeConfig  = $scopeConfigInterface;
        $this->_crypt        = $crypt;
        $this->_reinit       = $reinit;
        $this->_storeId      = (int)$this->_storeManager->getStore()->getId();
        $this->_storeCode    = $this->_storeManager->getStore()->getCode();
        parent::__construct($context);
    }

    public function __()
    {
        $args = func_get_args();
        if ($args[0] == '{{connect_hint}}') {
            if ($this->_helperConfig()->isConnected()) {
                return '';
            }
            $args[0] = 'Add <b>%s</b> to redirect urls for Instagram application';
            $args[1] = $this->_helperConfig()->getRedirectUrl();
        }
        $expr = new \Magento\Framework\Phrase(array_shift($args), $this->_getModuleName());
        array_unshift($args, $expr);
        return $this->_translator->translate($args);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->_scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE, //store
            $storeId //1
        );
    }
    
    public function _saveConfig($path, $value)
    {
        $data =
            [
                'path' => $path,
                'scope' =>  'stores',
                'scope_id' => $this->_storeId,
                'scope_code' => $this->_storeCode,
                'value' => $value,
            ];

        $this->_backendModel->addData($data);
        $this->_transaction->addObject($this->_backendModel);
        $this->_transaction->save();
    }
}
