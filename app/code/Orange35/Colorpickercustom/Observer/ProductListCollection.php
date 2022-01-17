<?php

namespace Orange35\Colorpickercustom\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class ProductListCollection implements ObserverInterface
{
    const SHOW_SWATCHES = 'catalog/frontend/show_swatches_in_product_list';

    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Add custom options to product collection to use them
     * in the Orange35\Colorpickercustom\Block\Product\Renderer\Listing\Swatches
     *
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        if ($this->scopeConfig->getValue(self::SHOW_SWATCHES, ScopeInterface::SCOPE_STORE)) {
            /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection */
            $collection = $observer->getEvent()->getData('collection');
            $collection->addOptionsToResult();
        }
    }
}
