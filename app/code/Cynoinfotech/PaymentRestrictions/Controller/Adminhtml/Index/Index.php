<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Cynoinfotech_PaymentRestrictions::paymentrestrictions');
        $resultPage->addBreadcrumb(__('paymentrestrictions'), __('paymentrestrictions'));
        $resultPage->addBreadcrumb(__('Manage paymentrestrictions'), __('Manage paymentrestrictions'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Payment Restrictions'));
        
        return $resultPage;
    }
}
