<?php
namespace Mexbs\ApBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveRuleInvalidateIndex implements ObserverInterface{

    protected $helper;
    protected $ruleProductProcessor;

    public function __construct(
        \Mexbs\ApBase\Helper\Data $helper,
        \Mexbs\ApBase\Model\Indexer\Rule\RuleProductProcessor $ruleProductProcessor
    ) {
        $this->helper = $helper;
        $this->ruleProductProcessor = $ruleProductProcessor;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rule = $observer->getEvent()->getRule();

        if(!$rule || !$rule->getId()){
            return;
        }

        if($this->helper->isSimpleActionAp($rule->getSimpleAction())){
            $this->ruleProductProcessor->markIndexerAsInvalid();
        }
    }
}