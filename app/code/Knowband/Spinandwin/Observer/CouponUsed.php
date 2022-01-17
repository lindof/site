<?php
namespace Knowband\Spinandwin\Observer;
 
use Magento\Framework\Event\ObserverInterface;

class CouponUsed implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
 
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Knowband\Spinandwin\Model\Coupons $couponModel,
        \Knowband\Spinandwin\Model\Users $userModel,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->_objectManager = $objectManager;
        $this->sp_couponModel = $couponModel;
        $this->sp_userModel = $userModel;
        $this->date = $date;
        $this->checkoutSession = $checkoutSession;
        $this->orderRepository = $orderRepository;
    }
 
    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {


	$event = $observer->getEvent();
        $orderIds = $event->getOrderIds();
        $order_id = $orderIds[0];
        $order = $this->orderRepository->get($order_id);
        $coupon_code = $order->getCouponCode();
        if ($coupon_code) {
            $collection = $this->sp_couponModel->getCollection();
            $collection->addFieldToFilter('main_table.coupon_code', array('eq' => $coupon_code));
            $coupon_data = $collection->getData();
            if (!empty($coupon_data)) {
                $coupon_detail = $collection->getFirstItem();
                $u_collection = $this->sp_userModel->getCollection();
                $u_collection->addFieldToFilter('main_table.coupon_id', array('eq' => $coupon_detail['coupon_id']));
                $user_data = $u_collection->getData();
                if (!empty($user_data)) {
                    $user_detail = $u_collection->getFirstItem();
                    $used_model = $this->sp_userModel->load($user_detail['id_user_list']);
                    $used_model->setCouponUsage('1');
                    $used_model->setUpdatedAt($this->date->date());
                }
                $used_model->save()->getId();
            }
        }
    }
}
