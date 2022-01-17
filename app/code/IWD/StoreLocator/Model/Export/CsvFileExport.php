<?php

namespace IWD\StoreLocator\Model\Export;

use IWD\StoreLocator\Api\ItemRepositoryInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class CsvFileExport
 * @package IWD\StoreLocator\Model\Export
 */
class CsvFileExport
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $directory;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * CsvFileExport constructor.
     * @param Filesystem $filesystem
     * @param ItemRepositoryInterface $itemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Filesystem $filesystem,
        ItemRepositoryInterface $itemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->itemRepository = $itemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * @return array
     */
    public function exportStoresToFile()
    {
        $file = 'export/iwdsl' . md5(microtime()) . '.csv';

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $data = $this->getStoreLocatorItemData();

        if (!empty($data) && isset($data[0])) {
            $header = $data[0]->getData();
            unset($header['item_id']);
            $header = array_keys($header);
            $stream->writeCsv($header);

            foreach ($data as $item) {
                $item = $item->getData();
                unset($item['item_id']);
                $stream->writeCsv($item);
            }
        }

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true // can delete file after use
        ];
    }

    /**
     * @return \IWD\StoreLocator\Api\Data\StoreLocatorInterface[]
     */
    private function getStoreLocatorItemData()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->itemRepository->getList($searchCriteria)->getItems();
    }
}
