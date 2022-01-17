<?php
namespace Mexbs\ApBase\Model\Rule\Action\Details;

abstract class GetEachNAbstract extends \Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product\Combine{
    abstract public function getDiscountType();
    abstract public function isEachN();
    abstract public function isDiscountPriceTypeApplicable();
    abstract public function isDiscountOrderTypeApplicable();
    abstract public function isLimitApplicable();
    abstract public function isDiscountQtyApplicable();
    abstract public function isOrderNumberApplicable();
    abstract public function isCheapest();


    public function getGroupNumbersDisplayableInPopupAdd(){
        return ['0'];
    }

    public function getGetYGroupNumber(){
        return 0;
    }

    public function getGroupTitleSingular($groupNumber){
        return $this->getNHintsSingular();
    }

    public function getGroupTitlePlural($groupNumber){
        return $this->getNHintsPlural();
    }

    public function hasAddressConditionsInActionInAnyGroup(){
        return $this->getEachNActionDetails()->hasAddressConditionsInAction();
    }


    public function getNonEmptyGroups(){
        $nonEmptyGroups = [];

        if(is_numeric($this->getNNumber())
            && ($this->getNNumber() > 0)
            && is_numeric($this->getMNumber())
            && ($this->getMNumber() > 0)){
            $eachNActionDetails = $this->getEachNActionDetails();
            if($eachNActionDetails){
                $eachNActionDetail = $eachNActionDetails->getActionDetails();
                if(isset($eachNActionDetail[0])){
                    $nonEmptyGroups[] = $eachNActionDetails;
                }
            }
        }
        return $nonEmptyGroups;
    }

    public function getNonEmptyGroupNumbers(){
        if(is_numeric($this->getNNumber())
            && ($this->getNNumber() > 0)
            && is_numeric($this->getMNumber())
            && ($this->getMNumber() > 0)){
            $eachNActionDetails = $this->getEachNActionDetails();
            if($eachNActionDetails){
                $eachNActionDetail = $eachNActionDetails->getActionDetails();
                if(isset($eachNActionDetail[0])){
                    return [0];
                }
            }
        }
        return [];
    }

    public function getRequiredGroupsForRule(){
        return [0];
    }

    public function getGroupNumbersToIndex(){
        return [0];
    }

    public function getGroupNumbersToActionType(){
        return [
            0 => 'buy'
        ];
    }

    public function getRuleActionType(){
        return "eachn";
    }

    public function getProductGroupActionTypeByNumber($groupNumber){
        return "buy";
    }

    protected function _getDiscountExpression($discountAmount){
        $currencySymbol = $this->apHelper->getCurrentCurrencySymbol();
        $expression = "";

        if($this->getDiscountType() == self::DISCOUNT_TYPE_PERCENT){
            if($discountAmount == 100){
                $expression = __("for free");
            }else{
                $expression = __("with %1% discount", $discountAmount);
            }
        }elseif($this->getDiscountType() == self::DISCOUNT_TYPE_FIXED){
            $expression = __("with %1%2 discount", $currencySymbol, $discountAmount);
        }elseif($this->getDiscountType() == self::DISCOUNT_TYPE_FIXED_PRICE){
            $expression = __("for %1%2 only", $currencySymbol, $discountAmount);
        }

        return $expression;
    }

    protected function _getCouponNotValidHintMessage(
        $promoItemNameSingular,
        $promoItemNamePlural,
        $promoItemDiscountAmount,
        $itemQtyToAddToGetDiscount,
        $atLeastOneMatchingProductAdded
    ){
        $discountExpression = $this->_getDiscountExpression($promoItemDiscountAmount);
        $hintMessage = sprintf(
            "Add %s%s %s to cart. Then try applying the coupon again. You should get %s %s %s!",
            (($itemQtyToAddToGetDiscount + 1 == 1) ? "one" : $itemQtyToAddToGetDiscount + 1),
            ($atLeastOneMatchingProductAdded ? " more" : ""),
            (($itemQtyToAddToGetDiscount + 1 == 1) ? $promoItemNameSingular : $promoItemNamePlural),
            (($itemQtyToAddToGetDiscount + 1 == 1) ? "the" : "one"),
            $promoItemNameSingular,
            $discountExpression
        );
        return $hintMessage;
    }

