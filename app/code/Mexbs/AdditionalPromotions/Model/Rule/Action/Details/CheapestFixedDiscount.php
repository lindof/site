<?php
namespace Mexbs\AdditionalPromotions\Model\Rule\Action\Details;

class CheapestFixedDiscount extends \Mexbs\ApBase\Model\Rule\Action\Details\GetEachNAbstract{

    const SIMPLE_ACTION = 'cheapest_fixed_discount_action';
    protected $type = 'Mexbs\AdditionalPromotions\Model\Rule\Action\Details\CheapestFixedDiscount';

    public function getDiscountType(){
        return self::DISCOUNT_TYPE_FIXED;
    }

    public function isEachN(){
        return false;
    }

    public function isCheapest(){
        return true;
    }

    public function isDiscountPriceTypeApplicable(){
        return true;
    }

    public function isDiscountOrderTypeApplicable(){
        return false;
    }

    public function isLimitApplicable(){
        return false;
    }

    public function isOrderNumberApplicable(){
        return true;
    }

    public function isDiscountQtyApplicable(){
        return false;
    }

    public function getSimpleAction(){
        return self::SIMPLE_ACTION;
    }

    public function asHtmlRecursive()
    {
        $getEachNHtml =  __(
                "For items [label for upsell cart hints - singular: %1, plural: %2], for which %3 of the following conditions are %4",
                $this->getNHintsSingularElement()->getHtml(),
                $this->getNHintsPluralElement()->getHtml(),
                $this->getEachNAggregatorElement()->getHtml(),
                $this->getEachNAggregatorValueElement()->getHtml()
            ).
            '<ul id="' .
            $this->getPrefix() .
            '_eachn__' .
            $this->getId() .
            '__children" class="rule-param-children">';

        if($this->getEachNActionDetails()){
            foreach($this->getEachNActionDetails()->getActionDetails() as $actionDetail){
                $getEachNHtml .= '<li>' . $actionDetail->asHtmlRecursive() . '</li>';
            }
        }

        $getEachNHtml .= '<li>' . $this->getEachNNewChildElement()->getHtml() . '</li></ul>';

        $getEachNHtml .=  __(
                "Get the %1th %2 item (matching the same conditions), with %3%4 discount",
                $this->getOrderNumberElement()->getHtml(),
                $this->getDiscountPriceTypeAttributeElement()->getHtml(),
                $this->getDiscountAmountValueElement()->getHtml(),
                $this->apHelper->getCurrentCurrencySymbol()
            ).'<ul id="' .
            $this->getPrefix() .
            '_eachn__' .
            $this->getId() .
            '__children" class="rule-param-children">';

        $html = $this->getEachNWrapperTypeElement()->getHtml() .
            $this->getEachNTypeElement()->getHtml() .
            $getEachNHtml;

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return "<li>".$html."</li>";
    }


    public function getDirectAttributeKeys(){
        return [
            'order_number',
            'n_hints_singular',
            'n_hints_plural',
            'm_number',
            'discount_amount_value',
            'discount_price_type',
            'after_m_qty'
        ];
    }
}