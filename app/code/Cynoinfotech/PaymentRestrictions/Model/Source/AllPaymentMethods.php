<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Model\Source;

/**
 * Class Allshippingmethods
 */
class AllPaymentMethods implements \Magento\Framework\Option\ArrayInterface
{
   /**
    * Core store config
    * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }
    /**
     * Return array of carriers.
     * If $isActiveOnlyFlag is set to true, will return only active carriers
     *
     * @param bool $isActiveOnlyFlag
     * @return array
     */
    public function toOptionArray()
    {
        $paymentMethodList  = $this->_scopeConfig->getValue('payment');
        foreach ($paymentMethodList as $code => $method) {
            if (isset($method['title'])) {
                $methodTitle = '';
                if (isset($method['group'])) {
                    $methodTitle .= $method['group'].' - ';
                }
                $methodTitle .= $method['title'];
                
                $methods[] = [
                        'value' => $code,
                        'label' => $methodTitle,
                    ];
            }
        }
        return $methods;
    }
}
