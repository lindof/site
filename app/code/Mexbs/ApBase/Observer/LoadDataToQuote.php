<?php
namespace Mexbs\ApBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class LoadDataToQuote implements ObserverInterface{

    protected $serializer;

    public function __construct(
        \Mexbs\ApBase\Serialize $serializer
    ) {
        $this->serializer = $serializer;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();

        $dataKeys = ['extra_messages_to_display' => 'array'];

        foreach($dataKeys as $dataKey => $dataType){
            $dataSerialized = $quote->getData($dataKey);

            $data = null;
            if($dataType == "array"){
                $data = [];
            }

            if(is_string($dataSerialized)){
                try{
                    $data = $this->serializer->unserialize($dataSerialized);
                }catch(\Exception $e){
                }
            }elseif($dataType == "array" && is_array($dataSerialized)){
                $data = $dataSerialized;
            }

            $quote->setData($dataKey, $data);
        }
    }
}