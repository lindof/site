<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Email: vladokrushko@gmail.com
 * Date: 06.08.2018
 * Time: 12:12
 */

namespace IWD\StoreLocator\Model\GeoLocation\Here;

use IWD\StoreLocator\Model\AbstractGeoLocation;
use Magento\Framework\Exception\LocalizedException;

class GeoLocation extends AbstractGeoLocation
{
    public function prepareUrl($modelStore)
    {
        $data = $modelStore->getData();
        $appId = $this->config->getHereAppId();
        $appCode = $this->config->getHereAppCode();
        $address = implode(',', $data);
        $queryStr = '';
        $baseUrl = "https://geocoder.api.here.com/6.2/geocode.json?app_id=$appId&app_code=$appCode";
        $data = [
            "&searchtext" => urlencode($address)
        ];

        foreach ($data as $key => $value) {
            $queryStr .= $key . "=" . $value;
        }

        return $baseUrl . $queryStr;
    }


    /**
     * @param $item
     * @return array|mixed
     */
    public function getPosition($item)
    {
        $position = [];
        try {
            $url = $this->prepareUrl($item);
            $http = $this->curlFactory->create();
            $config = ['timeout' => 5, 'header' => false, 'verifypeer' => false];
            $http->setConfig($config);
            $http->write(\Zend_Http_Client::GET, $url, '1.1');

            $response = $http->read();
            if ($response === false) {
                throw new LocalizedException(__('response is empty'));
            }
            $data = json_decode($response);

            if (!isset($data->Response)) {
                throw new LocalizedException(__('response is empty'));
            }
            $position['lat'] = $data->Response->View[0]->Result[0]->Location->NavigationPosition[0]->Latitude;
            $position['lng'] = $data->Response->View[0]->Result[0]->Location->NavigationPosition[0]->Longitude;
        } catch (\Exception $e) {
            $position['lat'] = '0';
            $position['lng'] = '0';
        }

        return $position;
    }
}