    protected function _getHintMessage(
        $promoItemNameSingular,
        $promoItemNamePlural,
        $promoItemDiscountAmount,
        $alreadyDiscountedItemQty,
        $itemQtyToAddToGetDiscount,
        $itemQtyOneCanGetIfAdds,
        $itemQtyOnCanGetNow,
        $atLeastOneMatchingProductAdded,
        $priceOrder
   ){
        $promoItemNameSingular = __($promoItemNameSingular)->render();
        $promoItemNamePlural = __($promoItemNamePlural)->render();
        $hintMessage = "";
        $discountExpression = $this->_getDiscountExpression($promoItemDiscountAmount);
        if($alreadyDiscountedItemQty > 0){
            if($alreadyDiscountedItemQty == 1 ){
                $hintMessage .= __("You've got one %1 %2.", $promoItemNameSingular, $discountExpression);
            }else{
                $hintMessage .= __("You've got %1 %2 %3.", $alreadyDiscountedItemQty, $promoItemNameSingular, $discountExpression);
            }
        }

        if($hintMessage != ""){
            $hintMessage .= " ";
        }

        if($itemQtyToAddToGetDiscount > 0
            && $itemQtyOneCanGetIfAdds > 0){
            if($itemQtyOneCanGetIfAdds < INF){
                if($itemQtyToAddToGetDiscount == 1){
                   if($atLeastOneMatchingProductAdded){
                       if($itemQtyOneCanGetIfAdds == 1){
                           $hintMessage .= __("Add one more %1, to get the next %2 %3!", $promoItemNameSingular, $promoItemNameSingular, $discountExpression);
                       }else{
                           $hintMessage .= __("Add one more %1, to get the next %2 %3 %4!", $promoItemNameSingular, $itemQtyOneCanGetIfAdds, $promoItemNamePlural, $discountExpression);
                       }
                   }else{
                       if($itemQtyOneCanGetIfAdds == 1){
                           $hintMessage .= __("Add one %1, to get the next %2 %3!", $promoItemNameSingular, $promoItemNameSingular, $discountExpression);
                       }else{
                           $hintMessage .= __("Add one %1, to get the next %2 %3 %4!", $promoItemNameSingular, $itemQtyOneCanGetIfAdds, $promoItemNamePlural, $discountExpression);
                       }
                   }
                }else{
                    if($atLeastOneMatchingProductAdded){
                        if($itemQtyOneCanGetIfAdds == 1){
                            $hintMessage .= __("Add %1 more %2, to get the next %3 %4!", $itemQtyToAddToGetDiscount, $promoItemNameSingular, $promoItemNameSingular, $discountExpression);
                        }else{
                            $hintMessage .= __("Add %1 more %2, to get the next %3 %4 %5!", $itemQtyToAddToGetDiscount , $promoItemNameSingular, $itemQtyOneCanGetIfAdds, $promoItemNamePlural, $discountExpression);
                        }
                    }else{
                        if($itemQtyOneCanGetIfAdds == 1){
                            $hintMessage .= __("Add %1 %2, to get the next %3 %4!", $promoItemNameSingular, $promoItemNameSingular, $discountExpression);
                        }else{
                            $hintMessage .= __("Add %1 %2, to get the next %3 %4 %5!", $promoItemNameSingular, $itemQtyOneCanGetIfAdds, $promoItemNamePlural, $discountExpression);
                        }
                    }
                }
            }else{
                if($itemQtyToAddToGetDiscount == 1){
                    if($atLeastOneMatchingProductAdded){
                        $hintMessage .= __("Add one more %1, to get all of the next %2 %3!", $promoItemNameSingular, $promoItemNameSingular, $discountExpression);
                    }else{
                        $hintMessage .= __("Add one %1, to get all of the next %2 %3!", $promoItemNameSingular, $promoItemNameSingular, $discountExpression);
                    }
                }else{
                    if($atLeastOneMatchingProductAdded){
                        $hintMessage .= __("Add %1 more %2, to get all of the next %3 %4!", $itemQtyToAddToGetDiscount, $promoItemNameSingular, $promoItemNameSingular, $discountExpression);
                    }else{
                        $hintMessage .= __("Add %1 %2, to get all of the next %3 %4!", $itemQtyToAddToGetDiscount, $promoItemNameSingular, $promoItemNameSingular, $discountExpression);
                    }
                }
            }
        }elseif($itemQtyOnCanGetNow > 0){
            if($itemQtyOnCanGetNow < INF){
                if($itemQtyOnCanGetNow == 1){
                    $hintMessage .= __("You can now add another %1 %2!", $promoItemNameSingular, $discountExpression);
                }else{
                    $hintMessage .= __("You can now add another %1 %2 %3!", $itemQtyOnCanGetNow, $promoItemNameSingular, $discountExpression);
                }
            }else{
                if($alreadyDiscountedItemQty > 0){
                    $hintMessage .= __("Add more %1 %2!", $promoItemNamePlural, $discountExpression);
                }else{
                    $hintMessage .= __("You can now add %1 %2!", $promoItemNamePlural, $discountExpression);
                }
            }
        }

        return $hintMessage;
    }

    public function getSumOfAllItemsQties($priceToItem){
        $sumOfQties = 0;
        foreach ($priceToItem as $item){
            $sumOfQties += $item->getQty();
        }
        return $sumOfQties;
    }

