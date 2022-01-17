<?php

namespace IWD\StoreLocator\Model;

use IWD\StoreLocator\Api\Data;
use IWD\StoreLocator\Api\ItemRepositoryInterface;
use IWD\StoreLocator\Model\ResourceModel\Item as ResourceItem;
use IWD\StoreLocator\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use IWD\StoreLocator\Helper\Data as helperData;

/**
 * Class BlockRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ItemRepository implements ItemRepositoryInterface
{
    /**
     * @var ResourceItem
     */
    private $resource;

    /**
     * @var ItemCollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var Data\BlockSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var \Magento\Cms\Api\Data\BlockInterfaceFactory
     */
    private $dataItemFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    private $countryFactory;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    private $regionFactory;

    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    private $curlFactory;
    private $helper;
    private $config;

    /**
     * ItemRepository constructor.
     * @param ResourceItem $resource
     * @param Data\StoreLocatorInterfaceFactory $dataItemFactory
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param Data\ItemSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param helperData $helper
     * @param \IWD\StoreLocator\Helper\Config $config
     */
    public function __construct(
        ResourceItem $resource,
        \IWD\StoreLocator\Api\Data\StoreLocatorInterfaceFactory $dataItemFactory,
        ItemCollectionFactory $itemCollectionFactory,
        Data\ItemSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        helperData $helper,
        \IWD\StoreLocator\Helper\Config $config
    )
    {
        $this->resource = $resource;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataItemFactory = $dataItemFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->helper = $helper;
        $this->config = $config;
    }

    /**
     * @return ResourceItem\Collection
     */
    public function getListWithEmptyLocation()
    {
        $collection = $this->itemCollectionFactory->create();
        $collection->addFieldToFilter(['lat', 'lat'], [['eq' => 0], ['null' => 'null']]);
        $collection->addFieldToFilter(['lng', 'lng'], [['eq' => 0], ['null' => 'null']]);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->itemCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }

        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getListForBlock(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();

        $collection = $this->itemCollectionFactory->create();

        $location = [];
        $address = [];
        $countryId = false;
        $isLocationOnly = $this->_isLocationOnly($criteria);

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), true);
                    continue;
                }

                if ($filter->getField() == 'lat' || $filter->getField() == 'lng') {
                    $location[$filter->getField()] = $filter->getValue();
                } elseif ($filter->getField() == 'distance') {
                    $radius = $filter->getValue();
                } else {
                    $value = $filter->getValue();
                    if (!empty($value) && !$isLocationOnly) {
                        $address[$filter->getField()] = $filter->getValue();
                        if ($filter->getField() == 'country_id') {
                            $collection->addFieldToFilter('country_id', ['eq' => $filter->getValue()]);
                            $countryId = $filter->getValue();
                        }
                    }
                }
            }
        }

        $collection->addFieldToFilter('lat', ['neq' => '0']);
        $collection->addFieldToFilter('lng', ['neq' => '0']);
        $collection->addFieldToFilter('is_active', ['eq' => '1']);

        //default radius
        $radiusDefault = $this->config->getDefaultRadius();
        $radius = empty($radius) ? $radiusDefault : $radius;

        $latitude = 0;
        $longitude = 0;

        //current location available
        if (!empty($location['lat']) && !empty($location['lng'])) {
            $latitude = $location['lat'];
            $longitude = $location['lng'];
        }

        //current location available and not request address
        if (!empty($location['lat']) && !empty($location['lng']) && count($address) == 0) {
            $latitude = $location['lat'];
            $longitude = $location['lng'];
        }

        //current location available but address is exists too - we change location from current to search
        if (!empty($location['lat']) && !empty($location['lng']) && count($address) > 0) {
            $locationAddress = $this->helper->getGeoLocator()->getCoordinatesByAddress($this->getAddress($address));
            if ($locationAddress != false) {
                if (isset($locationAddress['lat']) && isset($locationAddress['lng'])) {
                    //address location
                    $latitude = $locationAddress['lat'];
                    $longitude = $locationAddress['lng'];
                    $criteria = $this->_updateLocation($criteria, $locationAddress);
                }
            }
        } elseif ((empty($location['lat']) || empty($location['lng'])) && count($address) > 0) {
            $locationAddress = $this->helper->getGeoLocator()->getCoordinatesByAddress($this->getAddress($address));
            if ($locationAddress != false) {
                if (isset($locationAddress['lat']) && isset($locationAddress['lng'])) {
                    //address location
                    $latitude = $locationAddress['lat'];
                    $longitude = $locationAddress['lng'];
                    $location['lat'] = $locationAddress['lat'];
                    $location['lng']= $locationAddress['lng'];
                    $criteria = $this->_updateLocation($criteria, $locationAddress);
                }
            }
        }

        $radiusEarth = $this->getRadiusEarth();
        $collection->addFieldToSelect(new \Zend_Db_Expr('*, TRUNCATE(ACOS( SIN( RADIANS( `lat` ) ) * SIN( RADIANS( ' . $latitude . ' ) ) + COS( RADIANS( `lat` ) ) * COS( RADIANS( ' . $latitude . ' )) * COS( RADIANS( `lng` ) - RADIANS( ' . $longitude . ' )) ) * ' . $radiusEarth . ',0)', '*'), 'distance');

        $showRadius = $this->config->getFilterRadius();
        $filterByRadius = true;
        if (!$showRadius && $countryId && count($address) == 1 && isset($address['country_id'])) {
            $filterByRadius = false;
        }

        if ($filterByRadius) {
            $collection->getSelect()->where(new \Zend_Db_Expr('ACOS( SIN( RADIANS( `lat` ) ) * SIN( RADIANS( ' . $latitude . ' ) ) + COS( RADIANS( `lat` ) ) * COS( RADIANS( ' . $latitude . ' )) * COS( RADIANS( `lng` ) - RADIANS( ' . $longitude . ' )) ) * ' . $radiusEarth) . '<' . $radius);
        }

        $searchResults->setTotalCount($collection->getSize());

        $fieldOrder = $this->_getFieldToSort($location);

        $collection->addOrder($fieldOrder, SortOrder::SORT_ASC);

        //pagination settings
        $pagination = $this->config->isEnabledPagination();
        if ($pagination) {
            $collection->setCurPage($criteria->getCurrentPage());
            $collection->setPageSize($this->config->getPageSize());
        } else {
            $collection->setCurPage(1);
            $collection->setPageSize($this->config->getPageSize());
        }
        
        $searchResults->setSearchCriteria($criteria);
        $blocks = [];
        /** @var Block $blockModel */
        foreach ($collection as $blockModel) {
            $blockData = $this->dataItemFactory->create();

            $data = $blockModel->getData();
            if (!empty($data['website'])) {
                $website = $data['website'];
                if (!preg_match('/http/i', $website) && !preg_match('/https/', $website)) {
                    $website = 'https://' . $website;
                    $data['website'] = $website;
                }
            }
            if (!empty($data['region_id'])) {
                $data['region'] = $this->_getRegionName($data['country_id'], $data['region_id'], $data['region']);
            }
            $this->dataObjectHelper->populateWithArray(
                $blockData,
                $data,
                'IWD\StoreLocator\Api\Data\StoreLocatorInterface'
            );
            $blocks[] = $this->dataObjectProcessor->buildOutputDataArray(
                $blockData,
                'IWD\StoreLocator\Api\Data\StoreLocatorInterface'
            );
        }
        $searchResults->setItems($blocks);
        return $searchResults;
    }

    private function getRadiusEarth()
    {
        $option = $this->config->getMetric();
        if ($option == 1) {
            return 6371;
        } elseif ($option == 2) {
            return 3959;
        }

        return 6371;
    }

    private function _isLocationOnly($criteria)
    {
        $status = false;

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'onlyLocation') {
                    $status = (bool)$filter->getValue();
                }
            }
        }
        return $status;
    }

    private function _updateLocation($criteria, $location)
    {
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $filters = $filterGroup->getFilters();
            foreach ($filters as &$filter) {
                if ($filter->getField() == 'lat' || $filter->getField() == 'lng') {
                    $filter->setValue($location[$filter->getField()]);
                }
            }
        }

        return $criteria;
    }

    private function _getFieldToSort($location)
    {
        $field = $this->config->getOrder();
        if ($field == 1) {
            if (!empty($location['lat']) && !empty($location['lng'])) {
                return 'distance ';
            } else {
                return 'position';
            }
        } elseif ($field == 2) {
            return 'position';
        } else {
            return 'name';
        }
    }

    private function _getCountryName($country)
    {
        if ($country) {
            $country = $this->countryFactory->create()->loadByCode($country);
            return $country->getName();
        }

        return false;
    }

    private function _getRegionName($country, $regionId, $region)
    {
        if (!$country) {
            return false;
        }

        if (!$regionId && !$region) {
            return false;
        }

        if (!empty($regionId)) {
            //get region name
            $regionModel = $this->regionFactory->create()->load($regionId);
            if ($regionModel->getId()) {
                return $regionModel->getName();
            }
        }

        if (!empty($region)) {
            return $region;
        }

        return '';
    }

    private function getAddress($address)
    {
        $country = isset($address['country_id']) ? $address['country_id'] : false;

        $address['country'] = $this->_getCountryName($country);

        unset($address['country_id']);
        return $address;
    }
}
