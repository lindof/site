<?php

namespace Biztech\Deliverydate\Block\Sales\Order;

use Biztech\Deliverydate\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * DeliveryCharges.
 */
class DeliveryCharges extends Template
{

    /**
     *  * @var Order
     *   */
    protected $_order;

    /**
     *  * @var \Magento\Framework\DataObject
     *   */
    protected $_source;
    protected $_bizHelper;

    /**
     *
     * @param Context $context
     * @param Data $bizHelper
     * @param array $data
     *  
     */
    public function __construct(
        Context $context,
        Data $bizHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_bizHelper = $bizHelper;
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function displayFullSummary()
    {
        return true;
    }

    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        $title = 'Specific Timeslot Charges';
        $store = $this->getStore();
        if ($this->_order->getDeliveryCharges() != 0 || $this->_order->getDeliveryCharges() != 0) {
            $deliveryCharges = new \Magento\Framework\DataObject(
                [
                    'code' => 'delivery_charges',
                    'strong' => false,
                    'value' => $this->_source->getDeliveryCharges(),
                    'label' => __($title),
                ]
            );
            $parent->addTotal($deliveryCharges, 'delivery_charges');
        }
        if ($this->_order->getSameDayCharges() != 0 || $this->_order->getSameDayCharges() != 0) {
            $sameDayCharges = new \Magento\Framework\DataObject(
                [
                    'code' => 'same_day_charges',
                    'strong' => false,
                    'value' => $this->_source->getSameDayCharges(),
                    'label' => __('Same Day Delivery Charges'),
                ]
            );
            $parent->addTotal($sameDayCharges, 'same_day_charges');
        }
        return $this;
    }

    /**
     *  * Get order store object.
     *  *
     *  * @return \Magento\Store\Model\Store
     *   */
    public function getStore()
    {
        return $this->_order->getStore();
    }

    /**
     *  * @return Order
     *   */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     *  * @return array
     *   */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     *  * @return array
     *   */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
