<?php

namespace Biztech\Deliverydate\Block;

use Biztech\Deliverydate\Helper\Data;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Session;

class Deliverydate extends Template
{
    protected $_scopeConfig;
    protected $_helper;
    protected $_date;
    protected $priceHelper;
    protected $timeZone;
    protected $_order;
    protected $_cart;
    protected $customerSession;

    public function __construct(
        Context $context,
        Data $helper,
        DateTime $date,
        Timezone $timeZone,
        PricingHelper $priceHelper,
        OrderCollectionFactory $order,
        Session $customerSession,
        Cart $cart,
        array $data = []
    ) {
        $this->_order = $order;
        parent::__construct($context, $data);
        date_default_timezone_set($timeZone->getConfigTimezone());
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_helper = $helper;
        $this->_date = $date;
        $this->timeZone = $timeZone;
        $this->priceHelper = $priceHelper;
        $this->customerSession = $customerSession;
        $this->_cart = $cart;
    }

    public function getSalesOrderCollection(array $filters = [])
    {
        $collectOrder = $this->_order->create()->addAttributeToSelect('*');
        foreach ($filters as $field => $condition) {
            $collectOrder->addFieldToFilter($field, $condition);
        }

        return $collectOrder;
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /*public function getDeliverydate()
    {
        if (!$this->hasData('deliverydate')) {
            $this->setData('deliverydate', Mage::registry('deliverydate'));
        }

        return $this->getData('deliverydate');
    }*/

    public function getenabledMethod()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/delivery_method', ScopeInterface::SCOPE_STORE);
    }

    public function getConfigValue($path, $scope = null)
    {
        if (is_null($scope)) {
            $scope = ScopeInterface::SCOPE_STORE;
        }
        return $this->_scopeConfig->getValue($path, $scope);
    }

    public function enableExtension()
    {
        $display = false;
        $isCustomerApplicable = $this->_helper->checkCurrentUserAllowed($this->customerSession->getCustomer()->getGroupId());
        if ($this->_helper->isEnable() && $this->_helper->getOnWhichPage() == 1 && $isCustomerApplicable) {
            $display = true;
        }
        return $display;
    }

