<?php


namespace Magesales\Converge\Observer\Multishipping;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class Refund
 */
class Refund implements ObserverInterface
{
    /**
     * @var OrderRefundOperation
     */
    private $orderRefund;

    public function __construct(OrderRefundOperation $orderRefundOperation)
    {
        $this->orderRefund = $orderRefundOperation;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var array $orders */
        $orders = $observer->getData('orders');

        /** @var OrderInterface $order */
        foreach ($orders as $order) {
            $this->orderRefund->refund($order);
        }
    }
}
