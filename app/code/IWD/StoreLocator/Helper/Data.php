<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Email: vladokrushko@gmail.com
 * Date: 06.08.2018
 * Time: 13:26
 */

namespace IWD\StoreLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use IWD\StoreLocator\Model\GeoLocation\Google\GeoLocation as googleGeoLocation;
use IWD\StoreLocator\Model\GeoLocation\Here\GeoLocation as hereGeoLocation;

class Data extends AbstractHelper
{
    public $config;
    public $googleGeoLocation;
    public $hereGeoLocation;
    public $apiType;
    public $logger;

    public function __construct(
        Config $config,
        googleGeoLocation $googleGeoLocation,
        hereGeoLocation $hereGeoLocation
    )
    {
        $this->googleGeoLocation = $googleGeoLocation;
        $this->hereGeoLocation = $hereGeoLocation;
        $this->config = $config;
        $this->apiType = $this->config->getApiType();
    }

    public function getApiType()
    {
        return $this->apiType;
    }


    public function getGeoLocator()
    {
        $apiType = $this->config->getApiType();
        switch ($apiType) {
            case 'google':
                return $this->googleGeoLocation;
            case 'here':
                return $this->hereGeoLocation;
            default:
                throw new LocalizedException(__('Api Type  <' . $apiType . '> is not supported'));
        }
    }
}