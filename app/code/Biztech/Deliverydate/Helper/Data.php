<?php

namespace Biztech\Deliverydate\Helper;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Zend\Json\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class Data extends AbstractHelper
{
    const XML_PATH_DATA = 'deliverydate/activation/data';
    const XML_PATH_ENABLED = 'deliverydate/deliverydate_general/enabled';
    const XML_PATH_INSTALLED = 'deliverydate/activation/installed';
    const XML_PATH_WEBSITES = 'deliverydate/activation/websites';
    const XML_PATH_EN = 'deliverydate/activation/en';
    const XML_PATH_KEY = 'deliverydate/activation/key';
    const XML_DELIVERY_TIMESLOTS = 'deliverydate/deliverydate_timeslots/delivery_timeslot';
    const XML_ON_WHICH_PAGE = 'deliverydate/deliverydate_general/on_which_page';
    const XML_DAYOFF = 'deliverydate/deliverydate_dayoff/deliverydate_off';
    const XML_PERIOD_OFF = 'deliverydate/deliverydate_dayoff/deliverydate_periodoff';
    const XML_DISABLE_TIMESLOT = 'deliverydate/deliverydate_timeslots/disable_timeslot';
    const XML_DISABLE_TIMESLOT_DATE = 'deliverydate/deliverydate_timeslots/disable_timeslot_date';
    const XML_DATE_FORMAT = 'deliverydate/deliverydate_general/deliverydate_format';
    const EMAIL_IDENTITY = 'customer/create_account/email_identity';
    const EMAIL_NOTIFICATION_YESNO = 'deliverydate/deliverydate_general/email_notification_yesno';
    const EMAIL_NOTIFICATION_OPTIONS = 'deliverydate/deliverydate_general/email_notification_options';
    const EMAIL_NOTIFICATION_EMAIL = 'deliverydate/deliverydate_general/email_notification_email';

    protected $nonworking_days = null;
    protected $nonworking_period = null;
    protected $timeslot = null;
    protected $disable_timeslot = null;
    protected $disable_timeslot_date = null;
    protected $_logger;
    protected $_moduleList;
    protected $_zend;
    protected $_resourceConfig;
    protected $_encryptor;
    protected $_web;
    protected $_objectManager;
    protected $_coreConfig;
    protected $_timezone;
    protected $scopeConfig;
    protected $_collectionFactory;
    protected $_transportBuilder;
    protected $inlineTranslation;
    protected $_date;
    protected $_assetRepo;
    protected $priceHelper;
    protected $serializer;

