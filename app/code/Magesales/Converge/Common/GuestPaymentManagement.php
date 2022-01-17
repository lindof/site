<?php


namespace Magesales\Converge\Common;

use Magesales\Converge\Api\GuestPaymentManagementInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Api\OrderManagementInterface;

/**
 * Class GuestPaymentManagement
 * @deprecated since 1.1.0
 */
class GuestPaymentManagement implements GuestPaymentManagementInterface
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
     * @param string $cartId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function restoreQuote($cartId)
    {
        $order = $this->session->getLastRealOrder();
        if ($order->getId()) {
            $this->orderManagement->cancel($order->getId());
        }
        $this->session->restoreQuote();
        return true;
    }
}
