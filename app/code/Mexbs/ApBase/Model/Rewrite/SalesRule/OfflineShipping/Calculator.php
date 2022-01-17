<?php
namespace Mexbs\ApBase\Model\Rewrite\SalesRule\OfflineShipping;

class Calculator extends \Magento\OfflineShipping\Model\SalesRule\Calculator{
    protected $dataObjectFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\SalesRule\Model\Utility $utility,
        \Magento\SalesRule\Model\RulesApplier $rulesApplier,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\SalesRule\Model\Validator\Pool $validators,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct(
            $context,
            $registry,
            $collectionFactory,
            $catalogData,
            $utility,
            $rulesApplier,
            $priceCurrency,
            $validators,
            $messageManager,
            $resource,
            $resourceCollection,
            $data
        );
    }


    public function processFreeShipping(\Magento\Quote\Model\Quote\Item\AbstractItem $item)
    {
        $address = $item->getAddress();
        $item->setFreeShipping(false);

        foreach ($this->_getRules($address) as $rule) {
            /* @var $rule \Magento\SalesRule\Model\Rule */
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

            if (!$rule->getActions()->validate($item)
                || ($itemValidationResult->getResult() === false)) {
                continue;
            }

            switch ($rule->getSimpleFreeShipping()) {
                case \Magento\OfflineShipping\Model\SalesRule\Rule::FREE_SHIPPING_ITEM:
                    $item->setFreeShipping($rule->getDiscountQty() ? $rule->getDiscountQty() : true);
                    break;

                case \Magento\OfflineShipping\Model\SalesRule\Rule::FREE_SHIPPING_ADDRESS:
                    $address->setFreeShipping(true);
                    break;
            }
            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }
        return $this;
    }
}