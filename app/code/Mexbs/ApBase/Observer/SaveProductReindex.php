<?php
namespace Mexbs\ApBase\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveProductReindex implements ObserverInterface{

    protected $helper;
    protected $productRuleIndexer;

    public function __construct(
        \Mexbs\ApBase\Helper\Data $helper,
        \Mexbs\ApBase\Model\Indexer\Product\ProductRuleIndexer $productRuleIndexer
    ) {
        $this->helper = $helper;
        $this->productRuleIndexer = $productRuleIndexer;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if(!$product || !$product->getId()){
            return;
        }
        $this->productRuleIndexer->executeRow($product->getId());
    }
}