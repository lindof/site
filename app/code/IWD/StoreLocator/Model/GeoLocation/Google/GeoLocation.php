<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Email: vladokrushko@gmail.com
 * Date: 06.08.2018
 * Time: 12:12
 */

namespace IWD\StoreLocator\Model\GeoLocation\Google;

use IWD\StoreLocator\Model\AbstractGeoLocation;
use Magento\Framework\Exception\LocalizedException;

class GeoLocation extends AbstractGeoLocation
{

    public function prepareUrl($modelStore)
    {
        $data = $modelStore->getData();
        $address = implode(',', $data);
        $apikey = $this->scopeConfig->getValue('iwd_storelocator/api_settings/google_browser_api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $queryStr = '';
        $baseurl = "https://maps.googleapis.com/maps/api/geocode/json?";
        $data = [
            "&address" => urlencode($address)
        ];

        foreach ($data as $key => $value) {
            $queryStr .= $key . "=" . $value;
        }

        return $baseurl . $queryStr.'&key='.$apikey;
    }

    public function getPosition($item)
    {
        $position = [];

        try {
            $url = $this->prepareUrl($item);
            $http = $this->curlFactory->create();
            $config = ['timeout' => 5, 'header' => false, 'verifypeer' => false];
            $http->setConfig($config);
            $http->write(\Zend_Http_Client::POST, $url, '1.1');

            $response = $http->read();
            if ($response === false) {
                throw new LocalizedException(__('response is empty'));
            }
            $data = json_decode($response);

            if (!isset($data->results[0])) {
                throw new LocalizedException(__('response is empty'));
            }

            $position['lat'] = $data->results[0]->geometry->location->lat;
            $position['lng'] = $data->results[0]->geometry->location->lng;
        } catch (\Exception $e) {
            $position['lat'] = '0';
            $position['lng'] = '0';
        }

        return $position;
    }
}