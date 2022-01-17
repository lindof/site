<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Email: vladokrushko@gmail.com
 * Date: 03.08.2018
 * Time: 14:56
 */

namespace IWD\StoreLocator\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Message\Session\Proxy as Session;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\UrlInterface;
use Magento\Framework\Mail\Template\TransportBuilder;

class Config extends AbstractHelper
{
    public $storeManager;
    public $resourceConfig;
    public $curlFactory;
    public $session;
    public $customerSession;
    public $flagFactory;
    public $response = null;
    public $jsonHelper;
    public $_request;
    protected $_transportBuilder;

    const XML_PATH_GENERAL_IS_ACTIVE = 'iwd_storelocator/general/is_active';
    const XML_PATH_GENERAL_TITLE = 'iwd_storelocator/general/title';
    const XML_PATH_GENERAL_PATH = 'iwd_storelocator/general/path';
    const XML_PATH_GENERAL_LINK_VISIBILITY = 'iwd_storelocator/general/link_visibility';
    const XML_PATH_GENERAL_LINK_TEXT = 'iwd_storelocator/general/link_text';
    const XML_PATH_GENERAL_CMS = 'iwd_storelocator/general/cms';
    const XML_PATH_GENERAL_META_KEYWORD = 'iwd_storelocator/general/meta_keyword';
    const XML_PATH_GENERAL_META_DESCRIPTION = 'iwd_storelocator/general/meta_description';

    const XML_PATH_API_SETTINGS_TYPE = 'iwd_storelocator/api_settings/type';
    const XML_PATH_API_SETTINGS_GOOGLE_BROWSER_API_KEY = 'iwd_storelocator/api_settings/google_browser_api_key';
    const XML_PATH_API_SETTINGS_GOOGLE_SERVER_API_KEY = 'iwd_storelocator/api_settings/google_server_api_key';
    const XML_PATH_API_SETTINGS_GOOGLE_TYPE_CONTROL = 'iwd_storelocator/api_settings/google_type_control';
    const XML_PATH_API_SETTINGS_GOOGLE_SCALE_CONTROL = 'iwd_storelocator/api_settings/google_scale_control';
    const XML_PATH_API_SETTINGS_GOOGLE_SCROLLWHEEL_ZOOMING = 'iwd_storelocator/api_settings/google_scrollwheel_zooming';
    const XML_PATH_API_SETTINGS_HERE_APP_ID = 'iwd_storelocator/api_settings/here_app_id';
    const XML_PATH_API_SETTINGS_HERE_APP_CODE = 'iwd_storelocator/api_settings/here_app_code';
    const XML_PATH_API_SETTINGS_MARKER = 'iwd_storelocator/api_settings/marker';

    const XML_PATH_SEARCH_SEARCH_ONLOAD = 'iwd_storelocator/search/search_onload';
    const XML_PATH_SEARCH_PAGINATION = 'iwd_storelocator/search/pagination';
    const XML_PATH_SEARCH_PAGE_SIZE = 'iwd_storelocator/search/page_size';
    const XML_PATH_SEARCH_FILTER_RADIUS = 'iwd_storelocator/search/filter_radius';
    const XML_PATH_SEARCH_RADIUS_LIST = 'iwd_storelocator/search/radius_list';
    const XML_PATH_SEARCH_DEFAULT_RADIUS = 'iwd_storelocator/search/default_radius';
    const XML_PATH_SEARCH_METRIC = 'iwd_storelocator/search/metric';
    const XML_PATH_SEARCH_ORDER = 'iwd_storelocator/search/order';
    const XML_PATH_SEARCH_MESSAGE = 'iwd_storelocator/search/message';

    const XML_PATH_DESIGN_FULL_WIDTH = 'iwd_storelocator/design/full_width';
    const XML_PATH_DESIGN_HIDE_PLACEHOLDER = 'iwd_storelocator/design/hide_placeholders';
    const XML_PATH_DESIGN_PLACEHOLDER = 'iwd_storelocator/design/placeholder';
    const XML_PATH_DESIGN_HIGHLIGHT_RESULT = 'iwd_storelocator/design/highlight_result';
    const XML_PATH_DESIGN_FILL_COLOR = 'iwd_storelocator/design/fill_color';
    const XML_PATH_DESIGN_OPACITY = 'iwd_storelocator/design/opacity';
    const XML_PATH_DESIGN_STROKE_COLOR = 'iwd_storelocator/design/stroke_color';
    const XML_PATH_DESIGN_STROKE_OPACITY = 'iwd_storelocator/design/stroke_opacity';
    const XML_PATH_DESIGN_STROKE_WEIGHT = 'iwd_storelocator/design/stroke_weight';


