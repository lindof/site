<?php

namespace Biztech\Deliverydate\Model\Total\Creditmemo;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

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
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        parent::collect($creditmemo);
        $baseSameDayCharges = $creditmemo->getOrder()->getSameDayCharges();
        $deliverySameDayCharges = $this->_priceCurrency->convert($baseSameDayCharges);

        $creditmemo->setSameDayCharges($deliverySameDayCharges);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseSameDayCharges);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $deliverySameDayCharges);

        return $this;
    }
}
