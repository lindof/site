<?php
namespace Mexbs\ApBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class LoadHintMessagesToQuote implements ObserverInterface{

    protected $serializer;

    public function __construct(
        \Mexbs\ApBase\Serialize $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();

        $hintMessagesSerialized = $quote->getHintMessages();

        $hintMessages = [];
        if(is_string($hintMessagesSerialized)){
         try{
            $hintMessages = $this->serializer->unserialize($hintMessagesSerialized);
         }catch(\Exception $e){
         }
        }elseif(is_array($hintMessagesSerialized)){
            $hintMessages = $hintMessagesSerialized;
        }

        $quote->setHintMessages($hintMessages);
    }
}