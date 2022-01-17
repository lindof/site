<?php

namespace Knowband\Spinandwin\Controller\Adminhtml\Spinandwin;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
//use Mailchimp\Mailchimp;
class Index extends \Magento\Backend\App\Action
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
    }

    public function execute()
    {
        //require_once(Mage::getBaseDir('lib') . '/EZComponents/Base/src/base.php');
//        $mailchimp = new Mailchimp('9f4f6b1197e800bbec93a73daca904e6-us16');
//        $result = $mailchimp->get('lists');
//        d($result);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Knowband_Spinandwin::spinwin_backend');
        $resultPage->getConfig()->getTitle()->prepend(__('Spin and Win'));
        $resultPage->addBreadcrumb(__('Knowband'), __('Knowband'));
        $resultPage->addBreadcrumb(__('Spin and Win'), __('Spin and Win'));

        
        if ($this->getRequest()->getParam('store')) {
            $scope_id = $this->sp_storeManager->getStore($this->getRequest()->getParam('store'))->getId();
            $scope = "stores";
        } elseif ($this->getRequest()->getParam('website')) {
            $scope_id = $this->sp_storeManager->getWebsite($this->getRequest()->getParam('website'))->getId();
            $scope = "websites";
        } elseif ($this->getRequest()->getParam('group')) {
            $scope_id = $this->sp_storeManager->getGroup($this->getRequest()->getParam('group'))->getWebsite()->getId();
            $scope = "groups";
        } else {
            $scope = "default";
            $scope_id = 0;
        }
        if ($this->sp_request->isPost()) {
            $post_data = $this->sp_request->getPost();
            unset($post_data["form_key"]);
            $logoImage = $this->getRequest()->getFiles('vss_spinandwin');
            $logoImage = $logoImage['general_settings']['logo_image'];
            $fileName = ($logoImage && array_key_exists('name', $logoImage)) ? $logoImage['name'] : null;
            if ($logoImage && $fileName) {
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $fileName = 'kb_logo.'.$extension;
                $logoImage['name'] = $fileName;
                
                // Added by Dhruw
                $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $path = $mediaDirectory->getAbsolutePath('Knowband_Spinandwin');
                $mask = $path . '/'. $fileName;
                $matches = glob($mask);
                if (!empty($matches))
                    array_map('unlink', $matches);
                // Ends
                try {
                    
                    $uploader = $this->_objectManager->create(
                        '\Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => $logoImage]
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                    $uploader->setFilesDispersion(false);
                    $imageAdapterFactory = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')
                        ->create();
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setAllowCreateFolders(true);
                    $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save(
                        $mediaDirectory
                            ->getAbsolutePath('Knowband_Spinandwin')
                    );
                    $this->sp_resource->saveConfig("knowband/spinandwin/logo", json_encode(array('image' => $fileName)), $scope, $scope_id);
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }else{
            }
            
            // Added by Dhruw --for Pull out image update saving into config 
            $pulloutImage = $this->getRequest()->getFiles('vss_spinandwin');
            $pulloutImage = $pulloutImage['general_settings']['pullout_image'];
            $fileName = ($pulloutImage && array_key_exists('name', $pulloutImage)) ? $pulloutImage['name'] : null;
            if ($pulloutImage && $fileName) {
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $fileName = 'kb_pullout_image.'.$extension;
                $pulloutImage['name'] = $fileName;
                $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $path = $mediaDirectory->getAbsolutePath('Knowband_Spinandwin');
                $mask = $path . '/'. $fileName;
                $matches = glob($mask);
                if (!empty($matches))
                    array_map('unlink', $matches);

                try {
                    
                    $uploader = $this->_objectManager->create(
                        '\Magento\MediaStorage\Model\File\Uploader',
                        ['fileId' => $pulloutImage]
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                    $uploader->setFilesDispersion(false);
                    $imageAdapterFactory = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')
                        ->create();
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setAllowCreateFolders(true);
//                    $mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
                    $result = $uploader->save(
                        $mediaDirectory
                            ->getAbsolutePath('Knowband_Spinandwin')
                    );
                    $this->sp_resource->saveConfig("knowband/spinandwin/pullout", json_encode(array('image' => $fileName)), $scope, $scope_id);
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }
            //Ends
            
            if ($post_data['vss_spinandwin']['email_settings']['coupon_display_options'] != 1) {
                $this->sp_helper->saveEmailTemplate(
                    $post_data['vss_spinandwin']['email_settings']['template_id'],
                    $post_data['vss_spinandwin']['email_settings']['email_template'],
                    $post_data['vss_spinandwin']['email_settings']['email_subject'],
                    $post_data['vss_spinandwin']["email_settings"]["email_content"]
                );
            }

            $value = json_encode($post_data['vss_spinandwin']);
            $this->sp_resource->saveConfig("knowband/spinandwin/settings", $value, $scope, $scope_id);

            $this->messageManager->addSuccess(__('Settings saved successfully.'));
            
            if(!class_exists('Mobile_Detect')){
                $this->messageManager->addErrorMessage(__('Mobile Detect library is not installed for the module, kindly check the installation steps in the User Manual.'));
            }            
            
            $types = array('config');
            foreach ($types as $type) {
                $this->sp_cacheTypeList->cleanType($type);
            }
            foreach ($this->sp_cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl('*/*/');
            return $resultRedirect;
        }

        return $resultPage;
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Knowband_Spinandwin::main');
    }
}
