<?php
namespace IWD\StoreLocator\Block\Search;


use Magento\Framework\View\Element\Template;
use IWD\StoreLocator\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;


class Grid extends Template
{
    
    /**
     * @var ItemCollectionFactory
     */
    protected $ItemCollectionFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * 
     * @param Template\Context $context
     * @param ItemCollectionFactory $ItemCollectionFactory
     */
    public function __construct(
            Template\Context $context, 
            ItemCollectionFactory $ItemCollectionFactory
        )
    {
        $this->storeManager = $context->getStoreManager();
        $this->ItemCollectionFactory = $ItemCollectionFactory;
        parent::__construct($context);
    }
    
    public function getConfigOption($path, $bool = false){
        if (!$bool){
            return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
    
        return (bool)$this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getItemsCollection()
    {
        $storeId = $this->storeManager->getStore()->getId();
    
        $collection = $this->ItemCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq'=>'1']);
        $collection->addFieldToFilter('lat', ['neq'=>'0']);
        $collection->addFieldToFilter('lng', ['neq'=>'0']);
        $collection->addStoreFilter($storeId, true);
        
//         $collection->setCurPage(1);
//         $collection->setPageSize($this->getConfigOption('iwd_storelocator/search/page_size'));
         
    
    
        return $collection;
    }

}