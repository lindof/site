<?php
namespace Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product;

class Combine extends \Magento\SalesRule\Model\Rule\Condition\Product\Combine
{
    const DISCOUNT_TYPE_PERCENT = 'percent';
    const DISCOUNT_TYPE_FIXED = 'fixed';
    const DISCOUNT_TYPE_FIXED_PRICE = 'fixed_price';

    protected $discountPriceTypeOptions;
    protected $aggregatorValueOptions;
    protected $apHelper;
    protected $logger;
    private $ruleAvailProdsIndexCollFactory;
    private $productCollectionFactory;
    private $apRuleProductResourceFactory;
    private $productFactory;
    private $catalogProductVisibility;
    private $resource;
    private $connection;

    protected $type = 'Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product\Combine';

    const DISCOUNT_PRICE_TYPE_CHEAPEST = 'cheapest';
    const DISCOUNT_PRICE_TYPE_MOST_EXPENSIVE = 'most_expensive';

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\SalesRule\Model\Rule\Condition\Product $ruleConditionProduct,
        \Mexbs\ApBase\Helper\Data $apHelper,
        \Mexbs\ApBase\Logger\Logger $logger,
        \Mexbs\ApBase\Model\ResourceModel\RuleAvailableProductsIndex\CollectionFactory $ruleAvailProdsIndexCollFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Mexbs\ApBase\Model\ResourceModel\RuleAvailableProductsIndexFactory $apRuleProductResourceFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        parent::__construct($context, $ruleConditionProduct, $data);
        $this->apHelper = $apHelper;
        $this->logger = $logger;
        $this->ruleAvailProdsIndexCollFactory = $ruleAvailProdsIndexCollFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->apRuleProductResourceFactory = $apRuleProductResourceFactory;
        $this->productFactory = $productFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->setType($this->type)
            ->setActionDetails([]);

