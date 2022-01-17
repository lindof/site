<?php

namespace Biztech\Deliverydate\Model\Total\Creditmemo;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

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
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        parent::collect($creditmemo);
        
        $baseDeliveryCharges = $creditmemo->getOrder()->getDeliveryCharges();
        $deliveryCharges = $this->_priceCurrency->convert($baseDeliveryCharges);

        $creditmemo->setDeliveryCharges($deliveryCharges);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseDeliveryCharges);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $deliveryCharges);

        return $this;
    }
}
