<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Observer;

use Magento\Framework\Event\ObserverInterface;

class DisabledPaymentByPaymentRestrictions implements ObserverInterface
{
    public function __construct(
        \Cynoinfotech\PaymentRestrictions\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->_logger = $logger;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->getConfig('cynoinfotech_paymentrestrictions/general/enable')) {
            $event           = $observer->getEvent();
            $result          = $observer->getEvent()->getResult();
            $method_instance = $observer->getEvent()->getMethodInstance();
            $quote           = $observer->getEvent()->getQuote();
            
            if (null !== $quote) {
                $isNotActive=$this->helper->checkPaymentMethodCondition($event);
                
                if ($isNotActive) {
                     $result->setData('is_available', false);
                }
            }
        }
    
        return $this;
    }
}