        $this->discountPriceTypeOptions = [
            self::DISCOUNT_PRICE_TYPE_CHEAPEST => __('cheapest'),
            self::DISCOUNT_PRICE_TYPE_MOST_EXPENSIVE => __('most expensive')
        ];
        $this->aggregatorValueOptions = [
            1 => __('TRUE'),
            0 => __('FALSE')
        ];
        $this->discountTypes = [
            self::DISCOUNT_TYPE_PERCENT,
            self::DISCOUNT_TYPE_FIXED,
            self::DISCOUNT_TYPE_FIXED_PRICE
        ];
    }

    protected function _getDiscountExpression($discountAmount){
        $currencySymbol = $this->apHelper->getCurrentCurrencySymbol();
        $discountExpression = "";

        if($this->getDiscountType() == self::DISCOUNT_TYPE_PERCENT){
            if($discountAmount == 100){
                $discountExpression = __("for free");
            }else{
                $discountExpression = __("with %1% discount", $discountAmount);
            }
        }elseif($this->getDiscountType() == self::DISCOUNT_TYPE_FIXED){
            $discountExpression =  __("with %1%2 discount", $currencySymbol, $discountAmount);
        }elseif($this->getDiscountType() == self::DISCOUNT_TYPE_FIXED_PRICE){
            $discountExpression =  __("for %1%2 only", $currencySymbol, $discountAmount);
        }

        return $discountExpression;
    }

    public function getAllNecessaryFieldsFilled(){
        $discountType = $this->getDiscountType();
        if(!$this->_getIsDiscountTypeValid($discountType)){
            return false;
        }
        $discountAmount = $this->getDiscountAmountValue();
        if(!$this->_getIsDiscountAmountValid($discountAmount, $discountType)){
            return false;
        }
        return true;
    }

    public function getGroupNumbersToIndex(){
        return [];
    }

    public function getRuleActionType(){
        return "";
    }

    public function getProductGroupActionTypeByNumber($groupNumber){
        return "";
    }

    public function collectValidatedAttributesForGroup($productCollection, $group){
        $group->collectValidatedAttributes($productCollection);
    }

    public function validateProductForGroupWithoutQuote($group, $product){
        return $group->validateProductWithoutQuote($product);
    }

    public function validateProductWithoutQuote($product){
        if (!$this->getConditions()) {
            return true;
        }

        $all = $this->getAggregator() === 'all';
        $true = (bool)$this->getValue();

        foreach ($this->getConditions() as $cond) {
            $validated = $cond->validateProductWithoutQuote($product);
            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }
        return $all ? true : false;
    }

    public function hasAddressConditionsInActionInAnyGroup(){
        foreach($this->getNonEmptyGroupNumbers() as $groupNumber){
            if($this->getGroupActionDetail($groupNumber)->hasAddressConditionsInAction()){
                return true;
            }
        }
        return false;
    }

    public function hasAddressConditionsInAction(){
        if (!$this->getConditions()) {
            return false;
        }

        foreach ($this->getConditions() as $cond) {
            if(($cond instanceof \Magento\SalesRule\Model\Rule\Condition\Address)
            || $cond->hasAddressConditionsInAction()){
                return true;
            }
        }
        return false;
    }

    protected function fillInGetYItemInEmptySlotsOfPossibleTriggerItems($getYItemsSlots, $possibleTriggerItemIdsQtysGroup, $getYItem, $getYItemQty){
        $resultingGetYItemsSlots = $getYItemsSlots;
        $filledInQty = 0;

        foreach($getYItemsSlots as $getYItemsSlotIndex => $getYItemsSlot){
            if($filledInQty == $getYItemQty){
                break;
            }
            if($getYItemsSlot['get_y_items_slots']['unfilled_qty'] == 0){
                continue;
            }
            $buyXSetMatchPossibleTriggerItems = true;
            foreach($getYItemsSlot['buy_x_items_data']['items'] as $buyXItemData){
                $item = $buyXItemData['item'];
                $itemTriggerQty = $buyXItemData['qty'];

                if(!isset($possibleTriggerItemIdsQtysGroup[$this->apHelper->getQuoteItemId($item)])
                    || ($possibleTriggerItemIdsQtysGroup[$this->apHelper->getQuoteItemId($item)] != $itemTriggerQty)){
                    $buyXSetMatchPossibleTriggerItems = false;

                    break;
                }
            }

            if($buyXSetMatchPossibleTriggerItems){
                $getYItemQtyToFillIn = min($resultingGetYItemsSlots[$getYItemsSlotIndex]['get_y_items_slots']['unfilled_qty'], max(0, $getYItemQty - $filledInQty));
                $resultingGetYItemsSlots[$getYItemsSlotIndex]['get_y_items_slots']['items'][] = [
                    'item' => $getYItem,
                    'qty' => $getYItemQtyToFillIn
                ];
                $resultingGetYItemsSlots[$getYItemsSlotIndex]['get_y_items_slots']['unfilled_qty'] -= $getYItemQtyToFillIn;

                $filledInQty += $getYItemQtyToFillIn;
            }
        }

        return [$resultingGetYItemsSlots, $filledInQty];
    }


    protected function fillInGetYItemInEmptySlotsOfAnyTriggerItems($getYItemsSlots, $getYItem, $getYItemQty){
        $resultingGetYItemsSlots = $getYItemsSlots;
        $filledInQty = 0;
        $triggerItemIdsQtysGroup = null;

        foreach($getYItemsSlots as $getYItemsSlotIndex => $getYItemsSlot){
            if($filledInQty == $getYItemQty){
                break;
            }
            if($getYItemsSlot['get_y_items_slots']['unfilled_qty'] == 0){
                continue;
            }

            $getYItemQtyToFillIn = min($resultingGetYItemsSlots[$getYItemsSlotIndex]['get_y_items_slots']['unfilled_qty'], max(0, $getYItemQty - $filledInQty));
            $resultingGetYItemsSlots[$getYItemsSlotIndex]['get_y_items_slots']['items'][] = [
                'item' => $getYItem,
                'qty' => $getYItemQtyToFillIn
            ];
            $resultingGetYItemsSlots[$getYItemsSlotIndex]['get_y_items_slots']['unfilled_qty'] -= $getYItemQtyToFillIn;

            $filledInQty += $getYItemQtyToFillIn;

            if(isset($getYItemsSlot['buy_x_items_data']['items'])){
                foreach($getYItemsSlot['buy_x_items_data']['items'] as $buyXItemData){
                    $triggerItemIdsQtysGroup[$this->apHelper->getQuoteItemId($buyXItemData['item'])] = $buyXItemData['qty'];
                }
            }
        }

        return [$resultingGetYItemsSlots, $filledInQty, $triggerItemIdsQtysGroup];
    }

    protected function setProductsToAutoAdd($getYItemsSlots, $rule, $getYDiscountAmount, $getYDiscountType, $quote, $discountExpression)
    {
        $resultGetYItemsSlots = $getYItemsSlots;

        if(($getYDiscountAmount != 100)
            && ($getYDiscountType != self::DISCOUNT_TYPE_PERCENT)){
            return $resultGetYItemsSlots;
        }

        if(!$this->apHelper->getAreAllRuleGroupPromoBlockProductsSimple($rule, $this->getGetYGroupNumber())){
            return $resultGetYItemsSlots;
        }

        $getYProductIds = $this->apHelper->getRuleGroupPromoBlockProductIds($rule, $this->getGetYGroupNumber());
        if(count($getYProductIds) != 1){
            return $resultGetYItemsSlots;
        }
        $productQtyToAutoAdd = 0;
        $triggerItemIdsQtys = [];

        foreach($getYItemsSlots as $getYItemsSlotData){
            $triggerItemIdsQtys = [];
            if(isset($getYItemsSlotData['buy_x_items_data']['items'])){
                foreach($getYItemsSlotData['buy_x_items_data']['items'] as $buyXItemData){
                    $buyXItem = $buyXItemData['item'];
                    $buyXItemId = $this->apHelper->getQuoteItemId($buyXItem);
                    $triggerItemIdsQtys[$buyXItemId] = $buyXItemData['qty'];
                }
            }
            $productQtyToAutoAdd += $getYItemsSlotData['get_y_items_slots']['unfilled_qty'];
        }
        if($productQtyToAutoAdd == 0){
            return $resultGetYItemsSlots;
        }

        $productDataToAutoAdd = [
            'product_id' => $getYProductIds[0],
            'qty' => $productQtyToAutoAdd,
            'trigger_item_ids_qtys' => $triggerItemIdsQtys,
            'gift_message' => $this->getGiftMessageForItem($productQtyToAutoAdd, $productQtyToAutoAdd, $discountExpression)
        ];

        if(isset($buyXItem)){
            $buyXItemAutoAddData = $buyXItem->getAutoAddProductPerRule();
            if(!is_array($buyXItemAutoAddData)){
                $buyXItemAutoAddData = [];
            }
            $buyXItemAutoAddData[$rule->getId()] = $productDataToAutoAdd;
            $buyXItem->setAutoAddProductPerRule($buyXItemAutoAddData);
        }else{
            $quoteAutoAddData = $quote->getAutoAddProductPerRule();
            if(!is_array($quoteAutoAddData)){
                $quoteAutoAddData = [];
            }
            $quoteAutoAddData[$rule->getId()] = $productDataToAutoAdd;
            $quote->setAutoAddProductPerRule($quoteAutoAddData);
        }
    }


    protected function getGiftMessageForItem($itemDiscountedQty, $itemQtyInCart, $discountExpression){
        return sprintf(
            "You've got %s%s %s!",
            (($itemQtyInCart != $itemDiscountedQty) || ($itemQtyInCart > 1) ? ($itemDiscountedQty == 1 ? "one" : $itemDiscountedQty)." of " : ""),
            (($itemQtyInCart != $itemDiscountedQty) || ($itemQtyInCart > 1) ? " those" : "this"),
            (($itemDiscountedQty == 1) || ($this->getDiscountType() == self::DISCOUNT_TYPE_PERCENT) ? $discountExpression : $discountExpression." each")
        );
    }

    protected function setQtysToReducePerAutoAddedGetYItem($unfilledGetYItemsData, $rule){
        foreach($unfilledGetYItemsData as $unfilledGetYItemData){
            $unfilledGetYItem = $unfilledGetYItemData['item'];

            $buyRequest = $unfilledGetYItem->getBuyRequest();
            if(!$buyRequest->getIsAutoAdd() || !$buyRequest->getAutoAddRuleId()
                || ($buyRequest->getAutoAddRuleId() != $rule->getId())){
                continue;
            }
            if($unfilledGetYItemData['qty'] > 0){
               $unfilledGetYItem->setQtyToReduce($unfilledGetYItem->getQtyToReduce() + $unfilledGetYItemData['qty']);
            }
        }
    }

    protected function _getHintMessageForTriggeringItem(
        $yHintsSingular, $yHintsPlural, $getYItemQtyPerGroupYouCanGetNow, $discountExpression
    )
    {
        $triggerItemsHintMessage = "Add ";
        $triggerItemsHintMessage .= sprintf(
            "%s%s %s",
            ($getYItemQtyPerGroupYouCanGetNow == 1 ? "one " : $getYItemQtyPerGroupYouCanGetNow." "),
            ($getYItemQtyPerGroupYouCanGetNow == 1 ? $yHintsSingular : $yHintsPlural),
            (($getYItemQtyPerGroupYouCanGetNow == 1) || ($this->getDiscountType() == self::DISCOUNT_TYPE_PERCENT) ? $discountExpression : $discountExpression." each")
        );
        return $triggerItemsHintMessage;
    }

    protected function getItemIdsQtysIndex($buyXItemsData){
        $index = '';
        foreach($buyXItemsData['items'] as $itemData){
            $index .= $this->apHelper->getQuoteItemId($itemData['item'])."_".$itemData['qty'];
        }
        return $index;
    }

    protected function addItemsQtysToBuyXItems($buyXItems1, $buyXItems2){
        $returnBuyXItems = $buyXItems1;

        foreach($returnBuyXItems as $returnBuyXItemKey => $returnBuyXItem){
            foreach($buyXItems2 as $buyXItem2){
                if($this->apHelper->getQuoteItemId($returnBuyXItem['item']) == $this->apHelper->getQuoteItemId($buyXItem2['item'])){
                    $returnBuyXItems[$returnBuyXItemKey]['qty'] += $buyXItem2['qty'];
                }
            }
        }

        return $returnBuyXItems;
    }

    protected function addHintsToItemsWithEmptySlots($getYItemsSlots, $yHintsSingular, $yHintsPlural, $getYDiscountExpression, $rule, $tryBindingToTriggeringItems = true, $quote = null){
        $getYItemsSlotsWithUniqueBuyXGroups = [];
        $itemsAlreadyMatched = [];

        foreach($getYItemsSlots as $getYItemsSlot1){
            $currentBuyXItemsData = (isset($getYItemsSlot1['buy_x_items_data']) ? $getYItemsSlot1['buy_x_items_data'] : null);
            $currentItemIdsQtysIndex = $this->getItemIdsQtysIndex($currentBuyXItemsData);

            if(in_array($currentItemIdsQtysIndex, $itemsAlreadyMatched)){
                continue;
            }

            foreach($getYItemsSlots as $getYItemsSlot2){
                $buyXItemsData = null;
                if(isset($getYItemsSlot2['buy_x_items_data'])){
                    $buyXItemsData = $getYItemsSlot2['buy_x_items_data'];
                }
                if(!$tryBindingToTriggeringItems
                    || ($currentBuyXItemsData && $buyXItemsData && $this->isSameItemsQtys($currentBuyXItemsData, $buyXItemsData))
                    || (!$currentBuyXItemsData && !$buyXItemsData)){
                    $itemsAlreadyMatched[] = $currentItemIdsQtysIndex;

                    if(!isset($getYItemsSlotsWithUniqueBuyXGroups[$currentItemIdsQtysIndex])){
                        $getYItemsSlotsWithUniqueBuyXGroups[$currentItemIdsQtysIndex] = [
                            'get_y_items_slots' => [
                                'unfilled_qty' => 0
                            ]
                        ];
                    }

                    if(!isset($getYItemsSlotsWithUniqueBuyXGroups[$currentItemIdsQtysIndex]['buy_x_items_data']['items'])){
                        $getYItemsSlotsWithUniqueBuyXGroups[$currentItemIdsQtysIndex]['buy_x_items_data'] = [
                            'items' => $currentBuyXItemsData['items']
                        ];
                    }else{
                        $getYItemsSlotsWithUniqueBuyXGroups[$currentItemIdsQtysIndex]['buy_x_items_data']['items'] = $this->addItemsQtysToBuyXItems($getYItemsSlotsWithUniqueBuyXGroups[$currentItemIdsQtysIndex]['buy_x_items_data']['items'], $getYItemsSlot2['buy_x_items_data']['items']);
                    }

                    $getYItemsSlotsWithUniqueBuyXGroups[$currentItemIdsQtysIndex]['get_y_items_slots']['unfilled_qty'] += $getYItemsSlot2['get_y_items_slots']['unfilled_qty'];
                }
            }
        }

        foreach($getYItemsSlotsWithUniqueBuyXGroups as $getYItemsSlot){
            if($getYItemsSlot['get_y_items_slots']['unfilled_qty'] > 0){

                $giftTriggerItemIdsQtysOfSameGroup = [];
                $giftHintMessage = $this->_getHintMessageForTriggeringItem($yHintsSingular, $yHintsPlural, $getYItemsSlot['get_y_items_slots']['unfilled_qty'], $getYDiscountExpression);

                $giftQtysCanAddPerGroup = array_fill(1, count($this->getNonEmptyGroupNumbers()), 0);
                $giftQtysCanAddPerGroup[$this->getGetYGroupNumber()] = $getYItemsSlot['get_y_items_slots']['unfilled_qty'];

                if($tryBindingToTriggeringItems){
                    foreach($getYItemsSlot['buy_x_items_data']['items'] as $buyXItemData){
                        $buyXItem = $buyXItemData['item'];
                        $buyXItemId = $this->apHelper->getQuoteItemId($buyXItem);
                        $giftTriggerItemIdsQtysOfSameGroup[$buyXItemId] = $buyXItemData['qty'];
                    }

                    $firstBuyXItem = $getYItemsSlot['buy_x_items_data']['items'][0]['item'];
                    $this->apHelper->addCartHintDataToItem($rule->getId(), $giftHintMessage, $firstBuyXItem);


                    $firstBuyXItem->setGiftQtysCanAddPerGroup([$rule->getId() => $giftQtysCanAddPerGroup]);
                    $firstBuyXItem->setGiftTriggerItemIdsQtysOfSameGroup([$rule->getId() => $giftTriggerItemIdsQtysOfSameGroup]);
                }else{
                    if(isset($quote)){
                        $this->apHelper->addGiftCartHintData($rule->getId(), $giftHintMessage, $quote);

                        $quote->setGiftQtysCanAddPerGroup([$rule->getId() => $giftQtysCanAddPerGroup]);
                    }
                }
            }
        }
    }

    protected function isSameItemsQtys($itemsData1, $itemsData2){
        foreach($itemsData1['items'] as $itemData1){
            $matchingItemFound = false;
            foreach($itemsData2['items'] as $itemData2){
                if($this->apHelper->getQuoteItemId($itemData1['item']) != $this->apHelper->getQuoteItemId($itemData2['item'])){
                    continue;
                }
                if($itemData1['qty'] != $itemData2['qty']){
                    return false;
                }
                $matchingItemFound = true;
                break;
            }
            if(!$matchingItemFound){
                return false;
            }
        }
        return true;
    }

    protected function setGiftItemData($getYItemsSlots, $rule, $discountExpression){
        $uniqueGetYItems = [];
        foreach($getYItemsSlots as $getYItemsSlot){
            $triggerItemIds = [];

            if(isset($getYItemsSlot['get_y_items_slots']['items'])){
                if(isset($getYItemsSlot['buy_x_items_data']['items'])){
                    foreach($getYItemsSlot['buy_x_items_data']['items'] as $buyXItemData){
                        $buyXItem = $buyXItemData['item'];
                        $triggerItemIds[$this->apHelper->getQuoteItemId($buyXItem)] = $buyXItemData['qty'];
                    }
                }

                foreach($getYItemsSlot['get_y_items_slots']['items'] as $getYItemData){
                    $getYItem = $getYItemData['item'];

                    if(!isset($uniqueGetYItems[$this->apHelper->getQuoteItemId($getYItem)])){
                        $uniqueGetYItems[$this->apHelper->getQuoteItemId($getYItem)] = [
                            'item' => $getYItem,
                            'gift_qty' => 0,
                            'trigger_item_ids_qtys' => []
                        ];
                    }
                    $uniqueGetYItems[$this->apHelper->getQuoteItemId($getYItem)]['gift_qty'] += $getYItemData['qty'];
                    $uniqueGetYItems[$this->apHelper->getQuoteItemId($getYItem)]['trigger_item_ids_qtys'] += $triggerItemIds;

                    $getYItem->setGiftRuleId($rule->getId());
                    $getYItem->setGiftMessage($this->getGiftMessageForItem($getYItemData['qty'], $getYItem->getQty(), $discountExpression));
                    $getYItem->setGiftTriggerItemIdsQtys($triggerItemIds);
                }

                foreach($uniqueGetYItems as $getYItemData){
                    $getYItem = $getYItemData['item'];
                    $getYItem->setGiftRuleId($rule->getId());
                    $getYItem->setGiftMessage($this->getGiftMessageForItem($getYItemData['gift_qty'], $getYItem->getQty(), $discountExpression));
                    $getYItem->setGiftTriggerItemIdsQtys($getYItemData['trigger_item_ids_qtys']);
                }
            }
        }
    }

    protected function fillExistingGetYProductsIntoSlotsAndSetGiftItemData($getYItemsSlots, $getYItemsData){
        $unfilledGetYItemsData = [];

//        $getYItemsWithoutPossibleTriggerItems = [];
        $filledInQtysPerGetYItemIds = [];

        foreach($getYItemsData as $getYBuyXItemData){
            foreach($getYBuyXItemData['items'] as $getYItemData){
                $getYItem = $getYItemData['item'];

                $getYItemTotalQty = $getYItem->getQty();
                $getYItemAvailableQtyForCurrentFilling = $getYItemData['qty'];

                $buyRequest = $getYItem->getBuyRequest();
                $possibleTriggerItemIdsQtysGroup = ($buyRequest ? $buyRequest->getPossibleGiftTriggerItemIdsQtys() : null);

                if(!isset($filledInQtysPerGetYItemIds[$this->apHelper->getQuoteItemId($getYItem)])){
                    $filledInQtysPerGetYItemIds[$this->apHelper->getQuoteItemId($getYItem)] = [
                        'total_original_qty' => $getYItemTotalQty,
                        'total_filled_qty' => 0,
                        'item' => $getYItem
                    ];
                }

                if($possibleTriggerItemIdsQtysGroup){
                    $fillInResult = $this->fillInGetYItemInEmptySlotsOfPossibleTriggerItems($getYItemsSlots, $possibleTriggerItemIdsQtysGroup, $getYItem, $getYItemAvailableQtyForCurrentFilling);

                    $getYItemsSlots = $fillInResult[0];
                    $filledInQty = $fillInResult[1];

                    $filledInQtysPerGetYItemIds[$this->apHelper->getQuoteItemId($getYItem)]['total_filled_qty'] += $filledInQty;
                }
            }
        }

        foreach($filledInQtysPerGetYItemIds as $itemData){
            $getYItem = $itemData['item'];
            if($itemData['total_filled_qty'] < $itemData['total_original_qty']){
                $fillInResult = $this->fillInGetYItemInEmptySlotsOfAnyTriggerItems(
                    $getYItemsSlots,
                    $getYItem,
                    ($itemData['total_original_qty'] - $itemData['total_filled_qty'])
                );

                $getYItemsSlots = $fillInResult[0];
                $filledInQty = $fillInResult[1];

                if(($filledInQty + $itemData['total_filled_qty']) < $itemData['total_original_qty']){
                    $unfilledGetYItemsData[] = [
                        'item' => $getYItem,
                        'qty' => ($itemData['total_original_qty'] - ($filledInQty + $itemData['total_filled_qty']))
                    ];
                }
            }
        }

        return [
            'unfilled_get_y_items_data' => $unfilledGetYItemsData,
            'get_y_items_slots' => $getYItemsSlots
        ];
    }

    /**
     * @param $unfilledGetYItemsData
     * @param \Magento\Quote\Model\Quote $quote
     */
    protected function setAutoAddedItemsToRemoveFlag($unfilledAutoAddedItemsData){
        foreach($unfilledAutoAddedItemsData as $unfilledAutoAddedItem){
            $buyRequest = $unfilledAutoAddedItem->getBuyRequest();
            if(!$buyRequest->getIsAutoAdd()){
                continue;
            }
            $unfilledAutoAddedItem->setAutoAddedToRemove(true);
        }
    }

    protected function getUnfilledAutoAddedItems($getYItemsSlots, $quote, $rule){
        $unfilledAutoAddedItems =  [];

        foreach($quote->getAllItems() as $item){
            $buyRequest = $item->getBuyRequest();
            if(!$buyRequest->getIsAutoAdd() || !$buyRequest->getAutoAddRuleId()
                || ($buyRequest->getAutoAddRuleId() != $rule->getId())){
                continue;
            }
            $itemFoundInSlots = false;
            foreach($getYItemsSlots as $getYItemsSlotData){
                if(!isset($getYItemsSlotData['get_y_items_slots']['items'])){
                    break;
                }
                foreach($getYItemsSlotData['get_y_items_slots']['items'] as $getYItemData){
                    $getYItem = $getYItemData['item'];
                    if($this->apHelper->getQuoteItemId($getYItem) == $this->apHelper->getQuoteItemId($item)){
                        $itemFoundInSlots = true;
                        break 2;
                    }
                }
            }
            if(!$itemFoundInSlots){
                $unfilledAutoAddedItems[] = $item;
            }
        }

        return $unfilledAutoAddedItems;
    }


    /**
     * @param \Magento\Quote\Model\Quote $quote
     */
    public function getAlreadyAddedMatchingRuleItemsWithDisplayData($quote){
        $matchingItemsDataPerGroups = [];
        $nonEmptyGroupNumbers = $this->getNonEmptyGroupNumbers();
        foreach($quote->getAllVisibleItems() as $item){
            foreach($nonEmptyGroupNumbers as $groupNumber){
                $groupProductIds = $this->apHelper->getRuleGroupPromoBlockProductIds($this->getRule(), $groupNumber);
                if(in_array($item->getProduct()->getId(), $groupProductIds)){
                    if(!isset($matchingItemsDataPerGroups[$groupNumber])){
                        $matchingItemsDataPerGroups[$groupNumber] = [
                            'products' => [],
                            'products_qty' => 0,
                        ];
                    }

                    $productImage = $item->getProduct()->getData('image');
                    if(!$productImage){
                        $productImage = $this->productFactory->create()
                            ->load($item->getProduct()->getId(), 'image')
                            ->getData('image');
                    }
                    $matchingItemsDataPerGroups[$groupNumber]['products'][] = [
                        'item' => $item,
                        'title' => $item->getProduct()->getName(),
                        'image' => $this->apHelper->getFullProductImageUrl($productImage),
                        'qty' => $item->getQty()
                    ];
                    $matchingItemsDataPerGroups[$groupNumber]['products_qty'] += $item->getQty();

                    continue 2;
                }
            }
        }
        return $matchingItemsDataPerGroups;
    }

    public function getIsSomeProductsHasOptions(){
        /**
         * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
         */
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->getSelect()
            ->join(
                ['a' => $this->resource->getTableName('apactionrule_product')],
                "e.entity_id = a.product_id"
            )->where(
                sprintf(
                    '(rule_id = "%s") AND (e.type_id = "%s" OR product_has_custom_options = 1)',
                    $this->getRule()->getId(),
                    \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
                )
            )->limit(1);
        if($productCollection->getSize() == 0){
            return false;
        }
        return true;
    }

    public function getIsRuleHasSelections(){
        foreach($this->getNonEmptyGroupNumbers() as $groupNumber){
            if($this->apRuleProductResourceFactory->create()->getNumberOfProductsPerRuleAndGroup($this->getRule()->getId(), $groupNumber) > 1){
                return true;
            }
        }
        return false;
    }

    public function getGroupNumberOfProductInRule($productId, $ruleId){
        $query = $this->resource->getConnection()->select()
            ->from(['a' => $this->resource->getTableName('apactionrule_product')], ['group_number'])
            ->where(
                sprintf(
                    '(rule_id = "%s") AND (product_id = "%s")',
                    $ruleId,
                    $productId
                )
            );
        $cols =  $this->resource->getConnection()->fetchCol($query);
        if(isset($cols[0])){
            return $cols[0];
        }
        return null;
    }

    public function getEstimatedProductPriceAfterDiscount($productPriceBeforeDiscount, $sumOfOnlyProductPricesOfAllGroups){
        if($this->getDiscountType() == self::DISCOUNT_TYPE_PERCENT){
            return max(0, $productPriceBeforeDiscount*((100-$this->getDiscountAmountValue())/100));
        }elseif($this->getDiscountType() == self::DISCOUNT_TYPE_FIXED){
            return max(0, $productPriceBeforeDiscount - $this->getDiscountAmountValue()*($productPriceBeforeDiscount/$sumOfOnlyProductPricesOfAllGroups));
        }elseif($this->getDiscountType() == self::DISCOUNT_TYPE_FIXED_PRICE){
            return max(0, $this->getDiscountAmountValue()*($productPriceBeforeDiscount/$sumOfOnlyProductPricesOfAllGroups));
        }
        return $productPriceBeforeDiscount;
    }

    public function getCheapestProductPriceForGroup($groupNumber){
        return $this->getProductPriceForGroup($groupNumber, self::DISCOUNT_PRICE_TYPE_CHEAPEST);
    }

    public function getMostExpensiveProductPriceForGroup($groupNumber){
        return $this->getProductPriceForGroup($groupNumber, self::DISCOUNT_PRICE_TYPE_MOST_EXPENSIVE);
    }

    public function getProductPriceForGroup($groupNumber, $priceType=self::DISCOUNT_PRICE_TYPE_CHEAPEST){
        /**
         * @var \Mexbs\ApBase\Model\ResourceModel\RuleAvailableProductsIndex\Collection $ruleAvailProdsIndexGroupsColl
         */
        $ruleAvailProdsIndexGroupsColl = $this->ruleAvailProdsIndexCollFactory->create();
        $ruleAvailProdsIndexGroupsColl->addFieldToFilter('rule_id', $this->getRule()->getId())
            ->addFieldToFilter('group_number', $groupNumber)
            ->getSelect()->joinInner(["price_index" => $this->resource->getTableName('catalog_product_index_price')], "product_id=price_index.entity_id", ["final_price"])
            ->order("final_price ".($priceType==self::DISCOUNT_PRICE_TYPE_CHEAPEST ? \Magento\Framework\DB\MapperInterface::SORT_ORDER_ASC : \Magento\Framework\DB\MapperInterface::SORT_ORDER_DESC))
            ->limit(1);


        $productPrice = $ruleAvailProdsIndexGroupsColl->getFirstItem()->getFinalPrice();
        if(!$productPrice){
            return null;
        }
        return $productPrice;
    }

    public function getIsAllGroupsHaveOneProduct(){
        $select = $this->connection->select()
            ->from($this->resource->getTableName('apactionrule_product'), ["products_count" => new \Zend_Db_Expr("count(*)")])
            ->where(sprintf("rule_id='%s'", $this->getRule()->getId()))
            ->group("group_number");
        $productsPerGroup =  $this->connection->fetchCol($select);
        foreach($productsPerGroup as $productsInGroup){
            if($productsInGroup > 1){
                return false;
            }
        }
        return true;
    }

    public function getPromoProductGroupsWithDisplayData($currentProductId = null){
        /**
         * @var \Mexbs\ApBase\Model\ResourceModel\RuleAvailableProductsIndex\Collection $ruleAvailProdsIndexGroupsColl
         */
        $ruleAvailProdsIndexGroupsColl = $this->ruleAvailProdsIndexCollFactory->create();
        $ruleAvailProdsIndexGroupsColl->addFieldToFilter('rule_id', $this->getRule()->getId())
            ->getSelect()->group(["rule_id", "group_number"]);

        $nonEmptyDisplayableGroupNumbers = array_intersect($this->getGroupNumbersDisplayableInPopupAdd(), $this->getNonEmptyGroupNumbers());
        if(count($nonEmptyDisplayableGroupNumbers) > count($ruleAvailProdsIndexGroupsColl)){
            return [];
        }

        $currentProductGroupNumber = null;
        if($currentProductId){
            $currentProductGroupNumber = $this->getGroupNumberOfProductInRule($currentProductId, $this->getRule()->getId());
        }

        $promoProductGroups = [];

        $sumOfCheapestProductPricesOfAllGroups = 0;
        $sumOfMostExpensiveProductPricesOfAllGroups = 0;

        foreach($ruleAvailProdsIndexGroupsColl as $ruleAvailProdGroup){
            $groupNumber = $ruleAvailProdGroup->getGroupNumber();

            /**
             * @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
             */
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->getSelect()
                ->join(
                    ['a' => $this->resource->getTableName('apactionrule_product')],
                    "e.entity_id = a.product_id"
                )->where(
                    sprintf(
                        '(rule_id = "%s") AND (group_number = "%s")',
                        $this->getRule()->getId(),
                        $groupNumber
                    )
                );
            $productCollection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
            $productCollection->addAttributeToSelect('short_description');


            if($productCollection->getSize() == 0){
                return [];
            }

            if(!isset($promoProductGroups[$groupNumber])){
                $promoProductGroups[$groupNumber] = [];
            }

            $promoProductGroups[$groupNumber]['title_singular'] = $this->getGroupTitleSingular($groupNumber);
            $promoProductGroups[$groupNumber]['title_plural'] = $this->getGroupTitlePlural($groupNumber);
            $promoProductGroups[$groupNumber]['qty'] = $this->getGroupQty($groupNumber);

            $productCollection->addAttributeToSelect('image')
                ->addAttributeToSelect('name');

            if($currentProductId && $currentProductGroupNumber && ($currentProductGroupNumber == $groupNumber)){
                $shortProductImageUrl = $this->apHelper->getProductImage($currentProductId);
            }else{
                $shortProductImageUrl = $productCollection
                    ->getFirstItem()
                    ->getImage();
            }

            $promoProductGroups[$groupNumber]['image'] = $this->apHelper->getFullProductImageUrl($shortProductImageUrl);
            $promoProductGroups[$groupNumber]['single_product_to_select'] = ($productCollection->getSize() == 1 ? "1" : "0");
            $promoProductGroups[$groupNumber]['first_product_id'] = $productCollection->getFirstItem()->getId();
            $promoProductGroups[$groupNumber]['first_product'] = $productCollection->getFirstItem();

            $promoProductGroups[$groupNumber]['cheapest_product_price_before_discount'] = $promoProductGroups[$groupNumber]['qty']*$this->getCheapestProductPriceForGroup($groupNumber);
            $sumOfCheapestProductPricesOfAllGroups += $promoProductGroups[$groupNumber]['cheapest_product_price_before_discount'];

            $promoProductGroups[$groupNumber]['most_expensive_product_price_before_discount'] = $promoProductGroups[$groupNumber]['qty']*$this->getMostExpensiveProductPriceForGroup($groupNumber);
            $sumOfMostExpensiveProductPricesOfAllGroups += $promoProductGroups[$groupNumber]['most_expensive_product_price_before_discount'];

            if($productCollection->getSize() == 1){
                $promoProductGroups[$groupNumber]['first_product'] = $this->productFactory->create()->load($productCollection->getFirstItem()->getId());
            }

            $productCollection->clear()->addOptionsToResult();
            $promoProductGroups[$groupNumber]['products'] = $productCollection;
        }

        $promoProductGroups['sum_of_cheapest_product_prices_before_discount'] = $sumOfCheapestProductPricesOfAllGroups;
        $promoProductGroups['sum_of_cheapest_product_prices_after_discount'] = $this->getEstimatedProductPriceAfterDiscount($sumOfCheapestProductPricesOfAllGroups, $sumOfCheapestProductPricesOfAllGroups);
        $promoProductGroups['sum_of_most_expensive_product_prices_before_discount'] = $sumOfMostExpensiveProductPricesOfAllGroups;
        $promoProductGroups['sum_of_most_expensive_product_prices_after_discount'] = $this->getEstimatedProductPriceAfterDiscount($sumOfMostExpensiveProductPricesOfAllGroups, $sumOfMostExpensiveProductPricesOfAllGroups);

        foreach($promoProductGroups as $promoProductGroupKey => $promoProductGroupData){
            if(is_numeric($promoProductGroupKey)){
                if(isset($promoProductGroups['sum_of_cheapest_product_prices_before_discount'])
                    && $promoProductGroups['sum_of_cheapest_product_prices_before_discount']){
                    $promoProductGroups[$promoProductGroupKey]['cheapest_product_price_after_discount'] = $this->getEstimatedProductPriceAfterDiscount($promoProductGroupData['cheapest_product_price_before_discount'], $promoProductGroups['sum_of_cheapest_product_prices_before_discount']);
                }
                if(isset($promoProductGroups['sum_of_most_expensive_product_prices_before_discount'])
                    && $promoProductGroups['sum_of_most_expensive_product_prices_before_discount']){
                    $promoProductGroups[$promoProductGroupKey]['most_expensive_product_price_after_discount'] = $this->getEstimatedProductPriceAfterDiscount($promoProductGroupData['most_expensive_product_price_before_discount'], $promoProductGroups['sum_of_most_expensive_product_prices_before_discount']);
                }
            }
        }

        return $promoProductGroups;
    }

    protected function _getItemToCheckTierOrSpecialPrice($item){
        $itemProductToCheckTierOrSpecialPrice = $item->getProduct();
        if($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            $itemChildren = $item->getChildren();
            if(count($itemChildren) > 0
                && isset($itemChildren[0])){
                $itemToCheckTierOrSpecialPrice = $itemChildren[0];
                if($itemToCheckTierOrSpecialPrice->getProduct()){
                    $itemProductToCheckTierOrSpecialPrice = $itemToCheckTierOrSpecialPrice->getProduct();
                }
            }
        }
        return $itemProductToCheckTierOrSpecialPrice;
    }

    protected function _getPriceTypeFlags($item, $itemProduct){
        if(!$this->apHelper->getApPriceTypeFlagsForItem($item)){
            $this->_setPriceTypeFlags($item, $itemProduct);
        }
        return $this->apHelper->getApPriceTypeFlagsForItem($item);
    }

    protected function _setPriceTypeFlags($item, $itemProduct){
        $itemOriginalPrice = $item->getOriginalPrice();

        $tierPrice = $itemProduct->getTierPrice($item->getQty());
        if(!$tierPrice){
            $tierPrice = $itemOriginalPrice;
        }
        $specialPrice = $this->apHelper->getSpecialPrice($itemProduct, $itemOriginalPrice);
        $catalogRulePrice = $this->apHelper->getCatalogRulePrice($itemProduct);


        $isTierPriceApplied = false;
        $isSpecialPriceApplied = false;
        $isCatalogRulePriceApplied = false;

        $productPriceTypes = [$tierPrice, $specialPrice];
        if(is_numeric($catalogRulePrice)){
            $productPriceTypes[] = $catalogRulePrice;
        }

        $lowestPriceType = min($productPriceTypes);

        if($lowestPriceType < $itemOriginalPrice){
            if($lowestPriceType == $tierPrice){
                $isTierPriceApplied = true;
            }elseif(($lowestPriceType == $specialPrice)
                && ($specialPrice != $tierPrice)){
                $isSpecialPriceApplied = true;
            }elseif(is_numeric($catalogRulePrice)
                && ($lowestPriceType == $catalogRulePrice)){
                $isCatalogRulePriceApplied = true;
            }
        }

        $priceTypeFlags = [
            'is_tier_price_applied' => $isTierPriceApplied,
            'is_special_price_applied' => ($isSpecialPriceApplied || $isCatalogRulePriceApplied)
        ];

        $item->setApPriceTypeFlags($priceTypeFlags);
    }

    protected function _isTierPriceAppliedOnItem($item, $itemProduct){
        $apPriceTypeFlags = $this->_getPriceTypeFlags($item, $itemProduct);
        if($apPriceTypeFlags){
            if(isset($apPriceTypeFlags['is_tier_price_applied'])
                && $apPriceTypeFlags['is_tier_price_applied']){
                return true;
            }
        }
        return false;
    }

    protected function _isSpecialPriceAppliedOnItem($item, $itemProduct){
        $apPriceTypeFlags = $this->_getPriceTypeFlags($item, $itemProduct);
        if($apPriceTypeFlags){
            if(isset($apPriceTypeFlags['is_special_price_applied'])
                && $apPriceTypeFlags['is_special_price_applied']){
                return true;
            }
        }
        return false;
    }


    protected function _oneOfPriceTypesAppliedAndShouldSkip($item, $itemProduct, $rule){
        if($this->_isTierPriceAppliedOnItem($item, $itemProduct)
            && $this->apHelper->shouldSkipTierPrice($this->getRule())){
            return true;
        }
        if($this->_isSpecialPriceAppliedOnItem($item, $itemProduct)
            && $this->apHelper->shouldSkipSpecialPrice($this->getRule())){
            return true;
        }
        return false;
    }

    protected function _getItemExpectedPricesArray($item){
        return [
            'price' => $this->apHelper->getItemPrice($item),
            'base_price' => $this->apHelper->getItemBasePrice($item),
            'original_price' => $this->apHelper->getItemOriginalPrice($item),
            'base_original_price' => $this->apHelper->getItemBaseOriginalPrice($item),
        ];
    }


    protected function _getIsDiscountAmountValid($discountAmount, $discountType){
        if(!is_numeric($discountAmount)
            || ($discountAmount <= 0)){
            return false;
        }

        if($discountType == self::DISCOUNT_TYPE_PERCENT){
            if($discountAmount > 100){
                return false;
            }
        }

        return true;
    }

    protected function _getIsDiscountTypeValid($discountType){
        if(!in_array($discountType, $this->discountTypes)){
            return false;
        }
        return true;
    }

    protected function _getDiscountAmountOnItemUnit($itemPrice, $discountType, $discountAmount){
        if($discountType == self::DISCOUNT_TYPE_PERCENT){
            return $itemPrice*($discountAmount/100);
        }elseif($discountType == self::DISCOUNT_TYPE_FIXED){
            return $discountAmount;
        }elseif($discountType == self::DISCOUNT_TYPE_FIXED_PRICE){
            if($discountAmount >= $itemPrice){
                $discountAmount = $itemPrice;
            }
            return $itemPrice-$discountAmount;
        }
        return 0;
    }

    protected function _getItemListCompDesc($itemsQtys){
        $comprehensiveDescription = "";
        $itemsCounter = 0;

        foreach($itemsQtys as $itemData){
            $itemQty = $itemData['qty'];
            $item = $itemData['item'];

            if($itemQty == 1){
                $comprehensiveDescription .=
                    sprintf(
                        "%s",
                        $item->getName()
                    );
            }elseif($itemQty > 1){
                $comprehensiveDescription .=
                    sprintf(
                        "%s of %s",
                        $itemQty,
                        $item->getName()
                    );
            }

            if(count($itemsQtys) > 1){
                if($itemsCounter == (count($itemsQtys)-2)){
                    $comprehensiveDescription .= " and ";
                }elseif($itemsCounter < (count($itemsQtys)-1)){
                    $comprehensiveDescription .= ", ";
                }
            }

            $itemsCounter++;
        }

        return $comprehensiveDescription;
    }

    protected function _getDiscountCompDesc($discountType, $discountAmount){
        $currencySymbol = $this->apHelper->getCurrentCurrencySymbol();

        $comprehensiveDescription = "";
        if($discountType == self::DISCOUNT_TYPE_PERCENT){
            if($discountAmount >= 100){
                $comprehensiveDescription .= "for free";
            }else{
                $comprehensiveDescription .=
                    sprintf(
                        "with %s%% discount",
                        $discountAmount
                    );
            }
        }elseif($this->getDiscountType() == self::DISCOUNT_TYPE_FIXED){
            $comprehensiveDescription .=
                sprintf(
                    "with %s%s discount",
                    $currencySymbol,
                    $discountAmount
                );
        }elseif($this->getDiscountType() == self::DISCOUNT_TYPE_FIXED_PRICE){
            $comprehensiveDescription .=
                sprintf(
                    "for %s%s",
                    $currencySymbol,
                    $discountAmount
                );
        }

        return $comprehensiveDescription;
    }

    protected function _setRuleApComprehensiveDescriptionLines($rule, $description, $address){
        $apDiscountDetails = $address->getApDiscountDetails();
        if(!is_array($apDiscountDetails)){
            $apDiscountDetails = [];
        }
        $apDiscountDetails[$rule->getId()]['comprehensive_description'] = $description;
        $address->setApDiscountDetails($apDiscountDetails);
    }

    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_ruleConditionProd->loadAttributeOptions()->getAttributeOption();
        $pAttributes = [];
        $iAttributes = [];
        foreach ($productAttributes as $code => $label) {
            if (strpos($code, 'quote_item_') === 0) {
                $iAttributes[] = [
                    'value' => 'Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product|' . $code,
                    'label' => $label,
                ];
            } else {
                $pAttributes[] = [
                    'value' => 'Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product|' . $code,
                    'label' => $label,
                ];
            }
        }

        $conditions = \Magento\Rule\Model\Condition\Combine::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product\Combine',
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Cart Item Attribute'), 'value' => $iAttributes],
                ['label' => __('Product Attribute'), 'value' => $pAttributes]
            ]
        );
        return $conditions;
    }

    public function addActionDetail($actionDetail)
    {
        $actionDetail->setRule($this->getRule());
        $actionDetail->setObject($this->getObject());
        $actionDetail->setPrefix($this->getPrefix());
        $actionDetail->setSubPrefix($this->getSubPrefix());

        $actionDetails = $this->getActionDetails();
        $actionDetails[] = $actionDetail;

        if (!$actionDetail->getId()) {
            $actionDetail->setId($this->getId() . '--' . sizeof($actionDetails));
        }

        $this->setData($this->getPrefix(), $actionDetails);
        return $this;
    }

    public function loadArray($arr, $key = 'action_details')
    {
        $this->setAggregator(
            isset($arr['aggregator']) ? $arr['aggregator'] : null
        )->setAggregatorValue(
                isset($arr['aggregator_value']) ? $arr['aggregator_value']  : null
            );

        if (!empty($arr[$key]) && is_array($arr[$key])) {
            foreach ($arr[$key] as $actionDetailArr) {
                try {
                    $actionDetail = $this->_conditionFactory->create($actionDetailArr['type'])->setFormName($this->getFormName());
                    $this->addActionDetail($actionDetail);
                    $actionDetail->loadArray($actionDetailArr, $key);
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
            }
        }
        return $this;
    }

    public function loadValueOptions()
    {
        return $this;
    }

    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '==' => __('is'),
                '!=' => __('is not'),
                '>=' => __('equals or greater than'),
                '<=' => __('equals or less than'),
                '>' => __('greater than'),
                '<' => __('less than'),
                '()' => __('is one of'),
                '!()' => __('is not one of'),
            ]
        );
        return $this;
    }

    public function getValueElementType()
    {
        return 'text';
    }

    public function markMatchingItemsAndGetHint($items, $address){
        $this->logger->addError(sprintf(
            "The code came to markMatchingItems of Combine, the child is  %s.",
            __CLASS__
        ));
    }


    public function getAggregatorElement()
    {
        if ($this->getAggregator() === null) {
            foreach (array_keys($this->getAggregatorOption()) as $key) {
                $this->setAggregator($key);
                break;
            }
        }
        return $this->getForm()->addField(
            $this->getPrefix() . '_' . $this->getSubPrefix() . '__' . $this->getId() . '__aggregator',
            'select',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . ']['.$this->getSubPrefix().'][' . $this->getId() . '][aggregator]',
                'values' => $this->getAggregatorSelectOptions(),
                'value' => $this->getAggregator(),
                'value_name' => $this->getAggregatorName(),
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
                $this->_layout->getBlockSingleton('Magento\Rule\Block\Editable')
            );
    }

    public function getAggregatorValueName()
    {
        if(isset($this->aggregatorValueOptions[$this->getAggregatorValue()])){
            return $this->aggregatorValueOptions[$this->getAggregatorValue()];
        }
        return $this->getAggregatorValue();
    }

    public function getAggregatorValueElement()
    {
        if ($this->getAggregatorValue() === null) {
            foreach (array_keys($this->aggregatorValueOptions) as $key) {
                $this->setAggregatorValue($key);
                break;
            }
        }
        return $this->getForm()->addField(
            $this->getPrefix() . '_' . $this->getSubPrefix() . '__' . $this->getId() . '__aggregator_value',
            'select',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . ']['. $this->getSubPrefix() .'][' . $this->getId() . '][aggregator_value]',
                'values' => $this->aggregatorValueOptions,
                'value' => $this->getAggregatorValue(),
                'value_name' => $this->getAggregatorValueName(),
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
                $this->_layout->getBlockSingleton('Magento\Rule\Block\Editable')
            );
    }

    public function getValueElement()
    {
        $elementParams = [
            'name' => $this->elementName . '[' . $this->getPrefix() . ']['.$this->getSubPrefix().'][' . $this->getId() . '][value]',
            'value' => $this->getValue(),
            'values' => $this->getValueSelectOptions(),
            'value_name' => $this->getValueName(),
            'after_element_html' => $this->getValueAfterElementHtml(),
            'explicit_apply' => $this->getExplicitApply(),
            'data-form-part' => $this->getFormName()
        ];
        if ($this->getInputType() == 'date') {
            // date format intentionally hard-coded
            $elementParams['input_format'] = \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT;
            $elementParams['date_format'] = \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT;
        }
        return $this->getForm()->addField(
            $this->getPrefix() . '_' . $this->getSubPrefix() . '__' . $this->getId() . '__value',
            $this->getValueElementType(),
            $elementParams
        )->setRenderer(
                $this->getValueElementRenderer()
            );
    }


    public function getTypeElement()
    {
        return $this->getForm()->addField(
            $this->getPrefix() . '_' . $this->getSubPrefix() . '__' . $this->getId() . '__type',
            'hidden',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . ']['.$this->getSubPrefix().'][' . $this->getId() . '][type]',
                'value' => $this->getType(),
                'no_span' => true,
                'class' => 'hidden',
                'data-form-part' => $this->getFormName()
            ]
        );
    }

    public function getNewChildElement()
    {
        return $this->getForm()->addField(
            $this->getPrefix() . '_' . $this->getSubPrefix() . '__' . $this->getId() . '__new_child',
            'select',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . ']['.$this->getSubPrefix().'][' . $this->getId() . '][new_child]',
                'values' => $this->getNewChildSelectOptions(),
                'value_name' => $this->getNewChildName(),
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
                $this->_layout->getBlockSingleton('Magento\Rule\Block\Newchild')
            );
    }

    public function asArray(array $arrAttributes = [])
    {
        $out['aggregator'] = $this->getAggregator();
        $out['aggregator_value'] = $this->getAggregatorValue();
        $out['type'] = $this->getType();

        foreach ($this->getActionDetails() as $actionDetail) {
            $out['action_details'][] = $actionDetail->asArray();
        }

        return $out;
    }

    public function getDiscountPriceAttributeSelectOptions()
    {
        $opt = [];
        foreach ($this->discountPriceTypeOptions as $key => $value) {
            $opt[] = ['value' => $key, 'label' => $value];
        }
        return $opt;
    }

    public function getDiscountPriceTypeAttributeName()
    {
        return $this->discountPriceTypeOptions[$this->getDiscountPriceType()]->getText();
    }

    public function getDiscountPriceTypeAttributeElement()
    {
        if (null === $this->getDiscountPriceType()) {
            foreach (array_keys($this->discountPriceTypeOptions) as $option) {
                $this->setDiscountPriceType($option);
                break;
            }
        }
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__discount_price_type',
            'select',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][discount_price_type]',
                'values' => $this->getDiscountPriceAttributeSelectOptions(),
                'value' => $this->getDiscountPriceType(),
                'value_name' => $this->getDiscountPriceTypeAttributeName(),
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
                $this->_layout->getBlockSingleton('Magento\Rule\Block\Editable')
            );
    }

    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . __(
                'If %1 of these conditions are %2:',
                $this->getAggregatorElement()->getHtml(),
                $this->getAggregatorValueElement()->getHtml()
            );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }
        return $html;
    }

    public function asHtmlRecursive()
    {
        $html = $this->asHtml() .
            '<ul id="' .
            $this->getPrefix() .
            '_' .
            $this->getSubPrefix() .
            '__' .
            $this->getId() .
            '__children" class="rule-param-children">';
        foreach($this->getActionDetails() as $actionDetail){
            $html .= '<li>' . $actionDetail->asHtmlRecursive() . '</li>';
        }
        $html .= '<li>' . $this->getNewChildElement()->getHtml() . '</li></ul>';
        return $html;
    }

    protected function _isValid($entity)
    {
        if (!$this->getConditions()) {
            return true;
        }

        $all = $this->getAggregator() === 'all';
        $true = (bool)$this->getAggregatorValue();

        foreach ($this->getConditions() as $cond) {
            if ($entity instanceof \Magento\Framework\Model\AbstractModel) {
                $validated = $cond->validate($entity);
            } else {
                $validated = $cond->validateByEntityId($entity);
            }
            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }
        return $all ? true : false;
    }

    protected function _getItemsNamesQtysWithoutZeroQtys($itemsNamesQtys){
        $itemsNamesQtysWithoutZeroQtys = [];
        foreach($itemsNamesQtys as $itemNameQty){
            if($itemNameQty['qty'] > 0){
                $itemsNamesQtysWithoutZeroQtys[] = $itemNameQty;
            }
        }
        return $itemsNamesQtysWithoutZeroQtys;
    }

    protected function _getAlreadyDiscountedItemsExpr(
        $alreadyDiscountedItemNameSingular,
        $alreadyDiscountedItemNamePlural,
        $alreadyDiscountedItemQty,
        $discountExpr
    ){
        if($alreadyDiscountedItemQty > 0){
            return sprintf(
                "You've got %s %s %s",
                ($alreadyDiscountedItemQty == 1 ? "one" : $alreadyDiscountedItemQty),
                ($alreadyDiscountedItemQty == 1 ? $alreadyDiscountedItemNameSingular : $alreadyDiscountedItemNamePlural),
                $discountExpr
            );
        }
        return "";
    }

    protected function _getConditionFulfilmentExpr($itemsNamesQtysLeftToAddUntilDiscount, $isContinuationOfHint = true){
        $conditionFulfilmentExpr = "";
        $itemsNamesQtysLeftToAddUntilDiscountNoZeroQtys = $this->_getItemsNamesQtysWithoutZeroQtys($itemsNamesQtysLeftToAddUntilDiscount);
        if(count($itemsNamesQtysLeftToAddUntilDiscountNoZeroQtys)){
            if($isContinuationOfHint){
                $conditionFulfilmentExpr .= " ";
            }

            $conditionFulfilmentExpr .= "Add ";

            $index = 0;
            $arrayLength = count($itemsNamesQtysLeftToAddUntilDiscountNoZeroQtys);
            foreach($itemsNamesQtysLeftToAddUntilDiscountNoZeroQtys as $itemsNameQtyLeftToAddUntilDiscount){
                if($arrayLength > 1){
                    if($index == ($arrayLength-1)){
                        $conditionFulfilmentExpr .= " and ";
                    }elseif($index != 0){
                        $conditionFulfilmentExpr .= ", ";
                    }
                }
                $conditionFulfilmentExpr .= sprintf(
                    "%s more %s",
                    ($itemsNameQtyLeftToAddUntilDiscount['qty'] == 1 ? "one" : $itemsNameQtyLeftToAddUntilDiscount['qty']),
                    ($itemsNameQtyLeftToAddUntilDiscount['qty'] == 1 ? $itemsNameQtyLeftToAddUntilDiscount['hints_singular'] : $itemsNameQtyLeftToAddUntilDiscount['hints_plural'])
                );

                $index++;
            }
        }
        return $conditionFulfilmentExpr;
    }
}