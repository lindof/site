<?php
namespace Mexbs\ApBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveHintDataToQuoteItem implements ObserverInterface{

    protected $serializer;

    public function __construct(
        \Mexbs\ApBase\Serialize $serializer
    ) {
        $this->serializer = $serializer;
    }

    protected function getSerializedData($data){
        $dataSerialized = $data;
        if(!is_string($data)){
            $dataSerialized = $this->serializer->serialize($data);
        }
        return $dataSerialized;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getItem();

        $hintMessages = $item->getHintMessages();
        $item->setHintMessages($this->getSerializedData($hintMessages));
    }
}