<?php

namespace IWD\StoreLocator\Controller\Adminhtml\Item;

use IWD\StoreLocator\Model\Import\CsvFileImport;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Class ImportSave
 * @package IWD\StoreLocator\Controller\Adminhtml\Item
 */
class ImportSave extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'IWD_StoreLocator::Item';

    /**
     * @var \IWD\StoreLocator\Model\Import\CsvFileImport
     */
    private $storeLocatorImport;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * ImportSave constructor.
     * @param Action\Context $context
     * @param CsvFileImport $storeLocatorImport
     * @param UploaderFactory $uploaderFactory
     */
    public function __construct(
        Action\Context $context,
        CsvFileImport $storeLocatorImport,
        UploaderFactory $uploaderFactory
    ) {
        $this->storeLocatorImport = $storeLocatorImport;
        $this->uploaderFactory = $uploaderFactory;
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
            $fileName = $this->getFileName();
            $this->storeLocatorImport->importStoresFromFile($fileName);

            $this->addImportMessages();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }

    /**
     * @return mixed
     * @throws LocalizedException
     */
    private function getFileName()
    {
        $uploader = $this->uploaderFactory->create(['fileId' => 'import_file']);
        $file = $uploader->validateFile();

        if (!isset($file['tmp_name'])) {
            throw new LocalizedException(__('Invalid file upload attempt.'));
        }
        return $file['tmp_name'];
    }

    /**
     * @return void
     */
    private function addImportMessages()
    {
        $success = $this->storeLocatorImport->getCountImportedSuccess();
        if ($success > 0) {
            $this->messageManager->addSuccessMessage($success . ' items were imported successfully.');
        }

        $error = $this->storeLocatorImport->getCountImportedError();
        if ($error) {
            $this->messageManager->addErrorMessage($error . ' items were not imported.');
        }
    }
}
