<?php

namespace Knowband\Spinandwin\Controller\Adminhtml\Spinandwin;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class AddSchedule extends \Magento\Backend\App\Action
{
    public $resultPageFactory = false;
    public $sp_request;
    public $sp_resource;
    public $sp_storeManager;
    public $sp_cacheFrontendPool;
    public $sp_cacheTypeList;
    protected $sp_helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Knowband\Spinandwin\Model\ThemeSchedule $themeSchedule,
        \Knowband\Spinandwin\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->sp_request = $request;
        $this->resultPageFactory = $resultPageFactory; 
        $this->sp_resource = $resource;
        $this->sp_storeManager = $storeManager;
        $this->sp_cacheFrontendPool = $cacheFrontendPool;
        $this->sp_cacheTypeList = $cacheTypeList;
        $this->_filesystem = $fileSystem;
        $this->directory_list = $directory_list;  
        $this->sp_helper = $helper;
        $this->sp_themeSchedule = $themeSchedule;
    }

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Knowband_Spinandwin::spinwin_backend');
        $resultPage->getConfig()->getTitle()->prepend(__('Add/Edit Schedule'));

        if ($this->sp_request->isPost()) {
            $post_data = $this->sp_request->getPostValue();
            if (isset($post_data['vss_spinandwin'])) {
                $status = isset($post_data['vss_spinandwin']['schedule_status']) ? (int) $post_data['vss_spinandwin']['schedule_status'] : 0;

                if (isset($post_data['schedule_id']) && $post_data['schedule_id']) {
                    $themeScheduleModel = $this->sp_themeSchedule->load($post_data['schedule_id']);
                } else {
                    $themeScheduleModel = $this->sp_themeSchedule;
                }
                $themeScheduleModel->setStatus($status)
                        ->setFromDate($post_data['vss_spinandwin']['from_date'])
                        ->setToDate($post_data['vss_spinandwin']['to_date'])
                        ->setSettings(json_encode($post_data['vss_spinandwin']))
                        ->save();
            } else {
                $this->messageManager->addErrorMessage(__('Invalid Data.'));
            }

            $this->messageManager->addSuccessMessage(__('Schedule has been saved successfully.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/index');
            return $resultRedirect;
        }

        return $resultPage;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Knowband_Spinandwin::main');
    }
}
