<?php

namespace IWD\StoreLocator\Controller\Adminhtml\Item;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package IWD\StoreLocator\Controller\Adminhtml\Item
 */
class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'IWD_StoreLocator::Item';

    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \IWD\StoreLocator\Model\Image
     */
    protected $imageModel;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \IWD\StoreLocator\Model\Image $imageModel
     */
    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \IWD\StoreLocator\Model\Image $imageModel
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->uploaderFactory = $uploaderFactory;
        $this->imageModel = $imageModel;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->dataProcessor->filter($data);
            
            $model = $this->_objectManager->create('IWD\StoreLocator\Model\Item');

            $id = $this->getRequest()->getParam('item_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            /*$this->_eventManager->dispatch(
                'cms_page_prepare_save',
                ['page' => $model, 'request' => $this->getRequest()]
            );*/

            if (!$this->dataProcessor->validate($data)) {
                return $resultRedirect->setPath('*/*/edit', ['item_id' => $model->getId(), '_current' => true]);
            }

            try {
                $iconName = $this->uploadFile('icon', $this->imageModel->getBaseDir(), $data);
                $model->setIcon($iconName);
                
                $imageName = $this->uploadFile('image', $this->imageModel->getBaseDir(), $data);
                $model->setImage($imageName);

                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved this store.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['item_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, $e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['item_id' => $this->getRequest()->getParam('item_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $input
     * @param $destinationFolder
     * @param $data
     * @return string
     * @throws \Exception
     */
    protected function uploadFile($input, $destinationFolder, $data)
    {
        try {
            if (isset($data[$input]['delete'])) {
                return '';
            } else {
                $uploader = $this->uploaderFactory->create(['fileId' => $input]);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($destinationFolder);
                return $result['file'];
            }
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                throw new LocalizedException(__($e->getMessage()));
            } else {
                if (isset($data[$input]['value'])) {
                    return $data[$input]['value'];
                }
            }
        }
        return '';
    }
}
