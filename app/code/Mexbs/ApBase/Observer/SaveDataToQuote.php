<?php
namespace Mexbs\ApBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveDataToQuote implements ObserverInterface{

    protected $serializer;

    public function __construct(
        \Mexbs\ApBase\Serialize $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();

        $dataKeys = ['extra_messages_to_display'];

        foreach($dataKeys as $dataKey){
            $data = $quote->getData($dataKey);

            $dataSerialized = $data;
            if(!is_string($dataSerialized)){
                $dataSerialized = $this->serializer->serialize($dataSerialized);
            }

            $quote->setData($dataKey, $dataSerialized);
        }
    }
}