    /**
     *
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param ModuleListInterface $moduleList
     * @param Json $zend
     * @param StoreManagerInterface $storeManager
     * @param Config $resourceConfig
     * @param ObjectManagerInterface $objectmanager
     * @param ReinitableConfigInterface $coreConfig
     * @param Website $web
     * @param CollectionFactory $collectionFactory
     * @param TimezoneInterface $timezone
     * @param PricingHelper $priceHelper
     * @param TransportBuilder $transportBuilder
     * @param DateTime $date
     * @param Repository $assetRepo
     * @param SerializerInterface $serializer
     * @param StateInterface $inlineTranslation
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        ModuleListInterface $moduleList,
        Json $zend,
        StoreManagerInterface $storeManager,
        Config $resourceConfig,
        ObjectManagerInterface $objectmanager,
        ReinitableConfigInterface $coreConfig,
        Website $web,
        CollectionFactory $collectionFactory,
        TimezoneInterface $timezone,
        PricingHelper $priceHelper,
        TransportBuilder $transportBuilder,
        DateTime $date,
        Repository $assetRepo,
        SerializerInterface $serializer,
        StateInterface $inlineTranslation
    ) {
        $this->_zend = $zend;
        $this->_logger = $context->getLogger();
        $this->_moduleList = $moduleList;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_resourceConfig = $resourceConfig;
        $this->_encryptor = $encryptor;
        $this->_web = $web;
        $this->_objectManager = $objectmanager;
        $this->_coreConfig = $coreConfig;
        $this->_timezone = $timezone;
        $this->_transportBuilder = $transportBuilder;
        $this->_collectionFactory = $collectionFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->_assetRepo = $assetRepo;
        $this->_date = $date;
        $this->priceHelper = $priceHelper;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function isRequestAdmin()
    {
        return strpos($this->_request->getPathInfo(), 'admin') === false;
    }

    public function formatMySqlDateTime($dateString, $format = false, $store = false)
    {
        if ($dateString == '0000-00-00 00:00:00') {
            $result = 'N/A';
            return $result;
        }
        $format = $format ? $format : $this->getConfigDateFormat();
        $store = $store ? $store : $this->_storeManager->getStore();
        return $this->_timezone
            ->scopeDate($store, $dateString, true)
            ->format($format);
    }

    public function getConfigDateFormat()
    {
        return $this->scopeConfig->getValue(self::XML_DATE_FORMAT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getConfigValue($path, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($path, $scope);
    }

    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }

    public function getFormatedDeliveryDate($date = null)
    {
        if (empty($date) || $date === null || $date === '0000-00-00 00:00:00') {
            return __('No Delivery Date Specified.');
        }

        $formatedDate = $this->_timezone->formatDate($date, 'medium');

        return $formatedDate;
    }

    public function getFormatedDeliveryDateToSave($date = null)
    {
        $formatedDate = null;
        if (empty($date) || $date === null || $date === '0000-00-00 00:00:00') {
            return null;
        }

        $date = str_replace('/', '-', $date);

        $timestamp = null;
        try {
            $timestamp = strtotime($date);
            $dateArray = explode('-', $date);
            if (count($dateArray) !== 3) {
                return null;
            }
            $formatedDate = date('Y-m-d H:i:s', $timestamp);
        } catch (\Exception $e) {
            return null;
        }

        return $formatedDate;
    }

    public function getNonWorkingDays()
    {
        if (is_null($this->nonworking_days)) {
            $days = $this->scopeConfig->getValue(self::XML_DAYOFF, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->nonworking_days = [];
            if ($days) {
                $this->nonworking_days = $this->getConfigData($days);
            }
        }

        return $this->nonworking_days;
    }

    public function getFormattedNonWorkingDays()
    {
        $result = [];
        if (is_null($this->nonworking_days)) {
            $days = $this->scopeConfig->getValue(self::XML_DAYOFF, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->nonworking_days = [];
            if ($days) {
                $this->nonworking_days = $this->getConfigData($days);
            }
        }
        $nonworking_days = $this->nonworking_days;
        foreach ($nonworking_days as $key => $value) {
            $result[] = date('d-m-Y', strtotime(date($value['deliveryday'] . '-' . $value['deliverymonth'] . '-' . $value['deliveryyear'])));
        }

        return $result;
    }

    public function getNonWorkingPeriod()
    {
        if (is_null($this->nonworking_period)) {
            $days = $this->scopeConfig->getValue(self::XML_DAYOFF, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->nonworking_period = [];
            if ($days) {
                $this->nonworking_period = $this->getConfigData($days);
            }
        }

        $dates = [];
        foreach ($this->nonworking_period as $period) {
            $date_from = date($period['from_year'] . '-' . $period['from_month'] . '-' . $period['from_day']);
            $date_from = strtotime($date_from);
            $date_to = date($period['to_year'] . '-' . $period['to_month'] . '-' . $period['to_day']);
            $date_to = strtotime($date_to);

            for ($i = $date_from; $i <= $date_to; $i += 86400) {
                $dates[] = date('Y-m-d', $i);
            }
        }

        $nonworking_period = [];
        foreach ($dates as $date) {
            $nonworking_period1 = [
                'day' => date('d', strtotime($date)),
                'month' => date('m', strtotime($date)),
                'year' => date('Y', strtotime($date)),
            ];
            $nonworking_period[] = $nonworking_period1;
        }

        return $nonworking_period;
    }

    public function getFormattedNonWorkingPeriod()
    {
        if (is_null($this->nonworking_period)) {
            $storeId = $this->getCurrentStoreId();
            $days = $this->scopeConfig->getValue(self::XML_PERIOD_OFF, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
            $this->nonworking_period = [];
            if ($days) {
                $this->nonworking_period = $this->getConfigData($days);
            }
        }
        $dates = [];
        foreach ($this->nonworking_period as $period) {
            $date_from = date($period['from_year'] . '-' . $period['from_month'] . '-' . $period['from_day']);
            $date_from = strtotime($date_from);
            $date_to = date($period['to_year'] . '-' . $period['to_month'] . '-' . $period['to_day']);
            $date_to = strtotime($date_to);

            for ($i = $date_from; $i <= $date_to; $i += 86400) {
                $dates[] = date("d-m-Y", $i);
            }
        }
        return $dates;
    }

    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }

    public function getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }

    public function getTimeSlots()
    {
        if (is_null($this->timeslot)) {
            $timeslot = $this->scopeConfig->getValue(self::XML_DELIVERY_TIMESLOTS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->timeslot = [];
            if ($timeslot) {
                $this->timeslot = $this->getConfigData($timeslot);
            }
        }

        return $this->timeslot;
    }

    public function getFormattedDisableSlots()
    {
        if (is_null($this->disable_timeslot)) {
            $timeslot = $this->scopeConfig->getValue(self::XML_DISABLE_TIMESLOT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $this->disable_timeslot = [];
            if (isset($timeslot)) {
                $this->disable_timeslot = $this->getConfigData($timeslot);
            }
        }
        $disableSlots = [];
        foreach ($this->disable_timeslot as $disableslot) {
            if (isset($disableslot['day']) && $disableslot['day'] != "") {
                $disableSlotsTemp['day'] = $disableslot['day'];
                if (isset($disableslot['time_slot'])) {
                    $disableSlotsTemp['time_slot'] = $disableslot['time_slot'];
                }
                $disableSlots[] = $disableSlotsTemp;
            }
        }

        return $disableSlots;
    }

    public function getFormattedDisableSlotsDate()
    {
        if (is_null($this->disable_timeslot_date)) {
            $timeslot = $this->scopeConfig->getValue(self::XML_DISABLE_TIMESLOT_DATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $this->disable_timeslot_date = [];
            if (isset($timeslot)) {
                $this->disable_timeslot_date = $this->getConfigData($timeslot);
            }
        }

        $disableSlotsDate = [];
        foreach ($this->disable_timeslot_date as $disableslot_date) {
            $disableSlotsDateTemp['date'] = date('d-m-Y', strtotime(date($disableslot_date['day'] . '-' . $disableslot_date['month'] . '-' . $disableslot_date['year'])));
            if (isset($disableslot_date['time_slot'])) {
                $disableSlotsDateTemp['time_slot'] = $disableslot_date['time_slot'];
            }
            $disableSlotsDate[] = $disableSlotsDateTemp;
        }

        return $disableSlotsDate;
    }

    public function isAdmin()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $app_state = $om->get('\Magento\Framework\App\State');
        $area_code = $app_state->getAreaCode();
        if ($app_state->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return true;
        }

        return false;
    }

    public function getAllStoreDomains()
    {
        $domains = [];
        foreach ($this->_storeManager->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $url = $store->getConfig('web/unsecure/base_url');
                    $domains[] = $this->getFormatUrl($url);
                    $url = $store->getConfig('web/secure/base_url');
                    $domains[] = $this->getFormatUrl($url);

                }
            }
        }
        return array_unique($domains);
    }

    public function getDataInfo()
    {
        $data = $this->scopeConfig->getValue(self::XML_PATH_DATA, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return json_decode(base64_decode($this->_encryptor->decrypt($data), true));
        //return $this->_zend->decode(base64_decode($this->_encryptor->decrypt($data)));
    }

    public function getFormatUrl($url)
    {
        $input = trim($url, '/');
        if (!preg_match('#^http(s)?://#', $input)) {
            $input = 'http://' . $input;
        }
        $urlParts = parse_url($input);
        if (isset($urlParts['path'])) {
            $domain = preg_replace('/^www\./', '', $urlParts['host'] . $urlParts['path']);
        } else {
            $domain = preg_replace('/^www\./', '', $urlParts['host']);
        }

        return $domain;
    }

    public function convertDateFormatToJQueryUi($php_format)
    {
        $SYMBOLS_MATCHING = [
            // Day
            'd' => 'dd',
            'D' => 'D',
            'j' => 'd',
            'l' => 'DD',
            'N' => '',
            'S' => '',
            'w' => '',
            'z' => 'o',
            // Week
            'W' => '',
            // Month
            'F' => 'MM',
            'm' => 'mm',
            'M' => 'M',
            'n' => 'm',
            't' => '',
            // Year
            'L' => '',
            'o' => '',
            'Y' => 'yy',
            'y' => 'y',
            // Time
            'a' => '',
            'A' => '',
            'B' => '',
            'g' => 'HH',
            'G' => '',
            'h' => '',
            'H' => 'HH',
            'i' => 'mm',
            's' => 'ss',
            'u' => ''
        ];
        $jqueryui_format = "";
        $escaping = false;
        for ($i = 0; $i < strlen($php_format); $i++) {
            $char = $php_format[$i];
            if ($char === '\\') { // PHP date format escaping character
                $i++;
                if ($escaping) {
                    $jqueryui_format .= $php_format[$i];
                } else {
                    $jqueryui_format .= '\'' . $php_format[$i];
                }
                $escaping = true;
            } else {
                if ($escaping) {
                    $jqueryui_format .= "'";
                    $escaping = false;
                }
                if (isset($SYMBOLS_MATCHING[$char])) {
                    $jqueryui_format .= $SYMBOLS_MATCHING[$char];
                } else {
                    $jqueryui_format .= $char;
                }
            }
        }
        return $jqueryui_format;
    }


    public function addSameDayCharges($selectedDate)
    {
        $sameDayCharages['addCharge'] = false;

        $sameDayEnable = $this->getSameDayCharges();
        if ($sameDayEnable) {
            $date = $this->_timezone->date()->format('Y-m-d');
            $todayDate = date('Y-m-d', strtotime($date));
            $selectedDate = date('Y-m-d', strtotime($selectedDate));
            if ($todayDate === $selectedDate) {
                $sameDayCharages['addCharge'] = true;
                $sameDayCharages['charges'] = $sameDayEnable;
            }
        }
        return $sameDayCharages;
    }

    public function disableSameDayBasedOnDailyQuota()
    {
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $storeids = [];
        foreach ($this->_storeManager->getWebsites() as $website) {
            if($website->getWebsiteId() == $websiteId){
                foreach ($website->getGroups() as $group) {
                    $stores = $group->getStores();
                    foreach ($stores as $store) {
                        if($store->getIsActive()){
                            $storeids[] = $store->getStoreId();
                        }
                    }
                }
            }
        }

        $quotaLimit = $this->getDailyQuotaLimit();
        $futurequotaLimit = $this->getFutureQuotaLimit();
        $currenttimeobj = new \DateTime($this->_timezone->date()->format('Y-m-d h:i A'));

        $startdate = $this->_date->date('Y-m-d 00:00:01', $currenttimeobj);
        $enddate = date('Y-m-d 23:59:59', strtotime($startdate . ' +'.$futurequotaLimit.' day'));

        $orderCollectionByDate = $this->_collectionFactory->create();
        $collection = $orderCollectionByDate->addFieldToSelect('shipping_arrival_date')->addFieldToFilter('shipping_arrival_date', ['from' => $startdate, 'to' => $enddate]);
        $collection->addFieldToFilter('store_id', ['in' => $storeids]);

        if ($this->getReturnQuota()) {
            $collection->addFieldToFilter('status', ['nin' => [\Magento\Sales\Model\Order::STATE_CANCELED, \Magento\Sales\Model\Order::STATE_CLOSED]]);
        }

        $quota = [];
        $dates = [];
        $arrayofDays = [];

        $quota['disable'] = false;

        foreach($collection as $data){
            $daytocheck = date('d-m-Y', strtotime($data->getShippingArrivalDate()));
            if(array_key_exists($daytocheck,$arrayofDays)){
                $arrayofDays[$daytocheck] +=1;
            }else{
                $arrayofDays[$daytocheck] = 1;
            }
        }
        foreach($arrayofDays as $key => $value){
            if ($value >= $quotaLimit && $quotaLimit !== 0) {
                $quota['disable'] = true;
                array_push($dates, $key);
            }
        }
        $quota['day_to_disable'] = $dates;
        return $quota;
    }

    public function disableDayBasedOnCutoff()
    {
        $enableCutoff = $this->isCuttoffEnable();

        $cutoff = [];
        $cutoff['status'] = false;
        if ($enableCutoff) {
            $getCuttofValue = $this->getCuttoffValue();

            if (($getCuttofValue === "00,00,00") === false) {
                $getCuttofValue = str_replace(",", ":", $getCuttofValue);
                $cutoffobj = new \DateTime($this->_timezone->date()->format('d-m-Y') . " " . $getCuttofValue);
                $currenttimeobj = new \DateTime($this->_timezone->date()->format('Y-m-d H:i:s'));
                if ($currenttimeobj >= $cutoffobj) {
                    $cutoff['status'] = true;
                    $cutoff['day_to_disable'] = [$this->_timezone->date()->format('d-m-Y')];
                } else {
                    $cutoff['status'] = false;
                }
            } else {
                $cutoff['status'] = false;
            }
        } else {
            $cutoff['status'] = false;
        }
        return $cutoff;
    }

    public function sendEmailNotification()
    {
        $email_notification_yesno = $this->scopeConfig->getValue(self::EMAIL_NOTIFICATION_YESNO, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($this->isEnable()) {
            if ($email_notification_yesno) {
                $email_notification_option = $this->scopeConfig->getValue(self::EMAIL_NOTIFICATION_OPTIONS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                $email_notification_id = $this->scopeConfig->getValue(self::EMAIL_NOTIFICATION_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                $date = strtotime("+" . $email_notification_option . " day", time());
                $new_date = date('Y-m-d', $date);
                $dateStart = date('Y-m-d 00:00:00', strtotime($new_date));
                $dateEnd = date('Y-m-d 23:59:59', strtotime($new_date));

                $collection = $this->getOrderCollection($dateStart, $dateEnd);

                if ($collection->count() > 0) {
                    $orders_html = '';
                    $data = $collection->getData();

                    $identity = $this->scopeConfig->getValue(self::EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                    $templateId = 'deliverydate_email_notification_template';
                    $subject = __('Delivery Date Notification Order');
                    $vars = [
                        'date_created' => date("M d,Y"),
                        'subject' => $subject,
                        // 'orders' => $orders_html,
                        'order' => $data
                    ];

                    $storeid = $this->getCurrentStoreId();

                    $email_notification_ids = explode(",", $email_notification_id);
                    foreach ($email_notification_ids as $email) {
                        $emails = explode("@", $email);
                        $username = $emails[0];
                        $transport = $this->_transportBuilder
                            ->setTemplateIdentifier($templateId)
                            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeid])
                            ->setTemplateVars($vars)
                            ->setFrom($identity)
                            ->addTo($email, $username)
                            ->getTransport();
                        $transport->sendMessage();
                    }
                }
            }
        }
    }

    public function isEnable()
    {
        $storeID = $this->_storeManager->getStore()->getId();
        if ((!$this->scopeConfig->getValue(self::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) || (!in_array($storeID, $this->getAllWebsites()))) {
            return false;
        } else return true;
    }

    public function getAllWebsites()
    {
        $value = $this->scopeConfig->getValue(self::XML_PATH_INSTALLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$value) {
            return [];
        }
        $data = $this->scopeConfig->getValue(self::XML_PATH_DATA, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $web = $this->scopeConfig->getValue(self::XML_PATH_WEBSITES, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        //$websites = explode(',', str_replace($data, '', $this->_encryptor->decrypt($web)));
        $websites = explode(',', str_replace($data, '', $this->_encryptor->decrypt($web)));
        $websites = array_diff($websites, ['']);
        return $websites;
    }

    public function getOrderCollection($dateStart, $dateEnd)
    {
        $orderCollectionByDate = $this->_collectionFactory->create();
        $collection = $orderCollectionByDate->addAttributeToFilter('shipping_arrival_date', ['from' => $dateStart, 'to' => $dateEnd]);
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $joinTable = $resource->getTableName('sales_order_address');

        $collection
            ->getSelect()
            ->joinLeft(
                [$billingAliasName => $joinTable],
                "(main_table.entity_id = {$billingAliasName}.parent_id"
                    . " AND {$billingAliasName}.address_type = 'billing')",
                [
                    'billing_firstname' => $billingAliasName . '.firstname',
                    'billing_lastname' => $billingAliasName . '.lastname',
                    'billing_telephone' => $billingAliasName . '.telephone',
                    'billing_postcode' => $billingAliasName . '.postcode',
                ]
            )
            ->joinLeft(
                [$shippingAliasName => $joinTable],
                "(main_table.entity_id = {$shippingAliasName}.parent_id"
                    . " AND {$shippingAliasName}.address_type = 'shipping')",
                [
                    'shipping_firstname' => $shippingAliasName . '.firstname',
                    'shipping_lastname' => $shippingAliasName . '.lastname',
                    'shipping_telephone' => $shippingAliasName . '.telephone',
                    'shipping_postcode' => $shippingAliasName . '.postcode',
                ]
            );

        return $collection;
    }

    public function getConfigData($data)
    {

        if ($this->isJson($data)) {
            return json_decode($data, true);
        } else {
            return $this->serializer->unserialize($data);
        }
    }

    /**
     * Magento 2.2 are storing serialized data as json instead of serialize array
     *
     * @param   string $string
     * @return  bool
     */
    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function isEnableAtProductPage()
    {
        return ($this->getOnWhichPage() == 2);
    }

