<?php
namespace Mexbs\AdditionalPromotions\Model\Rule\Action\Details;

class FirstNNextMAfterKFixedDiscount extends FirstNNextMAfterKAbstract{

    const SIMPLE_ACTION = 'first_n_next_m_after_k_fixed_discount_action';
    const MAX_NUMBER_OF_SEQS = 5;

    protected $type = 'Mexbs\AdditionalPromotions\Model\Rule\Action\Details\FirstNNextMAfterKFixedDiscount';

    public function getDiscountType(){
        return self::DISCOUNT_TYPE_FIXED;
    }

    public function getSimpleAction(){
        return self::SIMPLE_ACTION;
    }

    public function getNumberOfSeqs(){
        return self::MAX_NUMBER_OF_SEQS;
    }

    public function asHtmlRecursive()
    {
        $html =  __(
                "For products [label for upsell cart hints - singular: %1, plural: %2], for which %3 of the following conditions are %4",
                $this->getHintsSingularElement()->getHtml(),
                $this->getHintsPluralElement()->getHtml(),
                $this->getFirstNNextMAggregatorElement()->getHtml(),
                $this->getFirstNNextMAggregatorValueElement()->getHtml()
            ).'<ul id="' .
            $this->getPrefix() .
            '_firstnnextm__' .
            $this->getId() .
            '__children" class="rule-param-children">';

        if($this->getFirstNNextMActionDetails()){
            foreach($this->getFirstNNextMActionDetails()->getActionDetails() as $actionDetail){
                $html .= '<li>' . $actionDetail->asHtmlRecursive() . '</li>';
            }
        }

        $html .= '<li>' . $this->getFirstNNextMNewChildElement()->getHtml() . '</li></ul>';

        for($seqPartIndex = 1; $seqPartIndex <= $this->getNumberOfSeqs(); $seqPartIndex++){
            $seqLabel = ($seqPartIndex == 1 ? "First" : "Next");
            $html .=  __(
                "<div>%1 %2 items with %3%4 discount</div>",
                $seqLabel,
                $this->getDiscountQtyValueElement($seqPartIndex)->getHtml(),
                $this->getDiscountAmountValueElement($seqPartIndex)->getHtml(),
                $this->apHelper->getCurrentCurrencySymbol()
            );
        }

        $html .= __("<div>After %1 such items has been added to cart for full price</div>",
            $this->getAfterMQtyElement()->getHtml());

        $html .=  '<ul id="' .
            $this->getPrefix() .
            '_firstnnextm__' .
            $this->getId() .
            '__children" class="rule-param-children">';

        $html = $this->getFirstNNextMWrapperTypeElement()->getHtml() .
            $this->getFirstNNextMTypeElement()->getHtml()
            .$html;

        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return "<li>".$html."</li>";
    }
}