<?php

namespace Knowband\Spinandwin\Controller\Adminhtml\Spinandwin;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class ValidateSchedule extends \Magento\Backend\App\Action
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
        \Knowband\Spinandwin\Model\ThemeScheduleFactory $themeSchedule
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
        $response = [];
        $is_error = false;
        $from_date_error = false;
        $to_date_error = false;
        $slot_already_available = false;
        $post_data = $this->getRequest()->getParams();
        if (isset($post_data['vss_spinandwin']['from_date']) && isset($post_data['vss_spinandwin']['to_date'])) {
            $from_date = $post_data['vss_spinandwin']['from_date'];
            $to_date = $post_data['vss_spinandwin']['to_date'];
            $schedule_id = (isset($post_data['schedule_id']) && !empty($post_data['schedule_id']))?$post_data['schedule_id']:0;
            $themeScheduleModel = $this->sp_themeSchedule->create()->getCollection()
                    ->addFieldToFilter('schedule_id', ['neq' => $schedule_id])
                    ->addFieldToFilter('from_date', ['lteq' => $from_date])
                    ->addFieldToFilter('to_date', ['gteq' => $from_date]);
            if ($themeScheduleModel->getSize()) {
                $from_date_error = true;
                $is_error = true;
            }
            
            $themeScheduleModel2 = $this->sp_themeSchedule->create()->getCollection()
                    ->addFieldToFilter('schedule_id', ['neq' => $schedule_id])
                    ->addFieldToFilter('from_date', ['gteq' => $from_date])
                    ->addFieldToFilter('from_date', ['lteq' => $to_date]);

             if ($themeScheduleModel2->getSize()) {
                $slot_already_available = true;
                $is_error = true;
            }
            
            $themeScheduleModel3 = $this->sp_themeSchedule->create()->getCollection()
                    ->addFieldToFilter('schedule_id', ['neq' => $schedule_id])
                    ->addFieldToFilter('from_date', ['lteq' => $to_date])
                    ->addFieldToFilter('to_date', ['gteq' => $to_date]);
            

            if ($themeScheduleModel3->getSize()) {
                $to_date_error = true;
                $is_error = true;
            }
            
             $themeScheduleModel4 = $this->sp_themeSchedule->create()->getCollection()
                    ->addFieldToFilter('schedule_id', ['neq' => $schedule_id])
                    ->addFieldToFilter('to_date', ['gteq' => $from_date])
                    ->addFieldToFilter('to_date', ['lteq' => $to_date]);
             
            if ($themeScheduleModel4->getSize()) {
                $slot_already_available = true;
                $is_error = true;
            }
            
            $response = [
                'from_date_error' => $from_date_error,
                'to_date_error' => $to_date_error,
                'slot_already_available' => $slot_already_available,
                'error' => $is_error,
            ];
        }
        $this->getResponse()->setBody(json_encode($response));
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Knowband_Spinandwin::main');
    }
}
