<?php

namespace Biztech\Deliverydate\Model\Total\Invoice;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class DeliveryCharges extends AbstractTotal
{
    protected $_priceCurrency;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->_priceCurrency = $priceCurrency;
    }

    /**
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        parent::collect($invoice);
        $baseDeliveryCharges = $invoice->getOrder()->getDeliveryCharges();
        $deliveryCharges = $this->_priceCurrency->convert($baseDeliveryCharges);
        $invoice->setDeliveryCharges($deliveryCharges);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseDeliveryCharges);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $deliveryCharges);
        return $this;
    }
}
