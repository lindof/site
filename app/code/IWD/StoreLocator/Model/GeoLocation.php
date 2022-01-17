<?php

namespace IWD\StoreLocator\Model;

use IWD\StoreLocator\Api\ItemRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use IWD\StoreLocator\Helper\Config;
use Psr\Log\LoggerInterface as PsrLogger;
use IWD\StoreLocator\Helper\Data;

/**
 * Class GeoLocation
 * @package IWD\StoreLocator\Model
 */
class GeoLocation
{
    /**
     * @var Config
     */
    public $config;
    /**
     * /**
     * @var mixed
     */
    public $apiType;
    public $itemRepository;
    public $logger;
    public $helper;

    /**
     * GeoLocation constructor.
     * @param Config $config
     * @param Data $helper
     * @param ItemRepositoryInterface $itemRepository
     * @param PsrLogger $logger
     */
    public function __construct(
        Config $config,
        Data $helper,
        ItemRepositoryInterface $itemRepository,
        PsrLogger $logger
    )
    {
        $this->itemRepository = $itemRepository;
        $this->config = $config;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    public function fillGeoData()
    {
        $collection = $this->itemRepository->getListWithEmptyLocation();
        $collection->setCurPage(1);
        $pageSize = $this->config->getAutoFillCount();
        $collection->setPageSize($pageSize);
        $geoLocator = $this->helper->getGeoLocator();
        foreach ($collection as $item) {
            try {
                $position = $geoLocator->getPosition($item);
                $item->setData('lat', $position['lat']);
                $item->setData('lng', $position['lng']);
                $item->save();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

}
