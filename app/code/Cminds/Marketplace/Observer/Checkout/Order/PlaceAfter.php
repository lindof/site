<?php

namespace Cminds\Marketplace\Observer\Checkout\Order;

use Magento\Sales\Model\OrderFactory;
use Cminds\Marketplace\Model\MethodsFactory as MethodsFactory;
use Cminds\Marketplace\Model\ResourceModel\Methods as MethodsResource;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Cminds Marketplace order place after observer.
 * Will be executed on "sales_order_place_after" event.
 *
 * @category Cminds
 * @package  Cminds_Marketplace
 * @author   Piotr Pierzak <piotrek.pierzak@gmail.com>
 */
class PlaceAfter implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderModel;

    /**
     * @var MethodsFactory
     */
    protected $methods;

    /**
     * @var MethodsResource
     */
    protected $methodsResource;

    /**
     * @var ScopeConfig
     */
    protected $_scopeConfig;

    /**
     * Checkout session object.
     *
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * Object initialization.
     *
     * @param CheckoutSession $checkoutSession Checkout session object.
     */
    public function __construct(
        OrderFactory $orderModel,
        MethodsFactory $methodsFactory,
        MethodsResource $methodsResource,
        ScopeConfig $scopeConfig,
        CheckoutSession $checkoutSession
    ) {
        $this->orderModel = $orderModel;
        $this->methods = $methodsFactory;
        $this->methodsResource = $methodsResource;
        $this->_scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Order place after event handler.
     *
     * @param Observer $observer Observer object.
     *
     * @return PlaceAfter
     */
    public function execute(Observer $observer)
    {
        $this->checkoutSession
            ->unsMarketplaceShippingMethods()
            ->unsMarketplaceShippingPrice();

        $orderIds = $observer->getEvent()->getOrderIds();
        if (count($orderIds) > 0) {
            $this->checkoutSession->setForceOrderMailSentOnSuccess(true);
            $order = $this->orderModel->create()->load($orderIds[0]);
            $incrementOrder = $order->getIncrementId();
            $orderWebsite = $order->getStore()->getWebsite()->getName();
            $customerEmail = $order->getCustomerEmail();
            $customerFirstName = $order->getCustomerFirstname();
            $customerLastName = $order->getCustomerLastname();
            if(!trim($customerFirstName) and !trim($customerLastName)) {
                $billingAddress = $order->getBillingAddress();
                $customerFirstName = $billingAddress->getFirstname();
                $customerLastName = $billingAddress->getLastname();
            }
            $customerName = $customerFirstName . ' ' . $customerLastName;
            $pickupAddresses = [];
            foreach ($order->getAllItems() as $item) {
                $method = $this->methods->create();
                $this->methodsResource->load($method, $item->getShippingMethodId());
                if ($method->getStorePickup() == 1) {
                    $itemName = $item->getName();
                    $itemSku = $item->getSku();
                    $addressPickup = $method->getStoreAddress();

                    $pickupAddresses[$method->getSupplierId()] = [
                        'item_name' => $itemName,
                        'item_sku' => $itemSku,
                        'address_pickup' => $addressPickup
                    ];
                }
            }

            if (count($pickupAddresses) > 0) {
                $this->sendPickupEmail($incrementOrder, $orderWebsite, $customerEmail, $customerName, $pickupAddresses);
            }
        }

        return $this;
    }

    public function sendPickupEmail($orderNumber, $orderWebsite, $customerEmail, $customerName, $pickupAddresses)
    {
        $pickupAddress = '';
        foreach($pickupAddresses as $address) {
            $pickupAddress .= $address['address_pickup'];
            $pickupAddress .= '<br/>';
        }

        // Send Mail functionality
        $from = $this->getSalesEmail();
        $nameFrom = $this->getSalesName();
        $to = $customerEmail;
        $nameTo = $customerName;
        $body = '
<div>
    <p>Hello, ' . $customerName .'</p>
    <p>YOUR ORDER # ' . $orderNumber . '</p>
    <p>Your Store Pick Up addresses:</p>' . $pickupAddress . '
    <br/><br/>
    <p>If you have questions about your order, you can email us at <a href="mailto:' . $from . '">' . $from . '</a></p>
    <br/>
    <p>Your ' . $orderWebsite . ' store.</p>
</div>';

        $email = new \Zend_Mail();
        $email->setSubject("Store Pick Up Addresses for order #" . $orderNumber);
        $email->setBodyHtml($body);     // use it to send html data
        //$email->setBodyText($body);   // use it to send simple text data
        $email->setFrom($from, $nameFrom);
        $email->addTo($to, $nameTo);
        $email->send();
    }

    public function getSalesEmail()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getSalesName()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
