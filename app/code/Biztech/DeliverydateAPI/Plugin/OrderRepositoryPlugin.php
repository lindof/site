<?php

namespace Biztech\DeliverydateAPI\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderRepositoryPlugin
 */
class OrderRepositoryPlugin
{
    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();

        foreach ($orders as &$order) {
            $DeliveryDateComment = $order->getData('shipping_arrival_comments');
	        $DeliveryDate = $order->getData('shipping_arrival_date');
	        $DeliveryDateTimeslot = $order->getData('shipping_arrival_slot');
	        $samedaycharge = $order->getData('same_day_charges');
	        $deleveryCharge = $order->getData('delivery_charges');
	        $extensionAttributes = $order->getExtensionAttributes();
	        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
	        $extensionAttributes->setShippingArrivalComments($DeliveryDateComment);
	        $extensionAttributes->setShippingArrivalDate($DeliveryDate);
	        $extensionAttributes->setShippingArrivalSlot($DeliveryDateTimeslot);
	        $extensionAttributes->setDeliveryCharges($deleveryCharge);
	        $extensionAttributes->setSameDayCharges($samedaycharge);
	        $order->setExtensionAttributes($extensionAttributes);
        }

        return $searchResult;
    }
}
