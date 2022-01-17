<?php

namespace IWD\StoreLocator\Block;

use IWD\StoreLocator\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Framework\View\Element\Template;

/**
 * Class Search
 * @package IWD\StoreLocator\Block
 */
class Search extends Template
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    private $countryFactory;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    private $regionFactory;

    /**
     * @var ItemCollectionFactory
     */
    private $ItemCollectionFactory;

    /**
     * @var \IWD\StoreLocator\Model\Image
     */
    private $imageHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    public $config;

    /**
     * Search constructor.
     * @param Template\Context $context
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param ItemCollectionFactory $ItemCollectionFactory
     * @param \IWD\StoreLocator\Model\Image $imageHelper
     * @param \IWD\StoreLocator\Helper\Config $config
     */
    public function __construct(
        Template\Context $context,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        ItemCollectionFactory $ItemCollectionFactory,
        \IWD\StoreLocator\Model\Image $imageHelper,
        \IWD\StoreLocator\Helper\Config $config
    )
    {
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->ItemCollectionFactory = $ItemCollectionFactory;
        $this->storeManager = $context->getStoreManager();
        $this->imageHelper = $imageHelper;
        $this->scopeConfig = $context->getScopeConfig();
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $metaTitle = $this->scopeConfig->getValue('iwd_storelocator/general/meta_title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $metaKeywords = $this->scopeConfig->getValue('iwd_storelocator/general/meta_keyword', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $metaDescription = $this->scopeConfig->getValue('iwd_storelocator/general/meta_description', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $this->pageConfig->getTitle()->set($metaTitle);
        $this->pageConfig->setKeywords($metaKeywords);
        $this->pageConfig->setDescription($metaDescription);

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            // Setting empty page title if content heading is absent
            $pageMainTitle->setPageTitle($this->escapeHtml($metaTitle));
        }
        return parent::_prepareLayout();
    }

    public function getGMBrowserApiKey()
    {
        return $this->scopeConfig->getValue('iwd_storelocator/api_settings/google_browser_api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSearchOnLoad()
    {
        return (int)$this->scopeConfig->getValue('iwd_storelocator/search/search_onload', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getConfigOption($path, $bool = false)
    {
        if (!$bool) {
            return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }

        return (bool)$this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function init()
    {
        $metric = $this->scopeConfig->getValue('iwd_storelocator/search/metric', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($metric == 1) {
            $metric = __('Km');
        } elseif ($metric == 2) {
            $metric = __('Miles');
        }

        $fillColor = $this->getConfigOption("iwd_storelocator/design/fill_color");
        if (!preg_match('/#/i', $fillColor)) {
            $fillColor = '#' . $fillColor;
        }

        $strokeColor = $this->getConfigOption("iwd_storelocator/design/stroke_color");
        if (!preg_match('/#/i', $strokeColor)) {
            $strokeColor = '#' . $strokeColor;
        }

        $folderName = \IWD\StoreLocator\Model\Config\Backend\Image\Marker::UPLOAD_DIR;

        $placeholder = $this->getConfigOption('iwd_storelocator/design/placeholder');
        if (!empty($placeholder)) {
            $path = $folderName . '/' . $placeholder;
            $placeholder = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
        } else {
            $placeholder = $this->getViewFileUrl('IWD_StoreLocator::images/placeholder.png');
        }

        $marker = $this->getConfigOption('iwd_storelocator/gm/marker');

        if (!empty($marker)) {
            $path = $folderName . '/' . $marker;
            $marker = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $path;
        } else {
            $marker = $this->getViewFileUrl('IWD_StoreLocator::images/marker.png');
        }
        $data = [
            'StoreLocator' => [
                'url' => $this->_urlBuilder->getBaseUrl() . 'rest/V1/storelocator/search/',
                'api_type' => $this->config->getApiType(),
                'searchOnload' => $this->getSearchOnLoad(),
                'markerUrl' => $this->getViewFileUrl('IWD_StoreLocator::images/marker.png'),
                'closeUrl' => $this->getViewFileUrl('IWD_StoreLocator::images/close.png'),
                'pagination' => (int)$this->getConfigOption('iwd_storelocator/search/pagination'),
                'dropdown' => $this->_prepareCountrytRegion(),
                'baseUrlImage' => $this->imageHelper->getBaseUrl(),
                'radiusDecorator' => [
                    'active' => (int)$this->getConfigOption("iwd_storelocator/design/hightlight_result"),
                    'fillColor' => $fillColor,
                    'fillOpacity' => $this->getConfigOption("iwd_storelocator/design/opacity"),
                    'strokeColor' => $strokeColor,
                    'strokeOpacity' => $this->getConfigOption("iwd_storelocator/design/stroke_opacity"),
                    'strokeWeight' => $this->getConfigOption("iwd_storelocator/design/stroke_weight"),
                ],

                'metric' => $metric,
                'placeholder_visability' => !$this->getConfigOption("iwd_storelocator/design/hide_placeholders", true),
                'empty_message' => $this->getConfigOption('iwd_storelocator/search/message'),
                'placeholder' => $placeholder,
                'marker' => $marker,
                'pageSize' => $this->getConfigOption('iwd_storelocator/search/page_size'),
            ]
        ];
        if ($this->config->getApiType() === 'here') {
            $data['StoreLocator']['init_here'] = true;
            $data['StoreLocator']['app_id'] = $this->config->getHereAppId();
            $data['StoreLocator']['app_code'] = $this->config->getHereAppCode();
        }

        return json_encode($data);
    }

    private function _prepareCountrytRegion()
    {
        $list = $this->_getListCountries();
        foreach ($list as $code => &$country) {
            $country = ['regions' => $this->_getRegions($code)];
        }

        return $list;
    }

    private function _getRegions($country)
    {
        $storeId = $this->storeManager->getStore()->getId();

        $collection = $this->ItemCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => 1]);
        $collection->addFieldToFilter('country_id', ['eq' => $country]);

        $collection->addStoreFilter($storeId, true);

        $regions = [];
        foreach ($collection as $blockModel) {
            $regions[] = $blockModel->getRegionId();
        }
        $regions = array_unique($regions);
        sort($regions);

        $result = [];
        foreach ($regions as $region) {
            $regionModel = $this->regionFactory->create()->load($region);
            if ($regionModel->getId()) {
                $result[$regionModel->getName()] = $region;
            }
        }

        return $result;
    }

    private function _getListCountries()
    {
        $storeId = $this->storeManager->getStore()->getId();

        $collection = $this->ItemCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => 1]);

        $collection->addStoreFilter($storeId, true);

        $countries = [];
        foreach ($collection as $blockModel) {
            $countries[] = $blockModel->getCountryId();
        }
        $countries = array_unique($countries);

        $data = [];
        foreach ($countries as $code) {
            if (!empty($code)) {
                $data[$code] = $this->countryFactory->create()->loadByCode($code)->getName();
            }
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function getScrollWheel()
    {
        return $this->getConfigOption("iwd_storelocator/gm/scrollwheel_zooming");
    }

    /**
     * @return bool
     */
    public function getScaleControl()
    {
        return $this->getConfigOption("iwd_storelocator/gm/scale_control");
    }

    /**
     * @return bool
     */
    public function getMapTypeControl()
    {
        return $this->getConfigOption("iwd_storelocator/gm/type_control");
    }

    /**
     * @return bool
     */
    public function getCmsBlock()
    {
        return $this->getConfigOption("iwd_storelocator/general/cms");
    }
}
