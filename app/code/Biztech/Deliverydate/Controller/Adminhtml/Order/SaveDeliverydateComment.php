<?php

namespace Biztech\Deliverydate\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Model\Quote;

/**
 * SaveDeliverydateComment
 */
class SaveDeliverydateComment extends Action
{

    protected $resultRawFactory;
    protected $resultPageFactory;
    protected $quote;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RawFactory $resultRawFactory,
        Quote $quote
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->quote = $quote;
        parent::__construct($context);
    }

    public function execute()
    {
        $request = $this->getRequest();

        $shippingArrivalComments = $request->getParam('shipping_arrival_comments');


        $sessionQuote = $this->_objectManager->get('Magento\Backend\Model\Session\Quote');

        $sessionQuote->getQuote()->setShippingArrivalComments($shippingArrivalComments);

        $sessionQuote->getQuote()->save();


        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('sales_order_create_load_block_totals');
        $result = $resultPage->getLayout()->renderElement('content');


        return $this->resultRawFactory->create()->setContents($result);
    }
}
