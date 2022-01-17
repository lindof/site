<?php

namespace MageSpark\Coupon\Plugin;

use Closure;
use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\SalesRule\Model\CouponFactory;
use MageSpark\Coupon\Helper\Data;

class PluginUtility
{
    /**
     * @var CouponFactory
     */
    protected $couponFactory;

    protected $orderRepository;

    protected $searchCriteriaBuilder;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * PluginUtility constructor.
     * @param CouponFactory $couponFactory
     * @param Data $helperData
     */
    public function __construct(
        CouponFactory $couponFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $helperData
    ) {
        $this->couponFactory = $couponFactory;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->helperData = $helperData;
    }
    /**
     * @param $subject
     * @param Closure $proceed
     * @param $rule
     * @param $address
     * @return bool
     * @throws Exception
     */
    public function aroundCanProcessRule($subject, Closure $proceed, $rule, $address)
    {
        $result = $proceed($rule, $address);

        $couponCode = $address->getQuote()->getCouponCode();

        if ($couponCode && $address->getQuote()->getCustomerEmail()) {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('customer_email', $address->getQuote()->getCustomerEmail(), 'eq')
                ->addFilter('coupon_code', $couponCode, 'eq')
                ->create();
            $orders = $this->orderRepository->getList($searchCriteria);

            $coupon = $this->couponFactory->create();
            $coupon->load($couponCode, 'code');

            if($orders && $coupon->getId() && count($orders->getItems()) >= $rule->getUsesPerCustomer())
            {
                $rule->setIsValidForAddress($address, false);
                $result = false;
            }
        }

        return $result;
    }
}
