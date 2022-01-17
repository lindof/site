<?php

namespace Biztech\Deliverydate\Plugin\Sales;

use Biztech\Deliverydate\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class OrderPlugin
 * @package Biztech\Deliverydate\Model\Sales
 */
class OrderPlugin
{
    public $helper;
    public $request;
    protected $http;
    protected $state;
    protected $scopeConfigInterface;

    /**
     * @param Data $helper
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $helper,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Request\Http $http,
        \Magento\Framework\App\Config\ScopeConfigInterface $ScopeConfigInterface
    ) {
        $this->scopeConfigInterface = $ScopeConfigInterface;
        $this->http = $http;
        $this->state = $state;
        $this->helper = $helper;
        $this->request = $request;
    }

    /**
     * we append delivery date to order shipping description string for pdf, emails and frontend order view
     * $order->setAppendDeliveryDate(true) is done in
     * Biztech\Deliverydate\Model\Sales\Order\Email\SenderBuilderPlugin and Biztech\Deliverydate\Model\Sales\Order\Pdf\InvoicePlugin
     *
     * @param $order
     * @param $result
     * @return string
     */
    public function afterGetShippingDescription($order, $result)
    {
        $isFrontOrderView = $this->helper->isAdmin() && $this->helper->isRequestAdmin() && $this->request->getActionName() == 'view' && $this->request->getModuleName() == 'sales';

    
        $areaCode = $this->state->getAreaCode();
        $requestname = $areaCode ."_". $this->http->getFullActionName();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isEnable = $this->scopeConfigInterface->getValue('deliverydate/deliverydate_general/enabled', $storeScope);
        $enableview = $this->scopeConfigInterface->getValue('deliverydate/deliverydate_general/display_on', $storeScope);
        if (strlen($enableview) > 0) {
            $enableview = explode(",", $enableview);
        } else {
            $enableview = [];
        }

        if (($order->getAppendDeliveryDate() && $order->getShippingArrivalDate() && (in_array($requestname, $enableview) || in_array("all", $enableview))) || $isFrontOrderView) {
            return $result .
            " \n " . __('Delivery date - ') .
            $this->helper->formatMySqlDateTime($order->getShippingArrivalDate()) . "  \n  " . $order->getShippingArrivalSlot();
        }
        return $result;
    }
}
