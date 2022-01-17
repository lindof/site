<?php

namespace Knowband\Spinandwin\Controller\Adminhtml\Spinandwin;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class DeleteSchedule extends \Magento\Backend\App\Action
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
        \Knowband\Spinandwin\Helper\Data $helper,
        \Knowband\Spinandwin\Model\ThemeSchedule $themeSchedule
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

    public function execute()
    {
        $schedule_id = $this->getRequest()->getParam('schedule_id');
        if(isset($schedule_id)){
            $themeScheduleModel = $this->sp_themeSchedule->load($schedule_id)->delete();
            $this->messageManager->addSuccessMessage(__('Schedule has been deleted successfully'));
        }else{
            $this->messageManager->addErrorMessage(__('Invalid Data.'));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
        
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Knowband_Spinandwin::main');
    }
}
