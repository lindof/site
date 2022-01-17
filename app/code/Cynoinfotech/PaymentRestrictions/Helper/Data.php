<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Helper;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_currentCustomer;
    
    /**
     * @var \Cynoinfotech\PaymentRestrictions\Model\PaymentrestrictionsFactory
     */
    protected $paymentRestrictionsModel;
    
    /**
     * @var \Cynoinfotech\Shippingrules\Model\ResourceModel\Shippingrules\CollectionFactory
     */
    protected $_collectionFactory;
    
     /**
      * @var \Magento\SalesRule\Model\Utility
      */
    protected $validatorUtility;
    
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;
    
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    
     /**
      * @var \Magento\SalesRule\Api\Data\ConditionInterfaceFactory
      */
    protected $conditionDataFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigObject,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $currentCustomer,
        \Cynoinfotech\PaymentRestrictions\Model\PaymentrestrictionsFactory $paymentRestrictionsFactory,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\SalesRule\Api\Data\ConditionInterfaceFactory $conditionDataFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializerInterface,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->_scopeConfig = $scopeConfigObject;
        $this->_urlInterface = $urlInterface;
        $this->_storeManager = $storeManager;
        $this->_currentCustomer = $currentCustomer;
        $this->paymentRestrictionsModel = $paymentRestrictionsFactory;
        $this->validatorUtility = $utility;
        $this->resourceConnection = $resourceConnection;
        $this->conditionDataFactory = $conditionDataFactory;
        $this->serializerInterface = $serializerInterface;
        $this->_dateTime = $dateTime;
    }
    
    /**
     * Functionality to get configuration values of plugin
     * @param $configPath: System xml config path
     * @return value of requested configuration
     */
     
    public function getConfig($configPath)
    {
        return $this->_scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
       
    public function checkPaymentMethodCondition($event)
    {
        if ($this->_currentCustomer->getCustomerGroupId()) {
            $group_id = $this->_currentCustomer->getCustomerGroupId();
        } else {
            $group_id = 0;
        }
            
        $currentStoreId = $this->_storeManager->getStore()->getStoreId();
        $cartQuote= $event->getQuote();
        $address = $cartQuote->getShippingAddress();
        $items=$cartQuote->getAllItems();
        $checkCurrentPaymentMethod = false;
        
        $paymentRestrictionsCollection = $this->paymentRestrictionsModel->create()->getCollection()
                                            ->setOrder('priority', 'DESC')
                                            ->addFieldToFilter('status', '1');
        
        if (!empty($paymentRestrictionsCollection)) {
            foreach ($paymentRestrictionsCollection as $paymentRestrictions) {
                $paymentMethodsArray = explode(',', $paymentRestrictions->getMethods());
                // check for Payment Method --------------------------------------//
                if (!in_array($event->getMethodInstance()->getCode(), $paymentMethodsArray)) {
                    continue;
                }
                
                // check for days------------------------------------------------//
                $paymentMethodAppliedDays = explode(",", $paymentRestrictions->getDays());
                $weekday = $this->_dateTime->date('w');
                if (!in_array($weekday, $paymentMethodAppliedDays)) {
                    continue;
                }
                
                // check for Store---------------------------------------------//
                
                $paymentRestrictionsAppliedForStores = explode(",", $paymentRestrictions->getStores());
                if (!in_array($currentStoreId, $paymentRestrictionsAppliedForStores) &&
                !in_array(0, $paymentRestrictionsAppliedForStores)) {
                    continue;
                }
                
                // check for Customer Group---------------------------------------//
                
                $paymentRestrictionsAppliedForGroups = explode(",", $paymentRestrictions->getCustomerGroup());
                if (!in_array($group_id, $paymentRestrictionsAppliedForGroups)) {
                    continue;
                }
                // check for Address Conditions---------------------------------------//
                                
                if ($paymentRestrictions && $this->checkIfConditionsAreBlank($paymentRestrictions)) {
                    $checkCurrentPaymentMethod = true;
                } elseif (!$this->validatorUtility->canProcessRule($paymentRestrictions, $address)) {
                    $checkCurrentPaymentMethod = false;
                    continue;
                } else {
                    $checkCurrentPaymentMethod = true;
                }
                
                if ($checkCurrentPaymentMethod == true) {
                    break;
                }
            }
        }
        
        return $checkCurrentPaymentMethod;
    }

    public function checkIfConditionsAreBlank($rule)
    {
        $unserializedConditions = [];
        $data=$rule->getData('conditions_serialized');
        $unserializedConditions = $this->serializerInterface->unserialize($data);
              
        if (is_array($unserializedConditions) && array_key_exists('conditions', $unserializedConditions)) {
            return false;
        }
        return true;
    }
}
