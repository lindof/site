<?php

namespace Biztech\Deliverydate\Model\Checkout;

use Biztech\Deliverydate\Helper\Data;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Quote\Model\QuoteRepository;

class ShippingInformationManagementPlugin
{
    protected $quoteRepository;
    protected $_bizHelper;

    /**
     * @param QuoteRepository $quoteRepository
     * @param Data $bizHelper
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        Data $bizHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_bizHelper = $bizHelper;
    }

    /**
     * @param ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $deliveryComments = $extAttributes->getShippingArrivalComments();
        $deliveryDate = $this->_bizHelper->getFormatedDeliveryDateToSave($extAttributes->getShippingArrivalDate());
        $deliverySlot = $extAttributes->getShippingArrivalSlot();
        $deliveryCharges = $extAttributes->getDeliveryCharges();
        $quote = $this->quoteRepository->getActive($cartId);

        $sameDayCharges = $this->_bizHelper->addSameDayCharges($deliveryDate);
        if ($sameDayCharges['addCharge'] === true) {
            $quote->setSameDayCharges($sameDayCharges['charges']);
        } else {
            $quote->setSameDayCharges(0);
        }

        $quote->setShippingArrivalComments($deliveryComments);
        $quote->setShippingArrivalDate($deliveryDate);
        $quote->setShippingArrivalSlot($deliverySlot);
        $callBeforeDelivery = (int) $extAttributes->getCallBeforeDelivery();
        $quote->setCallBeforeDelivery($callBeforeDelivery);
        if ($deliveryCharges == "" | $deliveryCharges == null) {
            $deliveryCharges = (float) 0;
        } else {
            $deliveryCharges = (float) $deliveryCharges;
        }
        $quote->setDeliveryCharges($deliveryCharges);
    }
}