    public function markMatchingItemsAndGetHint($items, $address){
        if(!$this->getEachNActionDetails()
            || !$this->getRule()
            || !$this->getRule()->getId()
        ){
            return null;
        }

        if($this->apHelper->getIsOneOfTheItemsMarkedByRule($items, $this->getRule()->getId())){
            return null;
        }

        $discountType = $this->getDiscountType();
        if(!$this->_getIsDiscountTypeValid($discountType)){
            return null;
        }

        $discountAmount = $this->getDiscountAmountValue();
        if(!$this->_getIsDiscountAmountValid($discountAmount, $discountType)){
            return null;
        }

        $eachNActionDetails = $this->getEachNActionDetails()->getActionDetails();
        if(!isset($eachNActionDetails[0])){
            return null;
        }
        $eachNActionDetail = $this->getEachNActionDetails();

        if(!$this->isEachN()){
            if($this->isOrderNumberApplicable()){
                if($this->getOrderNumber() === ""){
                    $nNumber = 0;
                }else{
                    $nNumber = $this->getOrderNumber()-1;
                    if(!is_numeric($nNumber)
                        || $nNumber<0){
                        return null;
                    }
                }
            }else{
                $nNumber = 0;
            }
            $mNumber = 1;
        }else{
            $nNumber = $this->getNNumber();
            if(!is_numeric($nNumber)
                || !$nNumber){
                return null;
            }

            $mNumber = $this->getMNumber();
            if(!is_numeric($mNumber)
                || !$mNumber){
                return null;
            }
        }

        $afterMQty = 0;
        if(
            is_numeric($this->getAfterMQty())
            && $this->getAfterMQty()
            ){
            $afterMQty = $this->getAfterMQty();
        }


        $maxQty = INF;
        if($this->isCheapest()){
            $maxQty = 1;
        }else{
            if($this->isDiscountQtyApplicable()){
                if(is_numeric($this->getRule()->getDiscountQty())
                    && ($this->getRule()->getDiscountQty() > 0)
                ){
                    $maxQty = $this->getRule()->getDiscountQty();
                }
            }elseif($this->isLimitApplicable()){
                if(is_numeric($this->getEachNLimit())
                    && ($this->getEachNLimit() > 0)
                ){
                    $maxQty = $this->getEachNLimit();
                }
            }
        }


        $validEachNPriceToItem = [];
        $validEachNItemsTotalQty = 0;
        foreach($items as $item){
            $itemProductToCheckTierOrSpecialPrice = $this->_getItemToCheckTierOrSpecialPrice($item);
            if($this->_oneOfPriceTypesAppliedAndShouldSkip($item, $itemProductToCheckTierOrSpecialPrice, $this->getRule())){
                continue;
            }
            if($this->apHelper->validateActionDetail($eachNActionDetail, $item)){
                $itemPrice = $this->apHelper->getItemPrice($item);
                $extendedArraySortableKey = strval($itemPrice*10000 + rand(0,9999));
                $validEachNPriceToItem[$extendedArraySortableKey] = $item;
                $validEachNItemsTotalQty += $item->getQty();
            }
        }

        $NHintsSingular = $this->getNHintsSingular();
        $NHintsPlural = $this->getNHintsPlural();

        $hintMessage = null;
        $hintCouponNotValidMessage = null;

        $addCartHints = (
            $this->getRule()->getDisplayCartHints()
            && $NHintsSingular
            && $NHintsPlural
        );

        if($validEachNItemsTotalQty <= $afterMQty
            || ($validEachNItemsTotalQty == 0)){
            $qtyToAddUntilDiscount = $afterMQty - $validEachNItemsTotalQty + $nNumber;
            $itemQtyOnCanGetNow = ($qtyToAddUntilDiscount > 0 ? 0 : ($this->isEachN() ? $mNumber : ($this->isCheapest() ? 1 : INF)));


            if($addCartHints){
                $itemQtyOneCanGetIfAdds = ($qtyToAddUntilDiscount > 0 ? ($this->isEachN() ? $mNumber : ($this->isCheapest() ? 1 : INF)) : 0);

                $hintMessage = $this->_getHintMessage(
                    $NHintsSingular,
                    $NHintsPlural,
                    $discountAmount,
                    0,
                    $qtyToAddUntilDiscount,
                    $itemQtyOneCanGetIfAdds,
                    $itemQtyOnCanGetNow,
                    ($validEachNItemsTotalQty > 0),
                    ($this->isDiscountPriceTypeApplicable() ? $this->getDiscountPriceType() : $this->getRule()->getDiscountOrderType())
                );

                if($this->getRule()->getDisplayCartHintsIfCouponInvalid()){
                    $hintCouponNotValidMessage = $this->_getCouponNotValidHintMessage(
                        $NHintsSingular,
                        $NHintsPlural,
                        $discountAmount,
                        $qtyToAddUntilDiscount,
                        ($validEachNItemsTotalQty > 0)
                    );
                }
            }

            return [
                'cart_hint' => $hintMessage,
                'coupon_not_valid_cart_hint' => $hintCouponNotValidMessage,
                'can_get_discounted_items_now' => ($itemQtyOnCanGetNow > 0)
            ];
        }

        if($this->isDiscountPriceTypeApplicable()){
            if($this->getDiscountPriceType() == self::DISCOUNT_PRICE_TYPE_CHEAPEST ){
                ksort($validEachNPriceToItem);
            }elseif($this->getDiscountPriceType() == self::DISCOUNT_PRICE_TYPE_MOST_EXPENSIVE){
                krsort($validEachNPriceToItem);
            }
        }elseif($this->isDiscountOrderTypeApplicable()){
            if($this->getRule()->getDiscountOrderType() == self::DISCOUNT_PRICE_TYPE_CHEAPEST ){
                ksort($validEachNPriceToItem);
            }elseif($this->getRule()->getDiscountOrderType() == self::DISCOUNT_PRICE_TYPE_MOST_EXPENSIVE){
                krsort($validEachNPriceToItem);
            }
        }


        $maxDiscountAmount = 0;
        if(is_numeric($this->getRule()->getMaxDiscountAmount())
            && ($this->getRule()->getMaxDiscountAmount() > 0)){
            $maxDiscountAmount = $this->getRule()->getMaxDiscountAmount();
        }

        $appliedItemsTotalAmount = 0;
        $appliedItemsTotalQty = 0;
        $fullPricePurchasedQty = 0;

        $eachNItemsQtys = [];

        $affectedProductNames = [];

        $undiscountedRemainderQty = 0;
        $discountedRemainderQty = 0;

        $currentItemIndex = 0;
        $itemsCount = count($validEachNPriceToItem);

        $qtyToAddUntilDiscount = 0;

        $loopExitedBecauseExceededMaxAmount = false;

        $getYItemsSlots = [];
        $lastGetYItemsSlots = [
            'buy_x_items_data' => [
                'items' => [],
                'items_qty' => 0
            ],
            'get_y_items_slots' => [
                'items' => [],
                'unfilled_qty' => $mNumber
            ]
        ];

        $discountMethod = \Mexbs\ApBase\Model\Source\SalesRule\DiscountMethod::SPREADOUT;
        if($this->getRule()->getDiscountMethod()){
            $discountMethod = $this->getRule()->getDiscountMethod();
        }

        $originalNNumber = $nNumber;
        $originalMNumber = $mNumber;
        $qtyThatWillBeAbleToAddUntilDiscount = 0;
        if($discountMethod == \Mexbs\ApBase\Model\Source\SalesRule\DiscountMethod::EDGE){
            $sumOfAllQties = $this->getSumOfAllItemsQties($validEachNPriceToItem);
            $qtiesLeftAfterM = max($sumOfAllQties - $afterMQty, 0);
            if($qtiesLeftAfterM > 0){
                $sequencesInLeftAfterM = floor($qtiesLeftAfterM/($nNumber+$mNumber));
                $remainder = $qtiesLeftAfterM - ($sequencesInLeftAfterM*($nNumber+$mNumber));
                $nInRemainder = min($nNumber, $remainder);
                $mInRemainder = min($remainder-$nInRemainder, $mNumber);

                $nNumber = min($qtiesLeftAfterM, max($nNumber,$nNumber*$sequencesInLeftAfterM+$nInRemainder));
                $mNumber = max(min($qtiesLeftAfterM-$nNumber, $mNumber*$sequencesInLeftAfterM+$mInRemainder), 0);

                $qtyThatWillBeAbleToAddUntilDiscount = $originalNNumber - $nInRemainder;
            }
        }

        foreach($validEachNPriceToItem as $item){
            $currentItemIndex++;

            $discountQtyOnItemFromRemaindersAndCycles = 0;
            $itemPrice = $this->apHelper->getItemPrice($item);
            $maxQtyToDoEachOnItem = $item->getQty();

            if($afterMQty){
                $fullPriceQtyOnItem = min(max($afterMQty - $fullPricePurchasedQty, 0), $item->getQty());
                $fullPricePurchasedQty += $fullPriceQtyOnItem;

                if($fullPriceQtyOnItem > 0){
                    $maxQtyToDoEachOnItem = $item->getQty() - $fullPriceQtyOnItem;
                }else{
                    $maxQtyToDoEachOnItem = $item->getQty();
                }
            }

            if($maxQty != INF){
                $itemsTotalQtyLeftToApply = max($maxQty-$appliedItemsTotalQty, 0);
                $maxQtyForDiscountOnItemWithinQtyLimit = min($maxQtyToDoEachOnItem, $itemsTotalQtyLeftToApply);
            }else{
                $maxQtyForDiscountOnItemWithinQtyLimit = $maxQtyToDoEachOnItem;
            }

            if($this->getDiscountType() == self::DISCOUNT_TYPE_FIXED_PRICE){
                $discountPercentInDecimal = max(1-($discountAmount/$itemPrice), 0);
            }elseif($this->getDiscountType() == self::DISCOUNT_TYPE_FIXED){
                $discountPercentInDecimal = $discountAmount/$itemPrice;
            }elseif($this->getDiscountType() == self::DISCOUNT_TYPE_PERCENT){
                $discountPercentInDecimal = $discountAmount/100;
            }else{
                $discountPercentInDecimal = 0; //@TODO - log error
            }

            if($maxDiscountAmount > 0){
                if(($itemPrice * $discountPercentInDecimal) == 0){
                    $maxQtyForDiscountOnItemWithinAmountLimit = $maxQtyForDiscountOnItemWithinQtyLimit;
                }else{
                    $maxQtyForDiscountOnItemWithinAmountLimit = floor(max($maxDiscountAmount - $appliedItemsTotalAmount, 0) / ($itemPrice * $discountPercentInDecimal));
                }

                $maxQtyForDiscountOnItemWithinLimits = min($maxQtyForDiscountOnItemWithinQtyLimit, $maxQtyForDiscountOnItemWithinAmountLimit);

                if($maxQtyForDiscountOnItemWithinQtyLimit > $maxQtyForDiscountOnItemWithinAmountLimit){
                    $loopExitedBecauseExceededMaxAmount = true;
                }
            }else{
                $maxQtyForDiscountOnItemWithinLimits = $maxQtyForDiscountOnItemWithinQtyLimit;
            }

            if($maxQtyForDiscountOnItemWithinLimits <= 0){
                continue;
            }

            $maxQtyToDoEachOnItemAfterBothRemaindersDeduct = $maxQtyToDoEachOnItem;

            if($undiscountedRemainderQty < $nNumber){
                $qtyDeductedFromItemForRemainderUndiscounted = min(
                    max($nNumber-$undiscountedRemainderQty, 0),
                    $maxQtyToDoEachOnItem
                );

                $undiscountedRemainderQty += $qtyDeductedFromItemForRemainderUndiscounted;

                if($qtyDeductedFromItemForRemainderUndiscounted > 0){
                    $lastGetYItemsSlots['buy_x_items_data']['items'][] = [
                        'item' => $item,
                        'qty' => $qtyDeductedFromItemForRemainderUndiscounted
                    ];
                    $lastGetYItemsSlots['buy_x_items_data']['items_qty'] += $qtyDeductedFromItemForRemainderUndiscounted;
                }

                $maxQtyToDoEachOnItemAfterBothRemaindersDeduct = $maxQtyToDoEachOnItem - $qtyDeductedFromItemForRemainderUndiscounted;
            }

            if(($undiscountedRemainderQty >= $nNumber)
                &&($discountedRemainderQty < $mNumber)){

                if($maxQtyForDiscountOnItemWithinLimits <= $discountQtyOnItemFromRemaindersAndCycles){
                    break;
                }

                $itemQtyDiscountedFromDiscRemainder = min(
                    $maxQtyToDoEachOnItemAfterBothRemaindersDeduct,
                    ($maxQtyForDiscountOnItemWithinLimits - $discountQtyOnItemFromRemaindersAndCycles),
                    max($mNumber - $discountedRemainderQty, 0)
                );
                $discountQtyOnItemFromRemaindersAndCycles += $itemQtyDiscountedFromDiscRemainder;

                $discountedRemainderQty += $itemQtyDiscountedFromDiscRemainder;

                if($itemQtyDiscountedFromDiscRemainder > 0){
                    $lastGetYItemsSlots['get_y_items_slots']['items'][] = [
                        'item' => $item,
                        'qty' => $itemQtyDiscountedFromDiscRemainder
                    ];
                    $lastGetYItemsSlots['get_y_items_slots']['unfilled_qty'] -= $itemQtyDiscountedFromDiscRemainder;
                }

                $maxQtyToDoEachOnItemAfterBothRemaindersDeduct = $maxQtyToDoEachOnItemAfterBothRemaindersDeduct - $itemQtyDiscountedFromDiscRemainder;
            }



            if(($undiscountedRemainderQty >= $nNumber)
                && ($discountedRemainderQty >= $mNumber)
                && (($maxQtyForDiscountOnItemWithinLimits >= $discountQtyOnItemFromRemaindersAndCycles))){
                $undiscountedRemainderQty = 0;
                $discountedRemainderQty = 0;

                $getYItemsSlots[] = $lastGetYItemsSlots;
                $lastGetYItemsSlots = [
                    'buy_x_items_data' => [
                        'items' => [],
                        'items_qty' => 0
                    ],
                    'get_y_items_slots' => [
                        'items' => [],
                        'unfilled_qty' => $mNumber
                    ]
                ];
            }

            $itemQtyDiscountedFromSelfReminder = 0;
            if(($maxQtyToDoEachOnItemAfterBothRemaindersDeduct > 0)
                && ($maxQtyForDiscountOnItemWithinLimits > $discountQtyOnItemFromRemaindersAndCycles)){

                $fullItemQtyCicles = floor($maxQtyToDoEachOnItemAfterBothRemaindersDeduct / ($nNumber + $mNumber));
                $itemQtyCiclesRemainder = $maxQtyToDoEachOnItemAfterBothRemaindersDeduct % ($nNumber + $mNumber);

                $itemQtyDiscountedFromCicles = $fullItemQtyCicles*$mNumber;
                $itemQtyDiscountedFromCiclesRestrictedByMaxQty = min($itemQtyDiscountedFromCicles, ($maxQtyForDiscountOnItemWithinLimits - $discountQtyOnItemFromRemaindersAndCycles));
                $fullItemQtyCiclesRestrictedByMaxQty = ceil($itemQtyDiscountedFromCiclesRestrictedByMaxQty/$mNumber);

                if($fullItemQtyCiclesRestrictedByMaxQty > 0){
                    $lastGetYItemsSlots = [
                        'buy_x_items_data' => [
                            'items' => [],
                            'items_qty' => 0
                        ],
                        'get_y_items_slots' => [
                            'items' => [],
                            'unfilled_qty' => $mNumber
                        ]
                    ];
                    $lastGetYItemsSlots['buy_x_items_data']['items'][] = [
                        'item' => $item,
                        'qty' => $fullItemQtyCiclesRestrictedByMaxQty*$nNumber
                    ];
                    $lastGetYItemsSlots['buy_x_items_data']['items_qty'] = $fullItemQtyCiclesRestrictedByMaxQty*$nNumber;

                    $lastGetYItemsSlots['get_y_items_slots']['items'][] = [
                        'item' => $item,
                        'qty' => $itemQtyDiscountedFromCiclesRestrictedByMaxQty
                    ];
                    $lastGetYItemsSlots['get_y_items_slots']['unfilled_qty'] = 0;
                    $getYItemsSlots[] = $lastGetYItemsSlots;

                    $lastGetYItemsSlots = [
                        'buy_x_items_data' => [
                            'items' => [],
                            'items_qty' => 0
                        ],
                        'get_y_items_slots' => [
                            'items' => [],
                            'unfilled_qty' => $mNumber
                        ]
                    ];
                }

                $discountQtyOnItemFromRemaindersAndCycles += $itemQtyDiscountedFromCiclesRestrictedByMaxQty;

                if($maxQtyForDiscountOnItemWithinLimits > $discountQtyOnItemFromRemaindersAndCycles){
                    $undiscountedRemainderQty = min(
                        $nNumber,
                        $itemQtyCiclesRemainder
                    );

                    $itemQtyDiscountedFromSelfReminder = min(
                        $mNumber,
                        max($itemQtyCiclesRemainder-$undiscountedRemainderQty, 0)
                    );


                    if($undiscountedRemainderQty > 0){
                        $lastGetYItemsSlots['buy_x_items_data']['items'][] = [
                            'item' => $item,
                            'qty' => $undiscountedRemainderQty
                        ];
                        $lastGetYItemsSlots['buy_x_items_data']['items_qty'] = $undiscountedRemainderQty;

                        if($itemQtyDiscountedFromSelfReminder > 0){
                            $lastGetYItemsSlots['get_y_items_slots']['items'][] = [
                                'item' => $item,
                                'qty' => $itemQtyDiscountedFromSelfReminder
                            ];
                            $lastGetYItemsSlots['get_y_items_slots']['unfilled_qty'] = max(0, $mNumber-$itemQtyDiscountedFromSelfReminder);
                        }
                    }


                    $discountedRemainderQty = $itemQtyDiscountedFromSelfReminder;

                    if(($undiscountedRemainderQty >= $nNumber)
                        && ($discountedRemainderQty >= $mNumber)){
                        $undiscountedRemainderQty = 0;
                        $discountedRemainderQty = 0;

                        $getYItemsSlots[] = $lastGetYItemsSlots;
                        $lastGetYItemsSlots = [
                            'buy_x_items_data' => [
                                'items' => [],
                                'items_qty' => 0
                            ],
                            'get_y_items_slots' => [
                                'items' => [],
                                'unfilled_qty' => $mNumber
                            ]
                        ];
                    }
                }

                $discountQtyOnItemFromRemaindersAndCycles += $itemQtyDiscountedFromSelfReminder;
            }


            $qtyToApplyOnItem = min($discountQtyOnItemFromRemaindersAndCycles, $maxQtyForDiscountOnItemWithinLimits);

            if($currentItemIndex == $itemsCount){
                if($maxQtyForDiscountOnItemWithinLimits >= $discountQtyOnItemFromRemaindersAndCycles){
                    if($discountMethod == \Mexbs\ApBase\Model\Source\SalesRule\DiscountMethod::EDGE){
                        $qtyToAddUntilDiscount = $qtyThatWillBeAbleToAddUntilDiscount;
                    }else{
                        if($discountedRemainderQty == 0){
                            $qtyToAddUntilDiscount = $originalNNumber-$undiscountedRemainderQty;
                        }
                    }
                }
            }


            if($qtyToApplyOnItem <= 0){
                continue;
            }

            $appliedItemsTotalQty += $qtyToApplyOnItem;

            $discountAmountOnItemUnit = $this->_getDiscountAmountOnItemUnit($itemPrice, $this->getDiscountType(), $discountAmount);
            $appliedItemsTotalAmount += $discountAmountOnItemUnit*$qtyToApplyOnItem;

            $itemApRuleMatches = $this->apHelper->getApRuleMatchesForItem($item);
            $itemApRuleMatches = (is_array($itemApRuleMatches) ? $itemApRuleMatches : []);

            $itemExpectedPricesArray = $this->_getItemExpectedPricesArray($item);
            $itemApRuleMatches[$this->getRule()->getId()]['apply'] = [
                'qty' => $qtyToApplyOnItem,
                'expected_prices' => $itemExpectedPricesArray
            ];


            $item->setApRuleMatches($itemApRuleMatches);

            $affectedProductNames[] = $item->getName();

            $eachNItemsQtys[] = [
                'qty' => $qtyToApplyOnItem,
                'item' => $item
            ];
        }

        if(($lastGetYItemsSlots['buy_x_items_data']['items_qty'] > 0) &&
            (($lastGetYItemsSlots['buy_x_items_data']['items_qty'] % $nNumber) == 0)){
            $getYItemsSlots[] = $lastGetYItemsSlots;
        }

        if($this->getRule()->getDisplayProductHints()){
            $this->setGiftItemData($getYItemsSlots, $this->getRule(), $this->_getDiscountExpression($discountAmount));

            if(!$loopExitedBecauseExceededMaxAmount){
                if($this->getRule()->getEnableAutoAdd()){
                    $this->setProductsToAutoAdd($getYItemsSlots, $this->getRule(), $discountAmount, $discountType, $address, $this->_getDiscountExpression($discountAmount));
                }
                $this->addHintsToItemsWithEmptySlots(
                    $getYItemsSlots,
                    $NHintsSingular,
                    $NHintsPlural,
                    $this->_getDiscountExpression($discountAmount),
                    $this->getRule(),
                    ($this->apHelper->getProductHintLocation($this->getRule()) == \Mexbs\FreeGift\Model\Source\Config\ProductHintsLocation::LOCATION_CART_TOP ? false : true),
                    $address->getQuote()
                );
            }
        }

        $hideHintsAfterDiscountNumber = $this->getRule()->getHideHintsAfterDiscountNumber();

        $addCartHints = $addCartHints
            && (!$loopExitedBecauseExceededMaxAmount)
            && ($appliedItemsTotalQty < $maxQty)
            && ($hideHintsAfterDiscountNumber == 0 || ($appliedItemsTotalQty < $hideHintsAfterDiscountNumber));


        $itemQtyOnCanGetNow = ($qtyToAddUntilDiscount > 0 ? 0 : ($this->isEachN() ? $originalMNumber-($appliedItemsTotalQty % $originalMNumber) : ($this->isCheapest() ? 1 : INF)));
        if($addCartHints){
            $itemQtyOneCanGetIfAdds = ($qtyToAddUntilDiscount > 0 ? ($this->isEachN() ? $originalMNumber : ($this->isCheapest() ? 1 : INF)) : 0);
            $hintMessage = $this->_getHintMessage(
                $NHintsSingular,
                $NHintsPlural,
                $discountAmount,
                $appliedItemsTotalQty,
                $qtyToAddUntilDiscount,
                $itemQtyOneCanGetIfAdds,
                $itemQtyOnCanGetNow,
                true,
                ($this->isDiscountPriceTypeApplicable() ? $this->getDiscountPriceType() : $this->getRule()->getDiscountOrderType())
            );

            if($this->getRule()->getDisplayCartHintsIfCouponInvalid()
                && ($appliedItemsTotalQty == 0)){
                $hintCouponNotValidMessage = $this->_getCouponNotValidHintMessage(
                    $NHintsSingular,
                    $NHintsPlural,
                    $discountAmount,
                    $qtyToAddUntilDiscount,
                    true
                );
            }
        }

        $itemsListEachNDescription = $this->_getItemListCompDesc($eachNItemsQtys);
        $discountDescription = $this->_getDiscountCompDesc($this->getDiscountType(), $this->getDiscountAmountValue());

        $ruleComprehensiveDescriptionLines = ["Got ".$itemsListEachNDescription." ".$discountDescription];

        $this->_setRuleApComprehensiveDescriptionLines($this->getRule(), $ruleComprehensiveDescriptionLines, $address);

        return [
            'cart_hint' => $hintMessage,
            'coupon_not_valid_cart_hint' => $hintCouponNotValidMessage,
            'can_get_discounted_items_now' => ($itemQtyOnCanGetNow > 0)
        ];
    }

