<?php

namespace Biztech\Deliverydate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;

class CheckoutCartProductAddAfterObserver implements ObserverInterface
{

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Biztech\Deliverydate\Helper\Data
     */
    protected $helper;


    protected $_request;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\RequestInterface $request,
        \Biztech\Deliverydate\Helper\Data $data,
        Json $serializer = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_layout = $layout;
        $this->_request = $request;
        $this->helper = $data;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if ($this->helper->isEnable() && $this->helper->getOnWhichPage() == 2) {
            /* @var \Magento\Quote\Model\Quote\Item $item */
            $item = $observer->getQuoteItem();
            $additionalOptions = [];
            if ($additionalOption = $item->getOptionByCode('additional_options')) {
                $additionalOptions = (array) $this->serializer->unserialize($additionalOption->getValue());
            }


            $deliveryDateView = $this->helper->getDeliveryDateView();
            if ($deliveryDateView == 1) {
                if ($this->_request->getParam('shipping_arrival_date') !== '' && $this->_request->getParam('shipping_arrival_date') !== null) {
                    $deliverydateLabel = $this->helper->getDeliverydatelabel() ? $this->helper->getDeliverydatelabel() : 'Deliverydate Date';
                    $additionalOptions[] = [
                        'label' => __($deliverydateLabel),
                        'value' => $this->_request->getParam('shipping_arrival_date'),
                        'used_for' => 'deliverydate',
                    ];
                }
                if ($this->_request->getParam('shipping_arrival_comments') !== '' && $this->_request->getParam('shipping_arrival_comments') !== null) {
                    $deliverydateCommentsLabel = $this->helper->getDeliveryDateCommentsLabel() ? $this->helper->getDeliveryDateCommentsLabel() : 'Deliverydate Comments';
                    $additionalOptions[] = [
                        'label' => __($deliverydateCommentsLabel),
                        'value' => $this->_request->getParam('shipping_arrival_comments'),
                        'used_for' => 'deliverydate',
                    ];
                }

                if ($this->_request->getParam('deliverydate_callme') !== '' && $this->_request->getParam('deliverydate_callme') !== null && $this->_request->getParam('deliverydate_callme') == 1) {
                    $deliverydateCallMeLabel = $this->helper->getDeliveryDateCallMeLabel() ? $this->helper->getDeliveryDateCallMeLabel() : 'Call me before Delivery';
                    $additionalOptions[] = [
                        'label' => __($deliverydateCallMeLabel),
                        'value' => __('Yes'),
                        'used_for' => 'deliverydate',
                    ];
                }
            } elseif ($deliveryDateView == 2) {
                if ($this->_request->getParam('delivery_charges') !== '' && $this->_request->getParam('delivery_charges') !== null && $this->_request->getParam('delivery_charges') != 0 && $this->_request->getParam('delivery_charges') != 0.00) {
                    $item = $observer->getEvent()->getData('quote_item');
                    $product = $observer->getEvent()->getData('product');

                    $item = ($item->getParentItem() ? $item->getParentItem() : $item);
                    $orignalprice = $product->getFinalPrice();
                    $deliveryCharge = $this->_request->getParam('delivery_charges');
                    $price = $orignalprice + $deliveryCharge;
                    $item->setCustomPrice($price);
                    $item->setOriginalCustomPrice($price);
                    $item->getProduct()->setIsSuperMode(true);

                    $additionalOptions[] = [
                        'label' => __("Specific Timeslot Charges"),
                        'value' => $deliveryCharge,
                        'used_for' => 'deliverydate',
                    ];
                }

                if ($this->_request->getParam('shipping_arrival_date') !== '' && $this->_request->getParam('shipping_arrival_date') !== null && $this->_request->getParam('shipping_arrival_slot') !== '' && $this->_request->getParam('shipping_arrival_slot') !== null) {
                    $deliverydateLabel = $this->helper->getDeliverydatelabel() ? $this->helper->getDeliverydatelabel() : 'Deliverydate Date';

                    $formattedate = $this->helper->getRowHeadingformat($this->helper->getDeliveryDateFormat(), $this->_request->getParam('shipping_arrival_date'));

                    $additionalOptions[] = [
                        'label' => __($deliverydateLabel),
                        'value' => $formattedate . " " . $this->_request->getParam('shipping_arrival_slot'),
                        'used_for' => 'deliverydate',
                    ];
                }

                if ($this->_request->getParam('shipping_arrival_comments') !== '' && $this->_request->getParam('shipping_arrival_comments') !== null) {
                    $deliverydateCommentsLabel = $this->helper->getDeliveryDateCommentsLabel() ? $this->helper->getDeliveryDateCommentsLabel() : 'Deliverydate Comments';
                    $additionalOptions[] = [
                        'label' => __($deliverydateCommentsLabel),
                        'value' => $this->_request->getParam('shipping_arrival_comments'),
                        'used_for' => 'deliverydate',
                    ];
                }

                if ($this->_request->getParam('deliverydate_callme') !== '' && $this->_request->getParam('deliverydate_callme') !== null && $this->_request->getParam('deliverydate_callme') == 1) {
                    $deliverydateCallMeLabel = $this->helper->getDeliveryDateCallMeLabel() ? $this->helper->getDeliveryDateCallMeLabel() : 'Call me before Delivery';
                    $additionalOptions[] = [
                        'label' => __($deliverydateCallMeLabel),
                        'value' => __('Yes'),
                        'used_for' => 'deliverydate',
                    ];
                }
            }

            if (count($additionalOptions) > 0) {
                $item->addOption([
                    'product_id' => $item->getProductId(),
                    'code' => 'additional_options',
                    'value' => $this->serializer->serialize($additionalOptions)
                ]);
            }
        }
    }
}
