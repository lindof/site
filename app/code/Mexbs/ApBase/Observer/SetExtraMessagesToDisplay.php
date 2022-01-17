<?php
namespace Mexbs\ApBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class SetExtraMessagesToDisplay implements ObserverInterface{

    protected $apHelper;

    public function __construct(
        \Mexbs\ApBase\Helper\Data $apHelper
    ) {
        $this->apHelper = $apHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getItem();
        $address = $observer->getEvent()->getAddress();
        $quote = $item->getQuote();
        $rule = $observer->getEvent()->getRule();

        $translatedMessage = __($rule->getMessageToDisplay())->render();
        $messageToDisplay = $this->apHelper->processMessagePlaceholder($translatedMessage, $address, $rule->getId(), $item);

        $currentExtraMessages = $quote->getExtraMessagesToDisplay();
        if(!is_array($currentExtraMessages)){
            $currentExtraMessages = [];
        }
        if($messageToDisplay){
            $currentExtraMessages[$rule->getId()] = $messageToDisplay;
        }
        $quote->setExtraMessagesToDisplay($currentExtraMessages);
    }
}
