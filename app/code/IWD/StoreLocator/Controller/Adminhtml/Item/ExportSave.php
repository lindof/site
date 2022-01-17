<?php

namespace IWD\StoreLocator\Controller\Adminhtml\Item;

use Magento\Backend\App\Action;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use IWD\StoreLocator\Model\Export\CsvFileExport;

/**
 * Class ExportSave
 * @package IWD\StoreLocator\Controller\Adminhtml\Item
 */
class ExportSave extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'IWD_StoreLocator::Item';

    /**
     * @var CsvFileExport
     */
    private $storeLocatorExport;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * ExportSave constructor.
     * @param Action\Context $context
     * @param FileFactory $fileFactory
     * @param CsvFileExport $storeLocatorExport
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        CsvFileExport $storeLocatorExport
    ) {
        $this->storeLocatorExport = $storeLocatorExport;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $file = $this->storeLocatorExport->exportStoresToFile();
            return $this->fileFactory->create('export.csv', $file, DirectoryList::VAR_DIR);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