    public function getDeliveryDateView()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/delivery_method', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getProductLevelConfig()
    {
        $enabledMethod = $this->getDeliveryDateView();
        switch ($enabledMethod) {
            case 1:
                $template = 'Biztech_Deliverydate/product/view/product-calender';
                break;

            case 2:
                $template = 'Biztech_Deliverydate/product/view/product-timeslot';
                break;
            default:
                $template = 'Biztech_Deliverydate/product/view/product-calender';
                break;
        }

        $nonworking_days = $this->getFormattedNonWorkingDays();
        $nonworking_period = $this->getFormattedNonWorkingPeriod();
        $off_days = array_merge((array) $nonworking_days, (array) $nonworking_period);

        $cutoff = $this->disableDayBasedOnCutoff();
        if ($cutoff['status'] === true) {
            $off_days = array_values(array_unique(array_merge($off_days, $cutoff['day_to_disable']), SORT_REGULAR));
        }
        $deliverydateLabel = $this->getDeliverydatelabel() ? $this->getDeliverydatelabel() : 'Deliverydate Date';
        $deliverydateCommentsLabel = $this->getDeliveryDateCommentsLabel() ? $this->getDeliveryDateCommentsLabel() : 'Deliverydate Comments';
        $timeslotTableLabel = $this->getTableLabel();
        $timeslotTableLabel = $timeslotTableLabel ? $timeslotTableLabel : 'Timeslot\'s';
        $dayOffs = $this->getDayOffs();
        $dayDiff = $this->getConfigValue('deliverydate/deliverydate_general/deliverytime_day_diff', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) ? $this->getConfigValue('deliverydate/deliverydate_general/deliverytime_day_diff', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) : 0;
        $timeDiff = $this->getConfigValue('deliverydate/deliverydate_timeslots/deliverytime_diff', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) ? $this->getConfigValue('deliverydate/deliverydate_timeslots/deliverytime_diff', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) : 0;
        $timeslot = $this->getavailableTimeslot();

        $display = false;
        if ($this->isEnable() && $this->getOnWhichPage() == 2) {
            $display = true;
        }

        $config = [
            'templateConfig' => [
                'template' => $template,
                'enabledMethod' => $enabledMethod,
                'deliverydateLabel' => __($deliverydateLabel),
                'deliverydateComments' => __($deliverydateCommentsLabel),
                'showHtml' => (int) $this->getConfigValue('deliverydate/deliverydate_general/show_html', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'displayHtml' => nl2br($this->getConfigValue('deliverydate/deliverydate_general/deliverydate_html', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)),
            ],
            'general' => [
                'enabled' => (int) $display,
                'disabledDates' => $off_days,
                'dayOffs' => $dayOffs,
                'dateFormat' => $this->convertDateFormatToJQueryUi($this->getDeliveryDateFormat()),
                'timeFormat' => $this->convertDateFormatToJQueryUi($this->getDeliveryTimeFormat()),
                'dayDiff' => (int) $dayDiff,
                'timeDiff' => $timeDiff,
                'isMandatory' => (int) $this->getConfigValue('deliverydate/deliverydate_general/is_mandatory', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'useCallFeature' => (int) $this->useCallFeature(),
                'callMeLabel' =>  $this->getDeliveryDateCallMeLabel()
            ],
            'calendar' => [
                'options' => [
                    'showsTime' => (int) $this->getConfigValue('deliverydate/deliverydate_general/deliverytime_enable_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    'buttonImage' => $this->_assetRepo->getUrl("Biztech_Deliverydate::images/datepicker.png"),
                    'interval' => (int) $this->getConfigValue('deliverydate/deliverydate_general/datepicker_time_mininterval', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    'buttonText' => __('Select Date'),
                    'showAnim' => $this->getConfigValue('deliverydate/deliverydate_general/datepicker_enable_animation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    'showButtonPanel' => (bool) $this->getConfigValue('deliverydate/deliverydate_general/datepicker_buttom_bar', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    'isRTL' => (bool) $this->getConfigValue('deliverydate/deliverydate_general/datepicker_rtl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    'maxDate' => (int) $this->getConfigValue('deliverydate/deliverydate_general/datepicker_delivery_intervals', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                ]
            ],
            'timeslot' => [
                'enabled_timeslots' => $timeslot,
                'timeslotTableLabel' => __($timeslotTableLabel),
            ]
        ];
        return $config;
    }

    public function getDeliveryTimeFormat()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverytime_format', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function useCallFeature()
    {
        return (int) $this->getConfigValue('deliverydate/deliverydate_general/show_callme', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDeliveryDateCallMeLabel()
    {
        return (string) $this->getConfigValue('deliverydate/deliverydate_general/deliverydate_callme_label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDeliveryDateFormat()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverydate_format', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDayDiff()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverytime_day_diff', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getTimeDiff()
    {
        return $this->getConfigValue('deliverydate/deliverydate_timeslots/deliverytime_diff', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getRowHeadingformat($deliverydate_format, $datestring)
    {
        $date = new \DateTime($datestring);
        $deliveryDateDisplay = $date->format($deliverydate_format);
        $deliveryDate = $date->format('d-m-Y');
        $day = $date->format('l');
        return $deliveryDateDisplay . ' ' . __($day);
    }

    protected function getDateRange($dateRange, $timeslot, $deliverytime_format = 'g:i a', $deliverydate_format = 'Y-m-d')
    {
        $displaySlots = [];
        $time_diff = ceil($this->getTimeDiff());

        $disable_slot = $this->getFormattedDisableSlots();
        $disable_slot_date = $this->getFormattedDisableSlotsDate();

        if ($this->getDayOffs() === '0') {
            $day_off = explode(',', $this->getDayOffs());
        } else {
            $day_off = $this->getDayOffs() ? explode(',', $this->getDayOffs()) : [];
        }

        $nonworking_days = $this->getFormattedNonWorkingDays();
        $nonworking_period = $this->getFormattedNonWorkingPeriod();
        $nonworking_dates = array_merge((array) $nonworking_days, (array) $nonworking_period);

        $cutoff = $this->disableDayBasedOnCutoff();

        if ($cutoff['status'] === true) {
            $nonworking_dates = array_values(array_unique(array_merge($nonworking_dates, $cutoff['day_to_disable']), SORT_REGULAR));
        }

        $currenttimeobj = new \DateTime($this->_timezone->date()->format('Y-m-d h:i A'));
        $currentTime = $this->_date->date('Y-m-d h:i A', $currenttimeobj);

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

                    if ((in_array($day_no, $day_off)) || (strtotime($_sTime) < strtotime($delivery_start_time)) || (in_array($deliveryDate, $nonworking_dates))
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

    public function getDeliverydatelabel()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverydate_label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getOnWhichPage()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/on_which_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getDeliveryDateCommentsLabel()
    {
        return $this->getConfigValue('deliverydate/deliverydate_general/deliverydate_comments_label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getTableLabel()
    {
        return $this->getConfigValue('deliverydate/deliverydate_timeslots/table_label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getNoofDaysofDelivery()
    {
        return $this->getConfigValue('deliverydate/deliverydate_timeslots/no_of_deliverydays', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function useCategoryForProduct()
    {
        return (bool) $this->getConfigValue('deliverydate/deliverydate_general/allow_for_category', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSelectApplicableCategoryForProduct()
    {
        $selectedCategories = $this->getConfigValue('deliverydate/deliverydate_general/use_category', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (strlen($selectedCategories) > 0) {
            return explode(',', $selectedCategories);
        } else {
            return [];
        }
    }

    public function useCustomerGroup()
    {
        return (bool) $this->getConfigValue('deliverydate/deliverydate_general/use_customer_group', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDailyQuotaLimit()
    {
        return (int) $this->getConfigValue('deliverydate/deliverydate_general/quota_limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getFutureQuotaLimit()
    {
        return (int) $this->getConfigValue('deliverydate/deliverydate_general/quota_future_limit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSameDayCharges()
    {
        $charges = $this->getConfigValue('deliverydate/deliverydate_general/same_day_deliverycharges', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (is_null($charges) || $charges == 0) {
            return false;
        } else {
            return (float) $charges;
        }
    }

    public function displaySamedaycharges()
    {
        $price = $this->getSameDayCharges();
        if ($price) {
            return $price = $this->priceHelper->currency($price, true, false);
        } else {
            return "";
        }
    }

    public function getCuttoffValue()
    {
        return (string) $this->getConfigValue('deliverydate/deliverydate_general/cut_offtime', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function isCuttoffEnable()
    {
        return (bool) $this->getConfigValue('deliverydate/deliverydate_general/enable_cutoff', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getReturnQuota()
    {
        return (bool) $this->getConfigValue('deliverydate/deliverydate_general/return_quota', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function useAllowedCustomerGroup()
    {
        $selectedGroups = $this->getConfigValue('deliverydate/deliverydate_general/selected_group', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (strlen($selectedGroups) > 0) {
            return explode(',', $selectedGroups);
        } else {
            return [];
        }
    }

    public function isAppliableWithCategory($product)
    {
        if ($this->useCategoryForProduct()) {
            $allowedCategory = $this->getSelectApplicableCategoryForProduct();
            $categoryIds = $product->getCategoryIds();
            $result = array_intersect($allowedCategory, $categoryIds);
            return (empty($result) === false);
        } else {
            return true;
        }
    }

    public function checkCurrentUserAllowed($userGroupId)
    {
        if ($this->useCustomerGroup()) {
            return in_array($userGroupId, $this->useAllowedCustomerGroup());
        } else {
            return true;
        }
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

    public function getavailableTimeslot()
    {
        $displaySlots = [];

        $timeslot = $this->getTimeSlots();
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
}