    public function getisMandatory()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/is_mandatory', ScopeInterface::SCOPE_STORE);
    }

    public function getDeliveryDateCommentsLabel()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverydate_comments_label', ScopeInterface::SCOPE_STORE);
    }

    public function getShowHtml()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/show_html', ScopeInterface::SCOPE_STORE);
    }

    public function getDeliveryHtml()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverydate_html', ScopeInterface::SCOPE_STORE);
    }

    public function getDeliverydatelabel()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverydate_label', ScopeInterface::SCOPE_STORE);
    }

    public function getCalendarTime()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverytime_enable_time', ScopeInterface::SCOPE_STORE);
    }

    public function getTableLabel()
    {
        return $this->getConfigValue('deliverydate/deliverydate_timeslots/table_label', ScopeInterface::SCOPE_STORE);
    }

    public function getCurrentTime($timeFormat = 'H')
    {
        return $this->_date->date($timeFormat);
    }

    public function getavailableTimeslot()
    {
        $displaySlots = [];

        $timeslot = $this->getHelper()->getTimeSlots();
        $delivery_days = $this->getNoofDaysofDelivery();
        $deliverytime_format = $this->getDeliveryTimeFormat();
        $deliverydate_format = $this->getDeliveryDateFormat();
        $startDate = $this->_date->date('Y-m-d');
        $dayIntval = $this->getDayDiff();

        if (!is_null($dayIntval)) {
            $startDate = date('Y-m-d', strtotime($startDate . '+' . $dayIntval . 'days'));
        }

        $endDate = date('Y-m-d', strtotime($startDate . '+' . ($delivery_days) . 'days'));

        $begin = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($begin, $interval, $end);

        $displaySlots = $this->getDateRange($dateRange, $timeslot, $deliverytime_format, $deliverydate_format);

        return $displaySlots;
    }

    public function getHelper()
    {
        return $this->_helper;
    }

    public function getNoofDaysofDelivery()
    {
        return $this->getConfigValue('deliverydate/deliverydate_timeslots/no_of_deliverydays', ScopeInterface::SCOPE_STORE);
    }

    public function getDeliveryTimeFormat()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverytime_format', ScopeInterface::SCOPE_STORE);
    }

    public function getDeliveryDateFormat()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverydate_format', ScopeInterface::SCOPE_STORE);
    }

    public function getDayDiff()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverytime_day_diff', ScopeInterface::SCOPE_STORE);
    }

    protected function getDateRange($dateRange, $timeslot, $deliverytime_format = 'g:i a', $deliverydate_format = 'Y-m-d')
    {
        $displaySlots = [];
        $time_diff = ceil($this->getTimeDiff());

        $disable_slot = $this->getHelper()->getFormattedDisableSlots();
        $disable_slot_date = $this->getHelper()->getFormattedDisableSlotsDate();

        if ($this->getDayOffs() === '0') {
            $day_off = explode(',', $this->getDayOffs());
        } else {
            $day_off = $this->getDayOffs() ? explode(',', $this->getDayOffs()) : [];
        }
        
        $nonworking_days = $this->getHelper()->getFormattedNonWorkingDays();
        $nonworking_period = $this->getHelper()->getFormattedNonWorkingPeriod();
        $nonworking_dates = array_merge((array)$nonworking_days, (array)$nonworking_period);
        $dailyQuota = $this->getHelper()->disableSameDayBasedOnDailyQuota();
        
        if ($dailyQuota['disable'] === true) {
            $nonworking_dates = array_values(array_unique(array_merge($nonworking_dates, $dailyQuota['day_to_disable']), SORT_REGULAR));
        }
        
        $cutoff = $this->getHelper()->disableDayBasedOnCutoff();
        if ($cutoff['status'] === true) {
            $nonworking_dates = array_values(array_unique(array_merge($nonworking_dates, $cutoff['day_to_disable']), SORT_REGULAR));
        }

        $currentTime = $this->timeZone->date()->format('Y-m-d h:i A');

        $delivery_start_time = $this->_date->date('Y-m-d h:i A', strtotime($currentTime) + 60 * 60 * $time_diff);

        if (count($timeslot) > 0) {
            foreach ($dateRange as $date) {
                $deliveryDateDisplay = $date->format($deliverydate_format);
                $deliveryDate = $date->format('d-m-Y');
                $day = $date->format('l');
                if ($day == 'Sunday') {
                    $day_no = 0;
                } else {
                    $day_no = $date->format('N');
                }

                $k = $date->format('l') . '_' . $date->format('Y-m-d');
                $displaySlots[$k]['row_heading'] = $deliveryDateDisplay . ' ' . __($day);
                $displaySlots[$k]['delivery_date'] = $date->format('Y-m-d');
                $l = 0;
                for ($j = 0; $j < count($timeslot); $j++) {
                    $cond1 = false;
                    $cond2 = false;
                    $cond3 = false;
                    $cond5 = false;
                    $timeslotPrice = $timeslot['timeslot_' . $j]['price'];

                    $startTime = date($deliverytime_format, strtotime($timeslot['timeslot_' . $j]['start_time']));
                    $endTime = date($deliverytime_format, strtotime($timeslot['timeslot_' . $j]['end_time']));

                    if ($timeslotPrice > 0) {
                        $price = $this->priceHelper->currency($timeslotPrice, true, false);
                        $timeslotValueHtml = '<span class="am">' . $startTime . '</span> <span class="seperator">-</span><span class="pm">' . $endTime . '</span> <span class="seperator"></span> <span class="price">' . $price . '</span>';
                    } else {
                        $timeslotValueHtml = '<span class="am">' . $startTime . '</span> <span class="seperator">-</span><span class="pm">' . $endTime . '</span> <span class="seperator"></span>';
                    }
                    $timeslotValue = $timeslot['timeslot_' . $j]['start_time'] . ' - ' . $timeslot['timeslot_' . $j]['end_time'];
                    $timeslotId = $deliveryDate . '_' . $timeslot['timeslot_' . $j]['start_time'] . '_' . $timeslotPrice;

                    $displaySlots[$k]['slots'][$j - $l]['slot_value_html'] = ($timeslotValueHtml);
                    $displaySlots[$k]['slots'][$j - $l]['slot_value'] = $timeslotValue;
                    $displaySlots[$k]['slots'][$j - $l]['slot_id'] = $timeslotId;

                    foreach ($disable_slot as $dslot) {
                        if (($dslot['day'] == $day_no) && in_array($timeslotValue, $dslot['time_slot'])) {
                            $cond1 = true;
                        }
                    }

                    foreach ($disable_slot_date as $dslot_date) {
                        if ((date('d-m-Y', strtotime($dslot_date['date'])) == date('d-m-Y', strtotime($deliveryDate))) && in_array($timeslotValue, $dslot_date['time_slot'])) {
                            $cond3 = true;
                        }
                    }
                    $_sTime = $date->format('Y-m-d') . ' ' . date('h:i A', strtotime($timeslot['timeslot_' . $j]['start_time']));
                    
                    if ((in_array($day_no, $day_off)) ||
                        (strtotime($_sTime) < strtotime($delivery_start_time)) || (in_array($deliveryDate, $nonworking_dates))
                    ) {
                        $cond2 = true;
                    }

                    if ($deliveryDate == $this->_date->date('d-m-Y', strtotime($delivery_start_time))) {
                        if ((strtotime($_sTime) < strtotime($delivery_start_time)) && $deliveryDate == $this->_date->date('d-m-Y', strtotime($delivery_start_time))) {
                            $cond5 = true;
                        }
                    }
                    
                    if ($cond1 == true || $cond2 == true || $cond3 == true || $cond5 == true) {
                        $displaySlots[$k]['slots'][$j - $l]['disabled'] = true;
                    } else {
                        $displaySlots[$k]['slots'][$j - $l]['disabled'] = false;
                    }
                }
            }
        }

        return $displaySlots;
    }

    public function getTimeDiff()
    {
        return $this->getConfigValue('deliverydate/deliverydate_timeslots/deliverytime_diff', ScopeInterface::SCOPE_STORE);
    }

    public function getDayOffs()
    {
        $dayOffs = $this->getConfigValue('deliverydate/deliverydate_dayoff/deliverydate_dayoff');
        if (isset($dayOffs[0]) && $dayOffs[0] == '') {
            unset($dayOffs[0]);
            $dayOffs = array_values($dayOffs);
        }
        return $dayOffs;
    }
}
