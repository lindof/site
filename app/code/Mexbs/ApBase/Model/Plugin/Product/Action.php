<?php
namespace Mexbs\ApBase\Model\Plugin\Product;

class Action
{
    protected $productRuleProcessor;

    public function __construct(\Mexbs\ApBase\Model\Indexer\Product\ProductRuleProcessor $productRuleProcessor)
    {
        $this->productRuleProcessor = $productRuleProcessor;
    }

    public function aroundUpdateAttributes(
        \Magento\Catalog\Model\Product\Action $subject,
        \Closure $proceed,
        $productIds, $attrData, $storeId
    ){
        $returnValue = $proceed($productIds, $attrData, $storeId);
        $this->productRuleProcessor->reindexList($productIds);
        return $returnValue;
    }
}