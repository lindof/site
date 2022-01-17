<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Biztech\Deliverydate\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\ObjectManagerInterface;

class AddHtmlToOrderShippingViewObserver implements ObserverInterface
{

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param ObjectManagerInterface $objectmanager
     */
    public function __construct(ObjectManagerInterface $objectmanager)
    {
        $this->_objectManager = $objectmanager;
    }

    public function execute(EventObserver $observer)
    {
        $bizHelper = $this->_objectManager->create('\Biztech\Deliverydate\Helper\Data');
        $formattedDate = '';
        $request = $this->_objectManager->get('\Magento\Framework\App\Request\Http');
        $area = $this->_objectManager->get('Magento\Framework\App\State');
        $areaCode = $area->getAreaCode();
        $requestname = $areaCode ."_". $request->getFullActionName();
        $isEnable = $bizHelper->getConfigValue('deliverydate/deliverydate_general/enabled');
        $enableview = $bizHelper->getConfigValue('deliverydate/deliverydate_general/display_on');
        if (strlen($enableview) > 0) {
            $enableview = explode(",", $enableview);
        } else {
            $enableview = [];
        }

        if ($isEnable && (in_array($requestname, $enableview) || in_array("all", $enableview))) {
            if ($observer->getElementName() === 'order_shipping_view' /* || $observer->getElementName() === 'shipment_tracking' */ || $observer->getElementName() === 'order_info') {
                $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());

                $order = $orderShippingViewBlock->getOrder();

                if (is_null($order) && $observer->getElementName() === 'shipment_tracking') {
                    $order = $orderShippingViewBlock->getShipment()->getOrder();
                }

                if (is_null($order->getShippingArrivalDate()) == false && $order->getShippingArrivalDate() !== '') {
                    if ($order->getShippingArrivalDate() == null || $order->getShippingArrivalDate() == '') {
                        $formattedDate = 'N/A';
                    } else {
                        $dateFormat = $bizHelper->getConfigValue('deliverydate/deliverydate_general/deliverydate_format');
                        $timeFormat = $bizHelper->getConfigValue('deliverydate/deliverydate_general/deliverytime_format');

                        if ($bizHelper->getConfigValue('deliverydate/deliverydate_general/delivery_method') == 1 && $bizHelper->getConfigValue('deliverydate/deliverydate_general/deliverytime_enable_time') == 1) {
                            $format = $dateFormat . ' ' . $timeFormat;
                            $formattedDate = date($format, strtotime($order->getShippingArrivalDate()));
                        } elseif ($bizHelper->getConfigValue('deliverydate/deliverydate_general/delivery_method') == 2) {
                            $format = $dateFormat;
                            $formattedDate = date($format, strtotime($order->getShippingArrivalDate()));
                        } else {
                            $format = $dateFormat . ' ' . $timeFormat;
                            $formattedDate = date($format, strtotime($order->getShippingArrivalDate()));
                        }
                    }
                    $comments = $order->getShippingArrivalComments();

                    $deliveryDateBlock = $this->_objectManager->create('Magento\Framework\View\Element\Template');
                    $aSlot = '';
                    if (!is_null($order->getShippingArrivalSlot())) {
                        $arrivalSlot = explode('-', $order->getShippingArrivalSlot());
                        $aSlot = date($timeFormat, strtotime($arrivalSlot[0])) . '-' . date($timeFormat, strtotime($arrivalSlot[1]));
                    }
                    $deliveryDateBlock->setShippingArrivalDate($formattedDate);
                    $deliveryDateBlock->setShippingArrivalSlot($aSlot);
                    $deliveryDateBlock->setShippingArrivalComments($comments);
                    $deliveryDateBlock->setCallMeLabel($bizHelper->getDeliveryDateCallMeLabel());
                    $deliveryDateBlock->setCallBeforeDelivery($order->getCallBeforeDelivery());
                    
                    $deliveryDateBlock->setViewMode($observer->getElementName());
                    $deliveryDateBlock->setTemplate('Biztech_Deliverydate::order_info_shipping_info.phtml');

                    $html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();

                    $observer->getTransport()->setOutput($html);
                }
            }
        }
    }
}
