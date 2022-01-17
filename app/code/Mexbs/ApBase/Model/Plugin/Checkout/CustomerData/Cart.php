<?php
namespace Mexbs\ApBase\Model\Plugin\Checkout\CustomerData;

class Cart
{
    protected $checkoutSession;
    protected $checkoutHelper;
    protected $quote;
    protected $taxHelper;
    protected $serializer;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Tax\Helper\Data $taxHelper,
        \Mexbs\ApBase\Serialize $serializer
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->checkoutHelper = $checkoutHelper;
        $this->taxHelper = $taxHelper;
        $this->serializer = $serializer;
    }
    /**
     * Get active quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }

    protected function findItemById($id, $itemsHaystack)
    {
        if (is_array($itemsHaystack)) {
            foreach ($itemsHaystack as $item) {
                /** @var $item \Magento\Quote\Model\Quote\Item */
                if ((int)$item->getItemId() == $id) {
                    return $item;
                }
            }
        }
        return false;
    }

    protected function getDecoratedHintMessages($hintMessages){
        $decoratedHintMessages = [];
        foreach($hintMessages as $ruleId => $hintMessage){
            $decoratedHintMessages[] = [
                'rule_id' => $ruleId,
                'hint_message' => $hintMessage
            ];
        }
        return $decoratedHintMessages;
    }

    protected function getDiscountAmount()
    {
        $discountAmount = 0;
        foreach($this->getQuote()->getAllVisibleItems() as $item){
            $discountAmount += ($item->getDiscountAmount() ? $item->getDiscountAmount() : 0);
        }
        return $discountAmount;
    }

    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        $items = $this->getQuote()->getAllVisibleItems();
        if (is_array($result['items'])) {
            foreach ($result['items'] as $key => $itemAsArray) {
                if ($item = $this->findItemById($itemAsArray['item_id'], $items)) {
                    $result['items'][$key]['discount_amount'] = 0;
                    if($this->taxHelper->displayCartPriceInclTax()){
                        $result['items'][$key]['price_incl_discount'] = $this->checkoutHelper->formatPrice($item->getRowTotalInclTax() / $item->getQty());
                    }else{
                        $result['items'][$key]['price_incl_discount'] = $this->checkoutHelper->formatPrice($item->getRowTotal() / $item->getQty());
                    }

                    if($item->getDiscountAmount() > 0){
                        $result['items'][$key]['discount_amount'] = (-1)*$item->getDiscountAmount();
                        if($this->taxHelper->displayCartPriceInclTax()){
                            $result['items'][$key]['price_incl_discount'] = $this->checkoutHelper->formatPrice(($item->getRowTotalInclTax() - $item->getDiscountAmount()) / $item->getQty());
                        }else{
                            $result['items'][$key]['price_incl_discount'] = $this->checkoutHelper->formatPrice(($item->getRowTotal() - $item->getDiscountAmount()) / $item->getQty());
                        }
                    }

                    $result['items'][$key]['gift_message'] = '';
                    $giftMessage = $item->getGiftMessage();
                    if($item->getGiftRuleId() && $giftMessage){
                        if($giftMessage){
                            $result['items'][$key]['gift_message'] = $giftMessage;
                        }
                    }

                    $result['items'][$key]['gift_trigger_item_ids_qtys_of_same_group'] = "";
                    $giftTriggerItemIdsQtysOfSameGroup = null;
                    if($item->getGiftTriggerItemIdsQtysOfSameGroup()){
                        try{
                            $giftTriggerItemIdsQtysOfSameGroup = $this->serializer->unserialize($item->getGiftTriggerItemIdsQtysOfSameGroup());
                        }catch(\Exception $e){

                        }
                        if($giftTriggerItemIdsQtysOfSameGroup){
                            $result['items'][$key]['gift_trigger_item_ids_qtys_of_same_group'] = htmlspecialchars(json_encode($giftTriggerItemIdsQtysOfSameGroup));
                        }
                    }

                    $result['items'][$key]['gift_trigger_item_ids_qtys'] = "";
                    $giftTriggerItemIdsQtys = null;
                    if($item->getGiftTriggerItemIdsQtys()){
                        try{
                            $giftTriggerItemIdsQtys = $this->serializer->unserialize($item->getGiftTriggerItemIdsQtys());
                        }catch(\Exception $e){

                        }
                        if($giftTriggerItemIdsQtys){
                            $result['items'][$key]['gift_trigger_item_ids_qtys'] = htmlspecialchars(json_encode($giftTriggerItemIdsQtys));
                        }
                    }

                    $result['items'][$key]['gift_qtys_can_add_per_group'] = "";
                    $giftQtysCanAddPerGroup = null;
                    if($item->getGiftQtysCanAddPerGroup()){
                        try{
                            $giftQtysCanAddPerGroup = $this->serializer->unserialize($item->getGiftQtysCanAddPerGroup());
                        }catch(\Exception $e){

                        }
                        if($giftQtysCanAddPerGroup){
                            $result['items'][$key]['gift_qtys_can_add_per_group'] = htmlspecialchars(json_encode($giftQtysCanAddPerGroup));
                        }
                    }

                    $result['items'][$key]['hint_messages'] = '';
                    $hintMessages = null;
                    if($item->getHintMessages()){
                        try{
                            $hintMessages = $this->serializer->unserialize($item->getHintMessages());
                        }catch(\Exception $e){

                        }
                        if($hintMessages){
                            $hintMessages = $this->getDecoratedHintMessages($hintMessages);
                            $result['items'][$key]['hint_messages'] = json_encode($hintMessages);
                        }
                    }
                }
            }
        }

        $result['discount_amount_no_html'] = -$this->getDiscountAmount();
        $result['discount_amount'] = $this->checkoutHelper->formatPrice(-$this->getDiscountAmount());
        $result['subtotal_incl_discount'] = $this->checkoutHelper->formatPrice($this->getQuote()->getSubtotal() - $this->getDiscountAmount());
        $result['display_totals_incl_tax'] = ($this->taxHelper->displayCartPriceInclTax() ? "1" : "0");

        return $result;
    }
}