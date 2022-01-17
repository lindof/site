<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Cynoinfotech\Shippingrules\Model\ShippingrulesFactory
     */
    protected $_prFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Cynoinfotech\PaymentRestrictions\Model\PaymentrestrictionsFactory $prFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_prFactory = $prFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cynoinfotech_PaymentRestrictions::delete');
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_prFactory->create();
        
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                $model->load($id);
                $title = $model->getId();
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The Payment Restrictions has been deleted.'));
                // go to grid
                $this->_eventManager->dispatch(
                    'adminhtml_paymentrestrictions_on_delete',
                    ['title' => $title, 'status' => 'success']
                );
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['rule_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a Payment Restriction Rules to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
