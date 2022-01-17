<?php

namespace Biztech\Deliverydate\Controller\Adminhtml\Order;

use Biztech\Deliverydate\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Model\Quote;

/**
 * SaveDeliverydateInfo
 */
class SaveDeliverydateInfo extends Action
{
    protected $resultRawFactory;
    protected $resultPageFactory;
    protected $quote;
    protected $_bizHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RawFactory $resultRawFactory,
        Quote $quote,
        Data $bizHelper
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->quote = $quote;
        $this->_bizHelper = $bizHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $request = $this->getRequest();

        $deliveryCharges = $request->getParam('delivery_charges');
        $shippingArrivalDate = $this->_bizHelper->getFormatedDeliveryDateToSave($request->getParam('shipping_arrival_date'));

        $shippingArrivalSlot = $request->getParam('shipping_arrival_slot');


        $sessionQuote = $this->_objectManager->get('Magento\Backend\Model\Session\Quote');
        $sessionQuote->getQuote()->collectTotals()->save();


        $sameDayCharges = $this->_bizHelper->addSameDayCharges($shippingArrivalDate);
        if ($sameDayCharges['addCharge'] === true) {
            $sessionQuote->getQuote()->setSameDayCharges($sameDayCharges['charges']);
        } else {
            $sessionQuote->getQuote()->setSameDayCharges(0);
        }

        $sessionQuote->getQuote()->setDeliveryCharges($deliveryCharges);
        $sessionQuote->getQuote()->setShippingArrivalDate($shippingArrivalDate);
        $sessionQuote->getQuote()->setShippingArrivalSlot($shippingArrivalSlot);

        $sessionQuote->getQuote()->save();
        $sessionQuote->getQuote()->collectTotals()->save();

        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('sales_order_create_load_block_totals');
        $result = $resultPage->getLayout()->renderElement('content');


        return $this->resultRawFactory->create()->setContents($result);
    }
}
