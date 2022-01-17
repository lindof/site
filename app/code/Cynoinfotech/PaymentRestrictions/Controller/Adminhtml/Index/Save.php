<?php
/**
 * @author CynoInfotech Team
 * @package Cynoinfotech_PaymentRestrictions
 */
namespace Cynoinfotech\PaymentRestrictions\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /*
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(
        Action\Context $context,
        \Cynoinfotech\PaymentRestrictions\Model\PaymentrestrictionsFactory $paymentrestrictionsFactory
    ) {
        $this->_paymentrestrictionsFactory = $paymentrestrictionsFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cynoinfotech_PaymentRestrictions::save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if ($data) {
            $model = $this->_paymentrestrictionsFactory->create();
            if (isset($data['methods'])) {
                $data['methods'] = implode(',', $data['methods']);
            } else {
                $data['methods']='';
            }
            if (isset($data['customer_group'])) {
                $data['customer_group'] = implode(',', $data['customer_group']);
            } else {
                $data['customer_group'] = '';
            }
            if (isset($data['stores'])) {
                $data['stores'] = implode(',', $data['stores']);
            } else {
                $data['stores'] ='';
            }
            if (isset($data['days'])) {
                $data['days'] = implode(',', $data['days']);
            } else {
                $data['days'] ='';
            }
            if (isset($data['time_from'])) {
                $data['time_from'] = implode(',', $data['time_from']);
            } else {
                $data['time_from'] ='';
            }
            if (isset($data['time_to'])) {
                $data['time_to'] = implode(',', $data['time_to']);
            } else {
                $data['time_to'] ='';
            }
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
                
            $data = $this->prepareData($data);
            $model->loadPost($data);
            $this->_session->setPageData($model->getData());
            
            try {
                $model->save();
                $this->messageManager->addSuccess(__('Payment Restrictions was successfully saved'));
                $this->_getSession()->setFormData($data);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }

                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __($e->getMessage().'Something went wrong while saving the page.')
                );
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
    
    protected function prepareData($data)
    {
        if (isset($data['rule']['actions'])) {
            $data['actions'] = $data['rule']['actions'];
        }
        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }
        unset($data['rule']);
        return $data;
    }
}
