<?php

namespace Knowband\Spinandwin\Controller\Adminhtml\Spinandwin;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\View\Result\PageFactory;

class ExportExcel extends \Magento\Framework\App\Action\Action {

    protected $sp_resultRawFactory;
    protected $sp_helper;
    protected $sp_scopeConfig;
    protected $sp_transportBuilder;
    protected $directoryList;
    protected $csvProcessor;
    protected $fileFactory;
    protected $_spinuserFactory;
    protected $csvWriter;

    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Knowband\Spinandwin\Helper\Data $helper, \Magento\Framework\View\Result\PageFactory $resultRawFactory, LayoutFactory $viewLayoutFactory, \Magento\Framework\View\LayoutInterface $layout, \Magento\Framework\App\Filesystem\DirectoryList $directoryList, \Magento\Framework\File\Csv $csvProcessor, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \Knowband\Spinandwin\Model\SpinUserData $spinuserModel, \Magento\Framework\File\Csv $csvWriter
    ) {
        parent::__construct($context);

        $this->csvWriter = $csvWriter;
        $this->sp_scopeConfig = $scopeConfig;
        $this->sp_helper = $helper;
        $this->fileFactory = $fileFactory;
        $this->sp_resultRawFactory = $resultRawFactory;
        $this->_layout = $layout;
        $this->_viewLayoutFactory = $viewLayoutFactory;
        $this->directoryList = $directoryList;
        $this->csvProcessor = $csvProcessor;
        $this->_spinuserFactory = $spinuserModel;
    }

    public function execute() {

        // ========== start csv export ==========        
        $result = [];
        $result[] = [
            __('Id'),
            __('First Name'),
            __('Last Name'),
            __('Email'),
            __('Created Date'),
        ];

        $users = $this->_spinuserFactory;
        $collection = $users->getCollection();
        foreach ($collection as $item) {
            $data = $item->getData();

            $result[] = [
                $data['id'],
                $data['fname'],
                $data['lname'],
                $data['email'],
                $data['date_added'],
            ];
        }

        $fileDirectory = \Magento\Framework\App\Filesystem\DirectoryList::MEDIA;
        $fileName = 'spinandwin_user_list.csv';
        $filePath = $this->directoryList->getPath($fileDirectory) . "/" . $fileName;

        $this->csvWriter
                ->setEnclosure('"')
                ->setDelimiter(',')
                ->saveData($filePath, $result);

        $this->fileFactory->create(
                $fileName, [
            'type' => "filename",
            'value' => $fileName,
            'rm' => true,
                ], \Magento\Framework\App\Filesystem\DirectoryList::MEDIA, 'text/csv', ''
        );
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw;

        // ========== end csv export ==========        
    }

}
