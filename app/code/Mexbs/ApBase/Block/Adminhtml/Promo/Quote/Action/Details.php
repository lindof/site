<?php
namespace Mexbs\ApBase\Block\Adminhtml\Promo\Quote\Action;

class Details extends \Magento\Backend\Block\Template
{
    const ACTIONS_SECTION_NAME = 'actions_apply_to';

    protected $coreRegistry;
    protected $apHelper;
    protected $loadedActionDetail;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Mexbs\ApBase\Helper\Data $apHelper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ){
        parent::__construct($context, $data);
        $this->setTemplate("Mexbs_ApBase::promo/action/details.phtml");
        $this->coreRegistry = $registry;
        $this->apHelper = $apHelper;
    }

    public function getMagentoVersion(){
        return $this->apHelper->getMagentoVersion();
    }

    public function getAllApSimpleActions(){
        return array_keys($this->apHelper->getApSimpleActionsToTypes());
    }

    public function getNewChildUrl(){
        $actionFieldSetId = "sales_rule_formrule_action_details_fieldset_";
        $formName = 'sales_rule_form';

        return $this->getUrl(
            'apromotions/promo_quote/newActionDetailsHtml/form/' . $actionFieldSetId,
            ['form_namespace' => $formName]
        );
    }

    public function getCurrentRule(){
        return $this->coreRegistry->registry(\Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE);
    }

    public function getActionDetailLoaded(){
        if($this->loadedActionDetail){
            return $this->loadedActionDetail;
        }

        $rule = $this->getCurrentRule();

        if($rule){
            $this->loadedActionDetail = $this->apHelper->getLoadedActionDetail($rule);
        }

        return $this->loadedActionDetail;
    }

    public function getActionDetailsContainerId(){
        return 'action-details-wrapper';
    }

    public function getChildrenHtml(){
        $actionDetailLoaded = $this->getActionDetailLoaded();

        if(!$actionDetailLoaded){
            return '';
        }
        return $actionDetailLoaded->asHtmlRecursive();
    }

    public function getAllApFieldNames(){
        return [
            'discount_order_type',
            'max_groups_number',
            'max_sets_number',
            'max_discount_amount',
            'skip_special_price',
            'skip_tier_price'
        ];
    }

    public function getAllNonApFieldNames(){
        return [
            'discount_amount',
            'discount_qty',
            'discount_step',
            'apply_to_shipping',
            'stop_rules_processing',
            'actions_apply_to'
        ];
    }

    public function getAllFieldNames(){
        return array_merge($this->getAllNonApFieldNames(), $this->getAllApFieldNames());
    }

    public function getApSimpleActionFieldNamesShowSetting(){

        if(class_exists("\Mexbs\AdditionalPromotions\Model\Rule\Action\Details\FirstNNextMAfterKFixedPriceDiscount")){
            return
                [
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetYForEachXSpent::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetYForEachXSpentUpToN::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyXGetNOfYPercentDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyXGetNOfYFixedDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyXGetNOfYFixedPriceDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyABCGetNOfDPercentDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyABCGetNOfDFixedDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\BuyABCGetNOfDFixedPriceDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetEachNAfterMPercentDiscount::SIMPLE_ACTION =>
                    [
                        'discount_qty',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetEachNAfterMFixedDiscount::SIMPLE_ACTION =>
                    [
                        'discount_qty',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetEachNAfterMFixedPriceDiscount::SIMPLE_ACTION =>
                    [
                        'discount_qty',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetAllAfterMPercentDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetAllAfterMFixedDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\GetAllAfterMFixedPriceDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\EachGroupOfNPercentDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_groups_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\EachGroupOfNFixedDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_groups_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\EachGroupOfNFixedPriceDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_groups_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\ProductsSetPercentDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_sets_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\ProductsSetFixedDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_sets_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\ProductsSetFixedPriceDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_sets_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\FirstNNextMAfterKPercentDiscount::SIMPLE_ACTION =>
                    [
                        'max_discount_amount',
                        'discount_order_type',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\FirstNNextMAfterKFixedDiscount::SIMPLE_ACTION =>
                    [
                        'max_discount_amount',
                        'discount_order_type',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\FirstNNextMAfterKFixedPriceDiscount::SIMPLE_ACTION =>
                    [
                        'max_discount_amount',
                        'discount_order_type',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\CheapestPercentDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\CheapestFixedDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                    \Mexbs\AdditionalPromotions\Model\Rule\Action\Details\CheapestFixedPriceDiscount::SIMPLE_ACTION =>
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ],
                ];
        }else{
            $showSettings = [];
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductPercentDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductPercentDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'skip_special_price',
                        'skip_tier_price'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductFixedDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductFixedDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'skip_special_price',
                        'skip_tier_price'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductFixedPriceDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'skip_special_price',
                        'skip_tier_price'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductForEachXSpentPercentDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductForEachXSpentPercentDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'skip_special_price',
                        'skip_tier_price',
                        'discount_qty'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductForEachXSpentFixedDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductForEachXSpentFixedDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'skip_special_price',
                        'skip_tier_price',
                        'discount_qty'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductForEachXSpentFixedPriceDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\GetProductForEachXSpentFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'skip_special_price',
                        'skip_tier_price',
                        'discount_qty'
                    ];
            }
            if(class_exists("\Mexbs\YForEachXSpent\Model\Rule\Action\Details\GetYForEachXSpent")){
                $showSettings[\Mexbs\YForEachXSpent\Model\Rule\Action\Details\GetYForEachXSpent::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\YForEachXSpent\Model\Rule\Action\Details\GetYForEachXSpentUpToN")){
                $showSettings[\Mexbs\YForEachXSpent\Model\Rule\Action\Details\GetYForEachXSpentUpToN::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\BuyXGetNOfYPercentDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\BuyXGetNOfYPercentDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\BuyXGetNOfYFixedDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\BuyXGetNOfYFixedDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\BuyXGetNOfYFixedPriceDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\BuyXGetNOfYFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\ExtendedBogo\Model\Rule\Action\Details\BuyABCGetNOfDPercentDiscount")){
                $showSettings[\Mexbs\ExtendedBogo\Model\Rule\Action\Details\BuyABCGetNOfDPercentDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\ExtendedBogo\Model\Rule\Action\Details\BuyABCGetNOfDFixedDiscount")){
                $showSettings[\Mexbs\ExtendedBogo\Model\Rule\Action\Details\BuyABCGetNOfDFixedDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\ExtendedBogo\Model\Rule\Action\Details\BuyABCGetNOfDFixedPriceDiscount")){
                $showSettings[\Mexbs\ExtendedBogo\Model\Rule\Action\Details\BuyABCGetNOfDFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\EachN\Model\Rule\Action\Details\GetEachNAfterMPercentDiscount")){
                $showSettings[\Mexbs\EachN\Model\Rule\Action\Details\GetEachNAfterMPercentDiscount::SIMPLE_ACTION] =
                    [
                        'discount_qty',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\EachN\Model\Rule\Action\Details\GetEachNAfterMFixedDiscount")){
                $showSettings[\Mexbs\EachN\Model\Rule\Action\Details\GetEachNAfterMFixedDiscount::SIMPLE_ACTION] =
                    [
                        'discount_qty',
                        'discount_method',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\EachN\Model\Rule\Action\Details\GetEachNAfterMFixedPriceDiscount")){
                $showSettings[\Mexbs\EachN\Model\Rule\Action\Details\GetEachNAfterMFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'discount_qty',
                        'discount_method',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetEachNAfterMPercentDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\GetEachNAfterMPercentDiscount::SIMPLE_ACTION] =
                    [
                        'discount_qty',
                        'discount_method',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetEachNAfterMFixedDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\GetEachNAfterMFixedDiscount::SIMPLE_ACTION] =
                    [
                        'discount_qty',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\FreeGift\Model\Rule\Action\Details\GetEachNAfterMFixedPriceDiscount")){
                $showSettings[\Mexbs\FreeGift\Model\Rule\Action\Details\GetEachNAfterMFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'discount_qty',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\Cheapest\Model\Rule\Action\Details\CheapestPercentDiscount")){
                $showSettings[\Mexbs\Cheapest\Model\Rule\Action\Details\CheapestPercentDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\Cheapest\Model\Rule\Action\Details\CheapestFixedDiscount")){
                $showSettings[\Mexbs\Cheapest\Model\Rule\Action\Details\CheapestFixedDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\Cheapest\Model\Rule\Action\Details\CheapestFixedPriceDiscount")){
                $showSettings[\Mexbs\Cheapest\Model\Rule\Action\Details\CheapestFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\EachN\Model\Rule\Action\Details\GetAllAfterMPercentDiscount")){
                $showSettings[\Mexbs\EachN\Model\Rule\Action\Details\GetAllAfterMPercentDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\EachN\Model\Rule\Action\Details\GetAllAfterMFixedDiscount")){
                $showSettings[\Mexbs\EachN\Model\Rule\Action\Details\GetAllAfterMFixedDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\EachN\Model\Rule\Action\Details\GetAllAfterMFixedPriceDiscount")){
                $showSettings[\Mexbs\EachN\Model\Rule\Action\Details\GetAllAfterMFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'discount_qty',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\DynamicTier\Model\Rule\Action\Details\EachGroupOfNPercentDiscount")){
                $showSettings[\Mexbs\DynamicTier\Model\Rule\Action\Details\EachGroupOfNPercentDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_groups_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\DynamicTier\Model\Rule\Action\Details\EachGroupOfNFixedDiscount")){
                $showSettings[\Mexbs\DynamicTier\Model\Rule\Action\Details\EachGroupOfNFixedDiscount::SIMPLE_ACTION]=
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_groups_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\DynamicTier\Model\Rule\Action\Details\EachGroupOfNFixedPriceDiscount")){
                $showSettings[\Mexbs\DynamicTier\Model\Rule\Action\Details\EachGroupOfNFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_groups_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\BundledDiscount\Model\Rule\Action\Details\ProductsSetPercentDiscount")){
                $showSettings[\Mexbs\BundledDiscount\Model\Rule\Action\Details\ProductsSetPercentDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_sets_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\BundledDiscount\Model\Rule\Action\Details\ProductsSetFixedDiscount")){
                $showSettings[\Mexbs\BundledDiscount\Model\Rule\Action\Details\ProductsSetFixedDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_sets_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\BundledDiscount\Model\Rule\Action\Details\ProductsSetFixedPriceDiscount")){
                $showSettings[\Mexbs\BundledDiscount\Model\Rule\Action\Details\ProductsSetFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'stop_rules_processing',
                        'discount_order_type',
                        'max_sets_number',
                        'skip_special_price',
                        'skip_tier_price',
                        'max_discount_amount'
                    ];
            }
            if(class_exists("\Mexbs\DiscountSteps\Model\Rule\Action\Details\FirstNNextMAfterKPercentDiscount")){
                $showSettings[\Mexbs\DiscountSteps\Model\Rule\Action\Details\FirstNNextMAfterKPercentDiscount::SIMPLE_ACTION] =
                    [
                        'max_discount_amount',
                        'discount_order_type',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price'
                    ];
            }
            if(class_exists("\Mexbs\DiscountSteps\Model\Rule\Action\Details\FirstNNextMAfterKFixedDiscount")){
                $showSettings[\Mexbs\DiscountSteps\Model\Rule\Action\Details\FirstNNextMAfterKFixedDiscount::SIMPLE_ACTION] =
                    [
                        'max_discount_amount',
                        'discount_order_type',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price'
                    ];
            }
            if(class_exists("\Mexbs\DiscountSteps\Model\Rule\Action\Details\FirstNNextMAfterKFixedPriceDiscount")){
                $showSettings[\Mexbs\DiscountSteps\Model\Rule\Action\Details\FirstNNextMAfterKFixedPriceDiscount::SIMPLE_ACTION] =
                    [
                        'max_discount_amount',
                        'discount_order_type',
                        'stop_rules_processing',
                        'skip_special_price',
                        'skip_tier_price'
                    ];
            }
        }


        return $showSettings;
    }
}
