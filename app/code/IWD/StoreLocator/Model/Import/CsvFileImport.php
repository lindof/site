<?php

namespace IWD\StoreLocator\Model\Import;

use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface as PsrLogger;

/**
 * Class CsvFileImport
 * @package IWD\StoreLocator\Model\Import
 */
class CsvFileImport extends AbstractImport
{
    /**
     * @var \Magento\Framework\File\Csv
     */
    private $csvParser;

    /**
     * @var array
     */
    private $importItemsData;

    /**
     * @var array
     */
    private $importItemsKeys;

    /**
     * @var array
     */
    private $mapImportItemsKeys = [
        'title' => 'name',
        'desc' => 'description',
        'latitude' => 'lat',
        'longitude' => 'lng',
    ];

    /**
     * CsvFileImport constructor.
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \IWD\StoreLocator\Model\Item $storeLocatorItem
     * @param PsrLogger $logger
     * @param \Magento\Framework\File\Csv $csvParser
     */
    public function __construct(
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \IWD\StoreLocator\Model\Item $storeLocatorItem,
        PsrLogger $logger,
        \Magento\Framework\File\Csv $csvParser
    ) {
        $this->csvParser = $csvParser;
        parent::__construct($regionFactory, $storeLocatorItem, $logger);
    }

    /**
     * @return array
     */
    public function getImportItemsData()
    {
        return $this->importItemsData;
    }

    /**
     * @return array
     */
    public function getImportItemsKeys()
    {
        return $this->importItemsKeys;
    }

    /**
     * @param $fileName
     */
    public function importStoresFromFile($fileName)
    {
        $this->readDataFromFile($fileName);
        $this->mapImportItemsKeys();

        $this->importStores();
    }

    /**
     * Read data and keys from csv file
     *
     * @param $fileName
     * @throws LocalizedException
     */
    private function readDataFromFile($fileName)
    {
        $importItemsData = $this->getFileData($fileName);
        if (isset($importItemsData[0])) {
            $this->importItemsKeys = $importItemsData[0];
            unset($importItemsData[0]);
        } else {
            throw new LocalizedException(__('File format is not correct'));
        }

        $this->importItemsData = $importItemsData;
    }

    /**
     * Convert old format of import file to new format
     */
    private function mapImportItemsKeys()
    {
        $map = $this->mapImportItemsKeys;

        foreach ($this->importItemsKeys as $id => $importItemsKey) {
            if (isset($map[$importItemsKey])) {
                $this->importItemsKeys[$id] = $map[$importItemsKey];
            }
        }
    }

    /**
     * Retrieve data from csv file
     *
     * @param $file
     * @return array
     * @throws LocalizedException
     */
    private function getFileData($file)
    {
        try {
            $data = [];
            if (file_exists($file)) {
                $this->csvParser->setDelimiter(',');
                $data = $this->csvParser->getData($file);
            }
        } catch (\Exception $e) {
            throw new LocalizedException(__('Error file format: ' . $e->getMessage()));
        }

        return $data;
    }
}
