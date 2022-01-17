<?php

namespace Cminds\Marketplace\Block\Settings;

use Cminds\Marketplace\Helper\Data as MarketplaceHelper;
use Cminds\Marketplace\Model\ResourceModel\Methods\CollectionFactory as MethodsCollection;
use Cminds\Supplierfrontendproductuploader\Helper\Price;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

class Methods extends Template
{
    protected $methodsCollection;
    protected $_marketplaceHelper;
    protected $priceHelper;

    public function __construct(
        Context $context,
        MethodsCollection $methodsCollection,
        MarketplaceHelper $marketplaceHelper,
        Price $priceHelper
    ) {
        parent::__construct($context);

        $this->methodsCollection = $methodsCollection;
        $this->_marketplaceHelper = $marketplaceHelper;
        $this->priceHelper = $priceHelper;
    }

    public function getSavedMethods()
    {
        $collection = $this->methodsCollection->create();
        $collection
            ->addFieldToFilter('supplier_id', $this->_marketplaceHelper->getSupplierId());

        return $collection;
    }

    public function getCurrentCurrencyPrice($price)
    {
        return $this->priceHelper->convertToCurrentCurrencyPrice($price);
    }
}
