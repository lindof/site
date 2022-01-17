<?php

namespace Biztech\Deliverydate\Model;

class Calendar
{

    private static $instance;
    private $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    private $currentYear = 0;
    private $currentMonth = 0;
    private $currentDay = 0;
    private $currentDate = null;
    private $selectDate = null;
    private $daysInMonth = 0;
    private $orderArray = [];
    private $isAdmin = false;
    private $_objectManager;
    private $_bizHelper;
    private $_coreHelper;
    private $orderCollection;
    private $_urlHelper;
    private $customerSession;

    /**
     *
     */
    public function __construct()
    {
        $this->getInitialize();
    }

    /**
     * @return $this
     */
    public function getInitialize()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_coreHelper = $this->_objectManager->create('\Magento\Backend\Helper\Data');
        $this->_bizHelper = $this->_objectManager->create('\Biztech\Deliverydate\Helper\Data');
        $this->orderCollection = $this->_objectManager->create('\Magento\Sales\Model\ResourceModel\Order\Collection');
        $this->isAdmin = $this->_bizHelper->isAdmin();
        $this->_urlHelper = $this->_objectManager->create('\Magento\Framework\Url');
        $this->customerSession = $this->_objectManager->create('\Magento\Customer\Model\Session');
        return $this;
    }

    /**
     * @return Calendar
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * print out the calendar.
     */
    public function show()
    {
        $request = $this->_bizHelper->getRequest();

        $rYear = $request->getParam('year');
        $rMonth = $request->getParam('month');
        $rDay = $request->getParam('day');

        $year = null;
        $month = null;
        $day = null;

        if (null === $year && isset($rYear)) {
            $year = $rYear;
        } elseif (null === $year) {
            $year = date('Y', time());
        }

        if (null === $month && isset($rMonth)) {
            $month = $rMonth;
        } elseif (null === $month) {
            $month = date('m', time());
        }
        if (null === $day && isset($rDay)) {
            $day = $rDay;
        } else {
            $day = 1;
        }

        $this->currentYear = $year;
        $this->currentMonth = $month;
        $this->selectDate = $day;
        $this->daysInMonth = $this->_daysInMonth($month, $year);
        $content = '<div id="calendar">' .
            '<caption>' .
            $this->_createNavi() .
            '</caption>' .
            '<table class="cal" cellpaddin="0" cellspacing="0">' .
            '<thead><tr>' . $this->_createLabels() . '</thead>';
        $content .= '<tbody><tr>';
        $weeksInMonth = $this->_weeksInMonth($month, $year);
        // Create weeks in a month
        for ($i = 0; $i < $weeksInMonth; ++$i) {
            //Create days in a week
            for ($j = 1; $j <= 7; ++$j) {
                $content .= $this->_showDay($i * 7 + $j);
            }
        }

        $content .= '</table>';

        $content .= '<div class="clear"></div>';

        $content .= '</div>';

        return $content;
    }

    /**
     * calculate number of days in a particular month.
     * @param null|mixed $month
     * @param null|mixed $year
     */
    private function _daysInMonth($month = null, $year = null)
    {
        if (null === ($year)) {
            $year = date('Y', time());
        }

        if (null === ($month)) {
            $month = date('m', time());
        }

        return date('t', strtotime($year . '-' . $month . '-01'));
    }

    /**
     * create navigation.
     */
    private function _createNavi()
    {
        $nextMonth = $this->currentMonth == 12 ? 1 : intval($this->currentMonth) + 1;
        $nextYear = $this->currentMonth == 12 ? intval($this->currentYear) + 1 : $this->currentYear;
        $preMonth = $this->currentMonth == 1 ? 12 : intval($this->currentMonth) - 1;
        $preYear = $this->currentMonth == 1 ? intval($this->currentYear) - 1 : $this->currentYear;

        if ($this->selectDate != null || $this->selectDate != "") {
            $preDay = 1;
            $nextDay = 1;
        }
        $preUrl = $this->_urlHelper->getUrl('deliverydate/index/index', ['month' => $preMonth, 'year' => $preYear, 'day' => $preDay]);
        $nextUrl = $this->_urlHelper->getUrl('deliverydate/index/index', ['month' => $nextMonth, 'year' => $nextYear, 'day' => $nextDay]);
        if ($this->_bizHelper->isAdmin()) {
            $preUrl = $this->_coreHelper->getUrl('deliverydate/deliverydate/index', ['month' => $preMonth, 'year' => $preYear, 'day' => $preDay]);
            $nextUrl = $this->_coreHelper->getUrl('deliverydate/deliverydate/index', ['month' => $nextMonth, 'year' => $nextYear, 'day' => $nextDay]);
        }

        $currentDisplayDate = $this->currentYear . '-' . $this->currentMonth . '-1';
        $navDisp = '<a class="cal-nav prev" id="cal-prev" href="#" data-url="' . $preUrl . '">&larr;</a>' .
            '<span class="title">' . date('Y M', strtotime($currentDisplayDate)) . '</span>' .
            '<a class="cal-nav next" id="cal-next" href="#" data-url="' . $nextUrl . '">&rarr;</a>';

        return $navDisp;
    }

    /**
     * create calendar week labels.
     */
    private function _createLabels()
    {
        $content = '';

        foreach ($this->dayLabels as $index => $label) {
            $content .= '<th class="' . ($label === 6 ? 'end title' : 'start title') . ' title">' . __($label) . '</th>';
        }

        return $content;
    }

    /**
     * calculate number of weeks in a particular month.
     * @param null|mixed $month
     * @param null|mixed $year
     */
    private function _weeksInMonth($month = null, $year = null)
    {
        if (null === ($year)) {
            $year = date('Y', time());
        }

        if (null === ($month)) {
            $month = date('m', time());
        }

        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month, $year);

        $numOfweeks = ($daysInMonths % 7 === 0 ? 0 : 1) + intval($daysInMonths / 7);

        $monthEndingDay = date('N', strtotime($year . '-' . $month . '-' . $daysInMonths));

        $monthStartDay = date('N', strtotime($year . '-' . $month . '-01'));

        if ($monthEndingDay < $monthStartDay) {
            ++$numOfweeks;
        }

        return $numOfweeks;
    }

    /**
     * @param $cellNumber
     * @return string
     */
    private function _showDay($cellNumber)
    {
        if (!is_null($this->selectDate) || !empty($this->selectDate)) {
            $active = date('Y-m-d', strtotime($this->selectDate));
        } else {
            $active = date('Y-m-d');
        }

        if ($this->currentDay == 0) {
            $currentDisplayDate = $this->currentYear . '-' . $this->currentMonth . '-1';
            $firstDayOfTheWeek = date('N', strtotime($currentDisplayDate));
            if (intval($cellNumber) == intval($firstDayOfTheWeek)) {
                $this->currentDay = 1;
            }
        }
        $today = date('Y-m-d');

        if (($this->currentDay != 0) && ($this->currentDay <= $this->daysInMonth)) {
            $this->currentDate = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . ($this->currentDay)));
            $cellContent = $this->currentDay;
            $this->currentDay++;
        } else {
            $this->currentDate = null;
            $cellContent = null;
        }

        if (in_array($this->currentDate, $this->getOrderArray())) {
            $orderslinks = '';
            $orderString = '';
            $ordercountPerDate = 0;

            foreach ($this->getOrderArray() as $key => $value) {
                if ($this->currentDate == $value) {
                    $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($key, 'increment_id');
                    $orderUrl = $this->_urlHelper->getUrl('sales/order/view/', ['order_id' => $order->getEntityId()]);
                    $orderslinks .= '<a href="' . $orderUrl . '">' . $key . ' </a>';

                    if ($this->isAdmin === true) {
                        $orderString .= strlen($orderString) > 0 ? '&' . $key : $key;
                        $url = trim($this->_coreHelper->getUrl('deliverydate/deliverydate/deliveryDetails', ['orders' => $orderString]));
                        $ordercountPerDate++;
                        $orderslinks = "<a class='delivery-orders' href='#' data-url='" . $url . "'>" . __('Orders') . "(" . $ordercountPerDate . ')</a>';
                    }
                }
            }
            return $this->_createTdContent($cellContent, $cellNumber, $today, $orderslinks, $active = 'active');
        }
        return $this->_createTdContent($cellContent, $cellNumber, $today, $orderslinks = '', $active = '');
    }

    /**
     * @return array
     */
    protected function getOrderArray()
    {
        $orderArray = [];
        $customerData = $this->customerSession->getCustomer();
        $customerData->getEntityId();
        $_orders = [];

        if ($this->isAdmin === true) {
            $_orders = $this->orderCollection;
        } else {
            $_orders = $this->orderCollection->addFieldToFilter('customer_id', $customerData->getEntityId());
        }

        foreach ($_orders as $ord) {
            $orderArray[$ord->getIncrementId()] = date('Y-m-d', strtotime($ord->getShippingArrivalDate()));
        }

        $this->orderArray = $orderArray;
        return $this->orderArray;
    }

    /**
     * @param $cellContent
     * @param $cellNumber
     * @param $today
     * @param $orderslinks
     * @param $active
     * @return string
     */
    private function _createTdContent($cellContent, $cellNumber, $today, $orderslinks, $active)
    {
        if ($this->currentDate === $today) {
            return '<td id="li-' . $this->currentDate . '" class="current ' . $active . ($cellNumber % 7 === 1 ? ' start ' : ($cellNumber % 7 === 0 ? ' end ' : ' ')) .
            ($cellContent === null ? 'mask' : '') . '">' . $cellContent . $orderslinks . '</td>' . ($cellNumber % 7 === 0 ? '</tr><tr> ' : ' ');
        }

        return '<td id="li-' . $this->currentDate . '" class="' . $active . ($cellNumber % 7 === 1 ? ' start ' : ($cellNumber % 7 === 0 ? ' end ' : ' ')) .
        ($cellContent === null ? 'mask' : '') . '">' . $cellContent . $orderslinks . '</td>' . ($cellNumber % 7 === 0 ? '</tr><tr> ' : ' ');
    }
}
