<?php

namespace Biztech\Deliverydate\Controller\Adminhtml\Deliverydate;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Store\Model\ScopeInterface;

class Exportdeliverydateorders extends Action
{
    const XML_DD_FORMAT = 'deliverydate/deliverydate_general/deliverydate_format';
    const XML_DT_FORMAT = 'deliverydate/deliverydate_general/deliverytime_format';
    const XML_DT_ENABLE_TIME = 'deliverydate/deliverydate_general/deliverytime_enable_time';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_layoutFactory;
    protected $_scopeConfig;
    protected $_resultRawFactory;
    protected $_fileFactory;

    /**
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        FileFactory $fileFactory
    ) {
        $this->_resultRawFactory = $resultRawFactory;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page.
     */
    public function execute()
    {
        $new_date = date('Y-m-d', time());
        $dateStart = date('Y-m-d 00:00:00', strtotime($new_date));
        $dateEnd = date('Y-m-d 23:59:59', strtotime($new_date));
        $delivery_from = $this->getRequest()->getParam('delivery_from');
        $delivery_to = $this->getRequest()->getParam('delivery_to');

        if (isset($delivery_from) && $delivery_from !== '') {
            $dateStart = $delivery_from;
        }
        if (isset($delivery_to) && $delivery_to !== '') {
            $dateEnd = $delivery_to;
        }
        $objectManager = ObjectManager::getInstance();
        $obj_order = $objectManager->create('\Biztech\Deliverydate\Helper\Data');
        $collection = $obj_order->getOrderCollection($dateStart, $dateEnd);
        $orders = $collection;
        $scopevalue = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $result = [['Order #Id', 'Purchased On', 'Bill To Name', 'Ship To Name', 'Same day charges', 'Specific timeslot charges', 'G.T. (Purchased)' ,'G.T. (Base)', 'Status', 'Delivery Date']];
        $date_format = $scopevalue->getValue(self::XML_DD_FORMAT, ScopeInterface::SCOPE_STORE);
        $time_format = $scopevalue->getValue(self::XML_DT_FORMAT, ScopeInterface::SCOPE_STORE);
        if ($date_format === '') {
            $date_format = 'd/M/Y';
        }
        if ($time_format === '') {
            $date_format .= ' ,g:i a';
        } else {
            $is_time = $scopevalue->getValue(self::XML_DT_ENABLE_TIME, ScopeInterface::SCOPE_STORE);
            if ($is_time) {
                $date_format .= ' ' . $time_format;
            } else {
                $date_format .= ' ';
            }
        }
        if (count($orders) > 0) {
            foreach ($orders as $order) {
                $shipping_arrival_date = $order->getShippingArrivalDate();
                $orderid = $order->getIncrementId();
                $purchasedon = $order->getCreatedAt();
                $billname = $order->getBillingFirstname() . ' ' . $order->getBillingLastname();
                $shipptoname = $order->getShippingFirstname() . ' ' . $order->getShippingLastname();
                $obj_currency = $objectManager->create('\Magento\Directory\Model\Currency');
                $gtbase = $obj_currency->getCurrencySymbol() . $order->getBaseGrandTotal();
                $gtpurchase = $obj_currency->getCurrencySymbol() . $order->getGrandTotal();
                $samedaycharge = $order->getSameDayCharges();
                $timeslotcharges = $order->getDeliveryCharges();
                $status = $order->getStatus();
                $deliverydate = date($date_format, strtotime($shipping_arrival_date));
                array_push($result, [$orderid, $purchasedon, $billname, $shipptoname, $samedaycharge , $timeslotcharges , $gtpurchase , $gtbase , $status, $deliverydate]);
            }
        }
        $content = '';
        $fileName = 'deliverydateorder.csv';
        foreach ($result as $line) {
            $content .= '"' . implode('","', $line) . '",' . "\n";
        }
        return $this->_fileFactory->create(
            $fileName,
            $content,
            DirectoryList::VAR_DIR
        );
    }
}
