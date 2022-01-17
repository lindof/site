<?php

namespace IWD\StoreLocator\Model\Import;

use Psr\Log\LoggerInterface as PsrLogger;

/**
 * Class AbstractImport
 * @package IWD\StoreLocator\Model\Import
 */
abstract class AbstractImport
{
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    private $regionFactory;

    /**
     * @var Item
     */
    private $storeLocatorItem;

    /**
     * @var PsrLogger
     */
    private $logger;

    /**
     * @var int
     */
    private $countImportedSuccess;

    /**
     * @var int
     */
    private $countImportedError;

    /**
     * @var array
     */
    private $countryStates;

    /**
     * AbstractImport constructor.
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \IWD\StoreLocator\Model\Item $storeLocatorItem
     * @param PsrLogger $logger
     */
    public function __construct(
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \IWD\StoreLocator\Model\Item $storeLocatorItem,
        PsrLogger $logger
    ) {
        $this->regionFactory = $regionFactory;
        $this->storeLocatorItem = $storeLocatorItem;
        $this->logger = $logger;

        $this->countryStates = [];
        $this->countImportedSuccess = 0;
        $this->countImportedError = 0;
    }

    /**
     * @return array
     */
    abstract public function getImportItemsData();

    /**
     * @return array
     */
    abstract public function getImportItemsKeys();

    /**
     * @return int
     */
    public function getCountImportedSuccess()
    {
        return $this->countImportedSuccess;
    }

    /**
     * @return int
     */
    public function getCountImportedError()
    {
        return $this->countImportedError;
    }

    /**
     * Import stores for store locator
     */
    public function importStores()
    {
        $this->countImportedSuccess = 0;
        $this->countImportedError = 0;

        $importItemsData = $this->getImportItemsData();
        foreach ($importItemsData as $data) {
            try {
                $this->importItem($data);
                $this->countImportedSuccess++;
            } catch (\Exception $e) {
                $this->countImportedError++;
                $this->logger->error('IWD Store Locator Error Import: ' . $e->getMessage());
            }
        }
    }

    /**
     * @param $data
     */
    private function importItem($data)
    {
        $model = clone $this->storeLocatorItem;

        $dataWithKey = array_combine($this->getImportItemsKeys(), $data);

        if (isset($dataWithKey['region']) && $dataWithKey['country_id']) {
            $dataWithKey['region_id'] = $this->_getRegionId($dataWithKey['country_id'], $dataWithKey['region']);
        }

        $dataWithKey['icon'] = (strlen($dataWithKey['icon']) > 1
            && !in_array(substr($dataWithKey['icon'], 0, 1), ['/','\\']))
            ? '/' . $dataWithKey['icon'] : $dataWithKey['icon'];

        $dataWithKey['image'] = (strlen($dataWithKey['image']) > 1
            && !in_array(substr($dataWithKey['image'], 0, 1), ['/','\\']))
            ? '/' . $dataWithKey['image'] : $dataWithKey['image'];

        $model->setData($dataWithKey);
        $model->save();
    }

    /**
     * @param $countryId
     * @param $region
     * @return bool
     */
    private function _getRegionId($countryId, $region)
    {
        if (!isset($this->countryStates[$countryId][$region])) {
            $regionObject = $this->regionFactory->create()->loadByCode($region, $countryId);
            if ($regionObject) {
                $this->countryStates[$countryId][$region] = $regionObject->getRegionId();
            }
        }

        return isset($this->countryStates[$countryId][$region])?$this->countryStates[$countryId][$region]:false;
    }
}
