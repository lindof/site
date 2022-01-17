<?php

namespace Knowband\Spinandwin\Block\Adminhtml;

class Country extends \Magento\Framework\View\Element\Template {
         
        protected $_countryCollectionFactory;
        public function __construct(
            \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
            \Magento\Framework\View\Element\Template\Context $context,
            array $data = []
        ) {
//            $this->Customer = $customer;
            parent::__construct($context, $data);
            $this->_countryCollectionFactory = $countryCollectionFactory;
        }
 
        public function getCountryCollection()
        {
            $collection = $this->_countryCollectionFactory->create()->loadByStore();
            return $collection;
        }
 
        /**
         * Retrieve list of top destinations countries
         *
         * @return array
         */
        protected function getTopDestinations()
        {
            $destinations = (string)$this->_scopeConfig->getValue(
                'general/country/destinations',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            return !empty($destinations) ? explode(',', $destinations) : [];
        }
 
        /**
         * Retrieve list of countries in array option
         *
         * @return array
         */
        public function getCountries()
        {
            return $options = $this->getCountryCollection()
                    ->setForegroundCountries($this->getTopDestinations())
                        ->toOptionArray();
        }
} 
