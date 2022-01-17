<?php

namespace IWD\StoreLocator\Api\Data;

/**
 * Interface StoreLocatorInterface
 * @package IWD\StoreLocator\Api\Data
 */
interface StoreLocatorInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const LOCATOR_ID                = 'item_id';
    const NAME                      = 'name';
    const DESCRIPTION               = 'description';
    const COUNTRY_ID                = 'country_id';
    const REGION_ID                 = 'region_id';
    const REGION                    = 'region';
    const CITY                      = 'city';
    const PHONE                     = 'phone';
    const WEBSITE                   = 'website';
    const POSITION                  = 'position';
    const CREATION_TIME             = 'creation_time';
    const UPDATE_TIME               = 'update_time';
    const IS_ACTIVE                 = 'is_active';
    const POSTAL_CODE               = 'postal_code';
    const STREET                    = 'street';
    const DISTANCE                  = 'distance';
    const LAT                       = 'lat';
    const LNG                       = 'lng';
    
    const ICON                      = 'icon';
    const IMAGE                     = 'image';
    /**#@-*/

    /**
     * Getters
     *
     */
    
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getName();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getCountryId();
    
    /**
     * Get content
     *
     * @return int|null
     */
    public function getRegionId();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getRegion();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getCity();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getPhone();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getStreet();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getPostalCode();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getWebsite();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getPosition();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getCreationTime();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getUpdateTime();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getLat();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getLng();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function isActive();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getDistance();
    
    /**
     * Get content
     *
     * @return string|null
     */
    public function getImage();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getIcon();

    /**
     * Setters
     *
     */

    /**
     * Set content
     *
     * @param int $id
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setId($id);

    /**
     * @param $name
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setName($name);

    /**
     * @param $description
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setDescription($description);
    
    /**
     * @param $countrId
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setCountryId($countrId);

    /**
     * @param $regionId
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setRegionId($regionId);
    
    /**
     * @param $region
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setRegion($region);
    
    /**
     * @param $city
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setCity($city);
    
    /**
     * @param $phone
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setPhone($phone);
    
    /**
     * @param $website
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setWebsite($website);
    
    /**
     * @param $street
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setStreet($street);
    
    /**
     * @param $zip
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setPostalCode($zip);
    
    /**
     * @param $position
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setPosition($position);
    
    /**
     * @param $creationTime
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setCreationTime($creationTime);
    
    /**
     * @param $updateTime
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setUpdateTime($updateTime);
    
    /**
     * @param $lat
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setLat($lat);
    
    /**
     * @param $lng
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setLng($lng);
    
    /**
     * @param $isActive
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setIsActive($isActive);
    
    /**
     * @param $distance
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setDistance($distance);

    /**
     * @param $image
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setImage($image);
    
    /**
     * @param $icon
     * @return \IWD\StoreLocator\Model\Item
     */
    public function setIcon($icon);
}
