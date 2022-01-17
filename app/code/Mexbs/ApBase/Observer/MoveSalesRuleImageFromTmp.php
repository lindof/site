<?php
namespace Mexbs\ApBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class MoveSalesRuleImageFromTmp implements ObserverInterface{
    protected $imageUploader;

    public function __construct(
        \Mexbs\ApBase\Model\ImageUploader $imageUploader
    ) {
        $this->imageUploader = $imageUploader;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rule = $observer->getEntity();

        $imagesKeys = ['popup_on_first_visit_image', 'banner_in_promo_trigger_products_image', 'badge_in_promo_trigger_products_image', 'badge_in_promo_trigger_products_category_image',
            'banner_in_get_products_image', 'badge_in_get_products_image', 'badge_in_get_products_category_image'];

        foreach($imagesKeys as $imageKey){
            if(!empty($rule->getData($imageKey))
                && $rule->getData($imageKey."_uploaded")){
                try{
                    $this->imageUploader->moveFileFromTmpDirectory($rule->getData($imageKey));
                }catch(\Exception $e){}
            }
        }
    }
}