<?php

namespace Biztech\Deliverydate\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Biztech\Deliverydate\Helper\Data;
use Magento\Framework\App\Helper\Context;
class SameDayCharges extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null;
    protected $_helper;
    protected $deliveryCharges;
    protected $scopeConfig;
    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator, PriceCurrencyInterface $priceCurrency,
        Data $helper,
        Context $context)
    {
        $this->quoteValidator = $quoteValidator;
        $this->_priceCurrency = $priceCurrency;
        $this->_helper = $helper;
        $this->scopeConfig = $context->getScopeConfig();
    }
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $baseDeliveryCharges = $quote->getSameDayCharges();
        $this->deliveryCharges = $this->_priceCurrency->convert($baseDeliveryCharges);
        $total->addTotalAmount('same_day_charges', $this->deliveryCharges);
        $total->addBaseTotalAmount('same_day_charges', $baseDeliveryCharges);
        $total->setBaseGrandTotal($total->getBaseGrandTotal() + $baseDeliveryCharges);
        return $this;
    }

    protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array|null
     */
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $storeID = $quote->getStoreId();
        if (($this->scopeConfig->getValue('deliverydate/deliverydate_general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeID)) && (in_array($storeID, $this->_helper->getAllWebsites()))) { 
            return [
            'code' => 'same_day_charges',
            'title' => $this->getLabel(),
            'value' => $quote->getSameDayCharges()
            ];
        }
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Same Day Delivery Charges');
    }
}
