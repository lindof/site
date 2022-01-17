<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Cynoinfotech\PaymentRestrictions\Model\PaymentrestrictionsFactory $prModelFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_prModelFactory = $prModelFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cynoinfotech_PaymentRestrictions::save');
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Cynoinfotech_PaymentRestrictions::paymentrestrictions')
            ->addBreadcrumb(__('PaymentRestrictions'), __('Payment Restrictions'))
            ->addBreadcrumb(__('Payment Restrictions'), __('Payment Restrictions'));
        return $resultPage;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_prModelFactory->create();
        
        if ($id) {
            $model->load($id);

            if (!$model->getPrId()) {
                $this->messageManager->addError(__('This Tab no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
       
        $this->_coreRegistry->register('paymentrestrictions', $model);
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Page') : __('Select'),
            $id ? __('Edit Page') : __('Select')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('PaymentRestrictions'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('Add Payment Restrictions'));
        
        return $resultPage;
    }
}