    protected function createGetYItemsSlots($buyXSets, $getYQty){
        $getYItemsSlots = [];

        $index = 0;
        foreach($buyXSets as $buyXSet){
            $getYItemsSlots[$index] = [];
            $getYItemsSlots[$index]['buy_x_items_data'] = $buyXSet;
            $getYItemsSlots[$index]['get_y_items_slots'] = [];
            $getYItemsSlots[$index]['get_y_items_slots']['unfilled_qty'] = $getYQty;

            $index++;
        }
        return $getYItemsSlots;
    }

    public function getEachNAggregatorName()
    {
        return $this->getAggregatorOption($this->getEachNAggregator());
    }

    public function getEachNAggregatorElement()
    {
        if ($this->getEachNAggregator() === null) {
            foreach (array_keys($this->getAggregatorOption()) as $key) {
                $this->setEachNAggregator($key);
                break;
            }
        }
        return $this->getForm()->addField(
            $this->getPrefix() . '_eachn__' . $this->getId() . '__aggregator',
            'select',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][eachn][' . $this->getId() . '][aggregator]',
                'values' => $this->getAggregatorSelectOptions(),
                'value' => $this->getEachNAggregator(),
                'value_name' => $this->getEachNAggregatorName(),
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
                $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
            );
    }

    public function getEachNAggregatorValueName()
    {
        if(isset($this->aggregatorValueOptions[$this->getEachNAggregatorValue()])){
            return $this->aggregatorValueOptions[$this->getEachNAggregatorValue()];
        }
        return $this->getEachNAggregatorValue();
    }

    public function getEachNAggregatorValueElement()
    {
        if ($this->getEachNAggregatorValue() === null) {
            foreach (array_keys($this->aggregatorValueOptions) as $key) {
                $this->setEachNAggregatorValue($key);
                break;
            }
        }
        return $this->getForm()->addField(
            $this->getPrefix() . '_eachn__' . $this->getId() . '__aggregator_value',
            'select',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][eachn][' . $this->getId() . '][aggregator_value]',
                'values' => $this->aggregatorValueOptions,
                'value' => $this->getEachNAggregatorValue(),
                'value_name' => $this->getEachNAggregatorValueName(),
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
                $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
            );
    }

    public function getNNumberElement()
    {
        $elementParams = [
            'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][n_number]',
            'value' => $this->getNNumber(),
            'value_name' => ($this->getNNumber() ? $this->getNNumber() : "..."),
            'data-form-part' => $this->getFormName()
        ];
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__n_number',
            'text',
            $elementParams
        )->setRenderer(
                $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
            );
    }

    public function getNHintsSingularElement()
    {
        $elementParams = [
            'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][n_hints_singular]',
            'value' => $this->getNHintsSingular(),
            'value_name' => ($this->getNHintsSingular() ? $this->getNHintsSingular() : "..."),
            'data-form-part' => $this->getFormName()
        ];
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__n_hints_singular',
            'text',
            $elementParams
        )->setRenderer(
                $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
            );
    }

    public function getNHintsPluralElement()
    {
        $elementParams = [
            'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][n_hints_plural]',
            'value' => $this->getNHintsPlural(),
            'value_name' => ($this->getNHintsPlural() ? $this->getNHintsPlural() : "..."),
            'data-form-part' => $this->getFormName()
        ];
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__n_hints_plural',
            'text',
            $elementParams
        )->setRenderer(
            $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
        );
    }

    public function getMNumberElement()
    {
        $elementParams = [
            'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][m_number]',
            'value' => $this->getMNumber(),
            'value_name' => ($this->getMNumber() ? $this->getMNumber() : "..."),
            'data-form-part' => $this->getFormName()
        ];
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__m_number',
            'text',
            $elementParams
        )->setRenderer(
                $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
            );
    }

    public function getEachNLimitElement()
    {
        $elementParams = [
            'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][each_n_limit]',
            'value' => $this->getEachNLimit(),
            'value_name' => ($this->getEachNLimit() ? $this->getEachNLimit() : "..."),
            'data-form-part' => $this->getFormName()
        ];
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__each_n_limit',
            'text',
            $elementParams
        )->setRenderer(
                $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
            );
    }

    public function getEachNNewChildElement()
    {
        return $this->getForm()->addField(
            $this->getPrefix() . '_eachn__'.$this->getId() . '__new_child',
            'select',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][eachn][' . $this->getId() . '][new_child]',
                'values' => $this->getNewChildSelectOptions(),
                'value_name' => $this->getNewChildName(),
                'data-form-part' => $this->getFormName()
            ]
        )->setRenderer(
                $this->_layout->getBlockSingleton('Magento\Rule\Block\Newchild')
            );
    }

    public function getAfterMQtyElement()
    {
        $elementParams = [
            'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][after_m_qty]',
            'value' => $this->getAfterMQty(),
            'value_name' => ($this->getAfterMQty() ? $this->getAfterMQty() : "..."),
            'data-form-part' => $this->getFormName()
        ];
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__after_m_qty',
            'text',
            $elementParams
        )->setRenderer(
                $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
            );
    }

    public function getUpToNNumberElement()
    {
        $elementParams = [
            'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][up_to_n_number]',
            'value' => $this->getUpToNNumber(),
            'value_name' => ($this->getUpToNNumber() ? $this->getUpToNNumber() : "..."),
            'data-form-part' => $this->getFormName()
        ];
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__up_to_n_number',
            'text',
            $elementParams
        )->setRenderer(
                $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
            );
    }

    public function getEachNTypeElement()
    {
        return $this->getForm()->addField(
            $this->getPrefix() . '_eachn__' . $this->getId() . '__type',
            'hidden',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][eachn][' . $this->getId() . '][type]',
                'value' => 'Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product\Combine',
                'no_span' => true,
                'class' => 'hidden',
                'data-form-part' => $this->getFormName()
            ]
        );
    }

    public function getEachNWrapperTypeElement()
    {
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__type',
            'hidden',
            [
                'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][type]',
                'value' => $this->getType(),
                'no_span' => true,
                'class' => 'hidden',
                'data-form-part' => $this->getFormName()
            ]
        );
    }

    public function getDiscountAmountValueElement()
    {
        $elementParams = [
            'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][discount_amount_value]',
            'value' => $this->getDiscountAmountValue(),
            'value_name' => ($this->getDiscountAmountValue() ? $this->getDiscountAmountValue() : "..."),
            'data-form-part' => $this->getFormName()
        ];
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__discount_amount_value',
            'text',
            $elementParams
        )->setRenderer(
                $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
            );
    }

    public function getOrderNumberElement()
    {
        $elementParams = [
            'name' => $this->elementName . '[' . $this->getPrefix() . '][' . $this->getId() . '][order_number]',
            'value' => $this->getOrderNumber(),
            'value_name' => ($this->getOrderNumber() ? $this->getOrderNumber() : "..."),
            'data-form-part' => $this->getFormName()
        ];
        return $this->getForm()->addField(
            $this->getPrefix() . '__' . $this->getId() . '__order_number',
            'text',
            $elementParams
        )->setRenderer(
                $this->_layout->getBlockSingleton('Mexbs\ApBase\Block\Editable')
            );
    }

    public function asSubActionArray($subActionDetailsKey){
        $out = [];
        if($subActionDetailsKey == 'eachn'){
            $out = $this->getEachNActionDetails()->asArray();
        }
        return $out;
    }

    public function loadSubActionArray($subActionDetailsKey, $arr, $key = 'action_details'){
        if($subActionDetailsKey == 'eachn'){
            $this->setEachNAggregator($arr[$key][1]['aggregator']);
            $this->setEachNAggregatorValue($arr[$key][1]['aggregator_value']);
            $eachNActionDetails = $this->_conditionFactory->create($arr[$key][1]['type']);
            $eachNActionDetails->setRule($this->getRule())
                ->setObject($this->getObject())
                ->setPrefix($this->getPrefix())
                ->setSubPrefix('eachn')
                ->setType('Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product\Combine')
                ->setId('1--1');

            $this->setEachNActionDetails($eachNActionDetails);
            $eachNActionDetails->loadArray($arr[$key][1], $key);
        }
    }

    public function asArray(array $arrAttributes = [])
    {
        //this method shouldn't be used, instead loadSubActionArray should be
    }

    public function getSubActionDetailsKeys(){
        return [
            'eachn'
        ];
    }
}
