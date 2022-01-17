<?php
namespace Mexbs\ApBase\Model\Plugin\Rule\Condition\Product;

class Combine
{
    public function afterGetNewChildSelectOptions(
        \Magento\SalesRule\Model\Rule\Condition\Product\Combine $subject,
        $conditions
    ){
        if($subject instanceof \Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product\Combine){
            $customOptionTitleValueCondition = [
                'value' => 'Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product\CustomOptionTitleValue',
                'label' => "Custom Option title and value"
            ];

            $customOptionSkuCondition = [
                'value' => 'Mexbs\ApBase\Model\Rule\Action\Details\Condition\Product\CustomOptionSku',
                'label' => "Custom Option SKU"
            ];
        }else{
            $customOptionTitleValueCondition = [
                'value' => 'Mexbs\ApBase\Model\SalesRule\Rule\Condition\Product\CustomOptionTitleValue',
                'label' => "Custom Option title and value"
            ];

            $customOptionSkuCondition = [
                'value' => 'Mexbs\ApBase\Model\SalesRule\Rule\Condition\Product\CustomOptionSku',
                'label' => "Custom Option SKU"
            ];
        }


        $conditionsIncludingAp = $conditions;
        foreach($conditions as $conditionIndex => $condition){
            if(isset($condition['label'])
                && ($condition['label'] == __('Cart Item Attribute'))){
                if(!isset($condition['value']) || !is_array($condition['value'])){
                    $conditionsIncludingAp[$conditionIndex]['value'] = [];
                }
                $conditionsIncludingAp[$conditionIndex]['value'][] = $customOptionTitleValueCondition;
                $conditionsIncludingAp[$conditionIndex]['value'][] = $customOptionSkuCondition;
            }
        }
        return $conditionsIncludingAp;
    }
}