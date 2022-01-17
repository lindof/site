<?php

namespace Biztech\Deliverydate\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;

class SaveDeliveryDateToOrderObserver implements ObserverInterface
{

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
    protected $serializer;
    protected $quote = null;
    private $quoteItems = [];

    /**
     * @param ObjectManagerInterface $objectmanager
     * @param Json|null              $serializer
     */
    public function __construct(ObjectManagerInterface $objectmanager, Json $serializer = null)
    {
        $this->_objectManager = $objectmanager;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    public function execute(EventObserver $observer)
    {
        try {
            $quote = $observer->getQuote();
            $order = $observer->getOrder();
            
            $quoteRepository = $this->_objectManager->create('Magento\Quote\Model\QuoteRepository');
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $quoteRepository->get($order->getQuoteId());

            $order->setShippingArrivalDate($quote->getShippingArrivalDate());
            $order->setShippingArrivalSlot($quote->getShippingArrivalSlot());
            $order->setShippingArrivalComments($quote->getShippingArrivalComments());
            $order->setDeliveryCharges($quote->getDeliveryCharges());
            $order->setSameDayCharges($quote->getSameDayCharges());
            $order->setCallBeforeDelivery($quote->getCallBeforeDelivery());
            return $this;
        } catch (\Exception $e) {
            return $this;
        }
    }
}
