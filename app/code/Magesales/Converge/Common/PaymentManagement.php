<?php


namespace Magesales\Converge\Common;

use Magesales\Converge\Api\PaymentManagementInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Api\OrderManagementInterface;

/**
 * Class PaymentManagement
 * @deprecated since 1.1.0
 */
class PaymentManagement implements PaymentManagementInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;

    /**
     * GuestPaymentManagement constructor.
     * @param Session $session
     * @param OrderManagementInterface $orderManagement
     */
    public function __construct(
        Session $session,
        OrderManagementInterface $orderManagement
    ) {
        $this->session = $session;
        $this->orderManagement = $orderManagement;
    }

    /**
     * @return bool
     */
    public function restoreQuote()
    {
        $order = $this->session->getLastRealOrder();
        if ($order->getId()) {
            $this->orderManagement->cancel($order->getId());
        }
        $this->session->restoreQuote();
        return true;
    }
}
