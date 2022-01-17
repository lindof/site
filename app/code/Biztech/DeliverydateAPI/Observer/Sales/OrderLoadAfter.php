<?php

namespace Biztech\DeliverydateAPI\Observer\Sales;

use Magento\Framework\Event\ObserverInterface;

class OrderLoadAfter implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null)
        {
            $extensionAttributes = $this->getOrderExtensionDependency();
        }
        $DeliveryDateComment = $order->getData('shipping_arrival_comments');
        $DeliveryDate = $order->getData('shipping_arrival_date');
        $DeliveryDateTimeslot = $order->getData('shipping_arrival_slot');
        $samedaycharge = $order->getData('same_day_charges');
        $deleveryCharge = $order->getData('delivery_charges');
        $extensionAttributes->setShippingArrivalComments($DeliveryDateComment);
        $extensionAttributes->setShippingArrivalDate($DeliveryDate);
        $extensionAttributes->setShippingArrivalSlot($DeliveryDateTimeslot);
        $extensionAttributes->setDeliveryCharges($deleveryCharge);
        $extensionAttributes->setSameDayCharges($samedaycharge);
        $order->setExtensionAttributes($extensionAttributes);
    }

    private function getOrderExtensionDependency()
    {
        $orderExtension = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magento\Sales\Api\Data\OrderExtension');
        return $orderExtension;
    }

}
