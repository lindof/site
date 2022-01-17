<?php

namespace Biztech\Deliverydate\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use \Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Biztech\Deliverydate\Helper\Data;
use Magento\Framework\App\Helper\Context;
/**
 * Delivery Charges.
 */
class DeliveryCharges extends AbstractTotal
{

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;
    protected $_helper;
    /**
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        Data $helper,
        Context $context
    ) {
        $this->_priceCurrency = $priceCurrency;
        $this->_helper = $helper;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     *
     * @return $this|bool
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {

        parent::collect($quote, $shippingAssignment, $total);
        $baseDeliveryCharges = $quote->getDeliveryCharges();
        $deliveryCharges = $this->_priceCurrency->convert($baseDeliveryCharges);
        $total->addTotalAmount('delivery_charges', $deliveryCharges);
        $total->addBaseTotalAmount('delivery_charges', $baseDeliveryCharges);
        $total->setBaseGrandTotal($total->getBaseGrandTotal() + $baseDeliveryCharges);
        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        $storeID = $quote->getStoreId();
        if (($this->scopeConfig->getValue('deliverydate/deliverydate_general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeID)) && (in_array($storeID, $this->_helper->getAllWebsites()))) 
        {
            $amount = $total->getDeliveryCharges();
            if ($amount == 0) {
                $amount = $quote->getDeliveryCharges();
            }
            $title = __('Specific Timeslot Charges');
            return [
                'code' => $this->getCode(),
                'title' => $title,
                'value' => $amount
            ];
        }
    }

    /**
     * Get Shipping label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Specific Timeslot Charges');
    }
}
