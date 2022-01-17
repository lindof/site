<?php
namespace Mexbs\ApBase\Model\Plugin\Rule;

class DataProvider
{
    private $apHelper;

    public function __construct(
        \Mexbs\ApBase\Helper\Data $apHelper
    ){
        $this->apHelper = $apHelper;
    }

    public function afterGetData(
        \Magento\SalesRule\Model\Rule\DataProvider $subject,
        $data
    ){
        if(!is_array($data)){
            return $data;
        }

        $imagesKeys = ['popup_on_first_visit_image', 'banner_in_promo_trigger_products_image', 'badge_in_promo_trigger_products_image', 'badge_in_promo_trigger_products_category_image',
            'banner_in_get_products_image', 'badge_in_get_products_image', 'badge_in_get_products_category_image'];

        foreach($data as $ruleId => $ruleData){
            foreach($imagesKeys as $imageKey){
                if (isset($ruleData[$imageKey])
                    && !empty($ruleData[$imageKey])) {
                    $imageName = $ruleData[$imageKey];
                    $data[$ruleId][$imageKey] = [];
                    $data[$ruleId][$imageKey][0] = [];
                    $data[$ruleId][$imageKey][0]['name'] = $imageName;
                    $data[$ruleId][$imageKey][0]['url'] = $this->apHelper->getSalesRuleImageUrl($imageName);
                }
            }
        }
        return $data;
    }
}