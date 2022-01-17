<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Affiliate
 */


namespace Amasty\AffiliateStoreCredit\Controller\Account;


use Amasty\AffiliateStoreCredit\Model\CustomerProcessor;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * Class Converter
 * @package Amasty\AffiliateStoreCredit\Controller\Account
 */
class Converter extends Action
{
    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var CustomerProcessor
     */
    private $customerProcessor;

    /**
     * Converter constructor.
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param CustomerProcessor $customerProcessor
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        CustomerProcessor $customerProcessor
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->customerProcessor = $customerProcessor;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('affiliate/account/transaction/');
        }

        $customerId = $this->getRequest()->getParam('customer_id');
        if ($customerId) {
            try {
                $this->customerProcessor->convertByCustomerId($customerId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('Something wrong during conversion.'));
                return $this->resultRedirectFactory->create()->setPath('affiliate/account/transaction/');
            }
        } else {
            $this->messageManager->addErrorMessage(__('No Customer Id Parameter.'));
            return $this->resultRedirectFactory->create()->setPath('affiliate/account/transaction/');
        }
        return $this->resultRedirectFactory->create()->setPath('affiliate/account/transaction/');
    }
}
