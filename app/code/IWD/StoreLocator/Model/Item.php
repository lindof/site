<?php

namespace IWD\StoreLocator\Model;

use IWD\StoreLocator\Api\Data\StoreLocatorInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Item
 * @package IWD\StoreLocator\Model
 */
class Item extends \Magento\Framework\Model\AbstractModel implements StoreLocatorInterface, IdentityInterface
{
    /**#@+
     * Page's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * StoreLocator store cache tag
     */
    const CACHE_TAG = 'storelocator_item';

    /**
     * @var string
     */
    protected $_cacheTag = 'storelocator_item';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'storelocator_item';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('IWD\StoreLocator\Model\ResourceModel\Item');
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        return parent::load($id, $field);
    }

    /**
     * Receive item store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    public function getId()
    {
        return parent::getData(self::LOCATOR_ID);
    }
    
    public function getName()
    {
        return $this->getData(self::NAME);
    }
    
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }
    
    public function getCountryId()
    {
        return $this->getData(self::COUNTRY_ID);
    }
    
    public function getRegionId()
    {
        return $this->getData(self::REGION_ID);
    }
    
    public function getRegion()
    {
        return $this->getData(self::REGION);
    }
    
    public function getCity()
    {
        return $this->getData(self::CITY);
    }
    
    public function getStreet()
    {
        return $this->getData(self::STREET);
    }
    
    public function getPostalCode()
    {
        return $this->getData(self::POSTAL_CODE);
    }
    
    public function getPhone()
    {
        return $this->getData(self::PHONE);
    }
    
    public function getWebsite()
    {
        return $this->getData(self::WEBSITE);
    }
    
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }
    
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }
    
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    public function getLat()
    {
        return $this->getData(self::LAT);
    }
    
    public function getLng()
    {
        return $this->getData(self::LNG);
    }

    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }
    
    public function getIcon()
    {
        return $this->getData(self::ICON);
    }

    public function isActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritDoc}
     * @see \IWD\StoreLocator\Api\Data\StoreLocatorInterface::getDistance()
     */
    public function getDistance()
    {
        return $this->getData(self::DISTANCE);
    }

    public function setId($id)
    {
        return $this->setData(self::LOCATOR_ID, $id);
    }
    
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }
    
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }
    
    public function setCountryId($countrId)
    {
        return $this->setData(self::COUNTRY_ID, $countrId);
    }
    
    public function setRegionId($regionId)
    {
        return $this->setData(self::REGION_ID, $regionId);
    }
    
    public function setRegion($region)
    {
        return $this->setData(self::REGION, $region);
    }
    
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }
    
    public function setPhone($phone)
    {
        return $this->setData(self::PHONE, $phone);
    }
    
    public function setStreet($street)
    {
        return $this->setData(self::STREET, $street);
    }
    
    public function setPostalCode($zip)
    {
        return $this->setData(self::POSTAL_CODE, $zip);
    }
    
    public function setWebsite($website)
    {
        return $this->setData(self::WEBSITE, $website);
    }
    
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }
    
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }
    
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }
    
    public function setLat($lat)
    {
        return $this->setData(self::LAT, $lat);
    }
    
    public function setLng($lng)
    {
        return $this->setData(self::LNG, $lng);
    }

    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }
    
    public function setIcon($icon)
    {
        return $this->setData(self::ICON, $icon);
    }
    
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    public function setDistance($distance)
    {
        return $this->setData(self::DISTANCE, $distance);
    }
    
    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
