<?php
namespace Mexbs\ApBase\Model\Rewrite\SalesRule;

class RulesApplier extends \Magento\SalesRule\Model\RulesApplier
{
    protected $dataObjectFactory;

    public function __construct(
        \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $calculatorFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        parent::__construct(
            $calculatorFactory,
            $eventManager,
            $utility
        );

        $this->dataObjectFactory = $dataObjectFactory;
    }

    public function applyRules($item, $rules, $skipValidation, $couponCode)
    {
        $address = $item->getAddress();
        $appliedRuleIds = [];
        /* @var $rule \Magento\SalesRule\Model\Rule */
        foreach ($rules as $rule) {
            if (!$this->validatorUtility->canProcessRule($rule, $address)) {
                continue;
            }

            /**
             * @var \Magento\Framework\DataObject $itemValidationResult
             */
            $itemValidationResult = $this->dataObjectFactory->create();
            $this->_eventManager->dispatch('salesrule_item_validate_for_rule',
                [
                    'item_validation_result' => $itemValidationResult,
                    'item' => $item,
                    'rule' => $rule,
                    'applies_rules' => true
                ]);

            if (!$skipValidation
                &&
                (
                    !$rule->getActions()->validate($item)
                    || ($itemValidationResult->getResult() === false)
                )
            ) {
                $childItems = $item->getChildren();
                $isContinue = true;
                if (!empty($childItems)) {
                    foreach ($childItems as $childItem) {
                        if ($rule->getActions()->validate($childItem)
                            && ($itemValidationResult->getResult() == true)) {
                            $isContinue = false;
                        }
                    }
                }
                if ($isContinue) {
                    $rulesCanGetDiscountedItemsNow = $address->getQuote()->getRulesCanGetDiscountedItemsNow();
                    if(in_array($rule->getId(), $rulesCanGetDiscountedItemsNow)){
                        $this->maintainAddressCouponCode($address, $rule, $couponCode);
                    }

                    continue;
                }
            }
			
			$this->_eventManager->dispatch('salesrule_item_rule_validated',
			[
				'item' => $item,
				'rule' => $rule,
				'address' => $address
			]);

            $this->applyRule($item, $rule, $address, $couponCode);
            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }

        return $appliedRuleIds;
    }
}