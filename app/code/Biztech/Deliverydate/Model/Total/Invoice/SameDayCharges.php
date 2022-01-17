<?php

namespace Biztech\Deliverydate\Model\Total\Invoice;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class SameDayCharges extends AbstractTotal
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
        $baseSameDayCharges = $invoice->getOrder()->getSameDayCharges();
        $deliverySameDayCharges = $this->_priceCurrency->convert($baseSameDayCharges);
        $invoice->setSameDayCharges($deliverySameDayCharges);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseSameDayCharges);
        $invoice->setGrandTotal($invoice->getGrandTotal() + $deliverySameDayCharges);

        return $this;
    }
}
