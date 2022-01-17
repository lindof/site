<?php
namespace Mexbs\ApBase\Model\Plugin;

class RuleRepository
{
    public static $extensionSalesRuleFields = [
        'action_details_serialized',
        'discount_order_type',
        'max_groups_number',
        'max_sets_number',
        'discount_breakdown_type',
        'max_discount_amount',
        'skip_special_price',
        'skip_tier_price',
        'display_popup_on_first_visit',
        'popup_on_first_visit_image',
        'display_cart_hints',
        'actions_hint_label',
        'hide_hints_after_discount_number',
        'display_cart_hints_if_coupon_invalid',
        'hide_promo_block_if_rule_applied',
        'display_product_hints',
        'product_hints_location',
        'enable_auto_add',
        'display_banner_in_promo_trigger_products',
        'banner_in_promo_trigger_products_image',
        'display_badge_in_promo_trigger_products',
        'badge_in_promo_trigger_products_image',
        'display_banner_in_get_products',
        'banner_in_get_products_image',
        'display_badge_in_get_products',
        'badge_in_get_products_image',
        'display_badge_in_promo_trigger_products_category',
        'badge_in_promo_trigger_products_category_image',
        'display_badge_in_get_products_category',
        'badge_in_get_products_category_image',
        'display_promo_block_in_cart',
        'display_promo_block_in_product',
        'promo_block_title_in_product',
        'discount_method'
    ];

    protected $ruleFactory;
    public function __construct(
        \Magento\SalesRule\Model\RuleFactory $ruleFactory
    ){
        $this->ruleFactory = $ruleFactory;
    }

    protected function camelize($input, $separator = '_')
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    public function aroundGetById(
        \Magento\SalesRule\Api\RuleRepositoryInterface $subject,
        \Closure $proceed,
        $ruleId
    )
    {
        $returnValue = $proceed($ruleId);

        $rule = $this->ruleFactory->create()
            ->load($ruleId);

        $extensionAttributes = $returnValue->getExtensionAttributes();

        foreach(self::$extensionSalesRuleFields as $extensionSalesRuleField){
            $ruleFieldContent = $rule->getData($extensionSalesRuleField);
            call_user_func_array(array($extensionAttributes, 'set'.$this->camelize($extensionSalesRuleField)), [$ruleFieldContent]);
        }

        $returnValue->setExtensionAttributes($extensionAttributes);

        return $returnValue;
    }

    public function aroundSave(
        \Magento\SalesRule\Api\RuleRepositoryInterface $subject,
        \Closure $proceed,
        \Magento\SalesRule\Api\Data\RuleInterface $rule
    )
    {
        $filledExtensionFields = [];

        $extensionAttributes = $rule->getExtensionAttributes();

        foreach(self::$extensionSalesRuleFields as $extensionSalesRuleField){
            $ruleFieldContent = call_user_func_array(array($extensionAttributes, 'get'.$this->camelize($extensionSalesRuleField)), []);
            $filledExtensionFields[$extensionSalesRuleField] = $ruleFieldContent;
        }

        $returnValue = $proceed($rule);

        $ruleModel = $this->ruleFactory->create()
            ->load($returnValue->getRuleId());

        foreach($filledExtensionFields as $filledExtensionFieldKey => $filledExtensionFieldValue){
            $ruleModel->setData($filledExtensionFieldKey, $filledExtensionFieldValue);
        }

        $ruleModel->save();

        return $returnValue;
    }
}
