<?php
namespace IWD\StoreLocator\Block\Search;

use Magento\Framework\View\Element\Template;
use IWD\StoreLocator\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Framework\Api\SortOrder;

class Form extends Template
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @var ItemCollectionFactory
     */
    private $itemCollectionFactory;
    
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    private $countryFactory;

    /**
     * Form constructor.
     * @param Template\Context $context
     * @param ItemCollectionFactory $ItemCollectionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     */
    public function __construct(
        Template\Context $context,
        ItemCollectionFactory $ItemCollectionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory
    ) {
       
        $this->itemCollectionFactory = $ItemCollectionFactory;
        $this->storeManager = $context->getStoreManager();
        $this->countryFactory = $countryFactory;

        parent::__construct($context);
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return array|mixed
     */
    public function getRadiusList()
    {
        $list = $this->_scopeConfig->getValue(
            'iwd_storelocator/search/radius_list',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $list = explode(';', $list);
       
        $default = $this->getDefaultRadius();
        array_push($list, $default);
        
        $list = array_unique($list);
        sort($list);
        return $list;
    }

    /**
     * @return int|mixed
     */
    public function getDefaultRadius()
    {
        $default = $this->_scopeConfig->getValue(
            'iwd_storelocator/search/default_radius',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        $default = empty($default) ? 7000 : $default;
        return $default;
    }

    /**
     * @return bool
     */
    public function isShownRadius()
    {
        return (bool)$this->_scopeConfig->getValue(
            'iwd_storelocator/search/filter_radius',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return array
     */
    public function getListCountries()
    {
        
        $storeId = $this->storeManager->getStore()->getId();
        
        $collection = $this->itemCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => 1]);
        $collection->addOrder('country_id', SortOrder::SORT_ASC);
        
        $collection->addStoreFilter($storeId, true);
        
        $countries = [];
        foreach ($collection as $blockModel) {
            $countries[] = $blockModel->getCountryId();
        }
        $countries = array_unique($countries);

        $data = [];
        foreach ($countries as $code) {
            if (!empty($code)) {
                $label = $this->countryFactory->create()->loadByCode($code)->getName();
                $data[$code] = !empty($label)?$label:$code; //FIX MAGENTO ISSUE WHEN NAME IS EMPTY
            }
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function isResponsive()
    {
        return !(bool)$this->_scopeConfig->getValue(
            'iwd_storelocator/design/full_width',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getMetric()
    {
        $metric = $this->_scopeConfig->getValue(
            'iwd_storelocator/search/metric',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($metric == 1) {
            $metric  = __('Km');
        } elseif ($metric == 2) {
            $metric  = __('Miles');
        }
        
        return $metric;
    }
}