    const XML_PATH_AUTO_FILL_COUNT = 'iwd_storelocator/auto_fill/count';
    const XML_PATH_AUTO_FILL_ENABLED = 'iwd_storelocator/auto_fill/enabled';
    const XML_PATH_AUTO_FILL_TIME = 'iwd_storelocator/auto_fill/time';
    const XML_PATH_AUTO_FILL_FREQUENCY = 'iwd_storelocator/auto_fill/frequency';


    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        CurlFactory $curlFactory,
        Session $session,
        ConfigInterface $resourceConfig,
        JsonHelper $jsonHelper,
        TransportBuilder $transportBuilder
    )
    {
        parent::__construct($context);
        $this->resourceConfig = $resourceConfig;
        $this->storeManager = $storeManager;
        $this->curlFactory = $curlFactory;
        $this->session = $session;
        $this->customerSession = $customerSession;
        $this->jsonHelper = $jsonHelper;
        $this->_transportBuilder = $transportBuilder;
    }

    public function getIsActive()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_GENERAL_IS_ACTIVE);
    }

    public function getTitle()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_TITLE);
    }

    public function getPath()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_PATH);
    }

    public function getLinkVisibility()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_GENERAL_LINK_VISIBILITY);
    }

    public function getLinkText()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_LINK_TEXT);
    }

    public function getCMSBlock()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_CMS);
    }

    public function getMetaKeyWord()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_META_KEYWORD);
    }

    public function getMetaDescription()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_META_DESCRIPTION);
    }

    public function getApiType()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_SETTINGS_TYPE);
    }

    public function getGoogleBrowserApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_SETTINGS_GOOGLE_BROWSER_API_KEY);
    }

    public function getGoogleServerApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_SETTINGS_GOOGLE_SERVER_API_KEY);
    }

    public function getIsMapTypeControl()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_API_SETTINGS_GOOGLE_TYPE_CONTROL);
    }

    public function getIsMapScaleControl()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_API_SETTINGS_GOOGLE_SCALE_CONTROL);
    }

    public function getIsMapScrollWheelZooming()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_API_SETTINGS_GOOGLE_SCROLLWHEEL_ZOOMING);
    }

    public function getHereAppId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_SETTINGS_HERE_APP_ID);
    }

    public function getHereAppCode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_SETTINGS_HERE_APP_CODE);
    }

    public function getMapMarker()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_API_SETTINGS_MARKER);
    }

    public function getIsSearchOnLoad()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_SEARCH_SEARCH_ONLOAD);
    }

    public function isEnabledPagination()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_SEARCH_PAGINATION);
    }

    public function getPageSize()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SEARCH_PAGE_SIZE);
    }

    public function getFilterRadius()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SEARCH_FILTER_RADIUS);
    }

    public function getRadiusList()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SEARCH_RADIUS_LIST);
    }

    public function getDefaultRadius()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SEARCH_DEFAULT_RADIUS);
    }

    public function getOrder()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SEARCH_ORDER);
    }

    public function getMetric()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SEARCH_METRIC);
    }

    public function getSearchMessage()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SEARCH_MESSAGE);
    }

    public function getIsFullWidth()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_DESIGN_FULL_WIDTH);
    }

    public function getIsHidePlaceholder()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_DESIGN_HIDE_PLACEHOLDER);
    }

    public function getPlaceholderLocation()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DESIGN_PLACEHOLDER);
    }

    public function getIsHighlightResult()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_DESIGN_HIGHLIGHT_RESULT);
    }

    public function getFillColor()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DESIGN_FILL_COLOR);
    }

    public function getOpacity()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DESIGN_OPACITY);
    }

    public function getStrokeColor()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DESIGN_STROKE_COLOR);
    }

    public function getStrokeOpacity()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DESIGN_STROKE_OPACITY);
    }

    public function getStrokeWeight()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DESIGN_STROKE_WEIGHT);
    }

    public function getAutoFillCount()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AUTO_FILL_COUNT);
    }

    public function isAutoFillEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_AUTO_FILL_ENABLED);
    }

    public function getAutoFillTime()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AUTO_FILL_TIME);
    }

    public function getAutoFillFrequency()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AUTO_FILL_FREQUENCY);
    }

}
