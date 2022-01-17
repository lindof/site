<?php

namespace IWD\StoreLocator\Model;


use Magento\Directory\Model\Region;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface as PsrLogger;
use IWD\StoreLocator\Helper\Config;

abstract class AbstractGeoLocation
{
    public $itemRepository;

    public $region;

    public $curlFactory;

    public $scopeConfig;

    public $config;

    public $logger;

    public function __construct(
        Region $region,
        CurlFactory $curlFactory,
        ScopeConfigInterface $scopeConfig,
        Config $config,
        PsrLogger $logger
    )
    {
        $this->region = $region;
        $this->curlFactory = $curlFactory;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param $item
     * @return mixed
     */
    abstract public function getPosition($item);

    abstract public function prepareUrl($modelStore);

    public function getCoordinatesByAddress($address)
    {
        $addressObject = new \Magento\Framework\DataObject();
        $addressObject->setData($address);
        $result = $this->getPosition($addressObject);
        return $result;
    }

    public function prepareState($data)
    {
        $region = (isset($data['region'])) ? $data['region'] : '';
        if (!empty($region)) {
            return $region;
        }
        try {
            $regionId = (isset($data['region_id'])) ? $data['region_id'] : '';
            return $this->region->load($regionId)->getDefaultName();
        } catch (\Exception $e) {
            return '';
        }
    }
}
