<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Controller\Adminhtml\Index;

class MassDelete extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;

    protected $resultPageFactory;

    protected $_prCollectionFactory;
   
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Cynoinfotech\PaymentRestrictions\Model\ResourceModel\Paymentrestrictions\CollectionFactory $prCollectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_prCollectionFactory = $prCollectionFactory;
        $this->filter = $filter;
        parent::__construct($context);
    }

    public function execute()
    {
        
        $collection = $this->filter->getCollection($this->_prCollectionFactory->create());
        $count = 0;
        foreach ($collection as $colle) {
            $colle->delete();
            $count++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
