<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_SeoSuite
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GeoIPAutoSwitchStore\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Action\Context;

class Redirect implements ObserverInterface
{
    const FLAG_NO_DISPATCH = 'no-dispatch';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData
     */
    private $geoIpHelper;

    /**
     * @var int
     */
    protected $currentStoreId = 0;

    /**
     * Redirect constructor.
     * @param \Bss\GeoIPAutoSwitchStore\Helper\Data $dataHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData $geoIpHelper
     * @param Context $context
     */
    public function __construct(
        \Bss\GeoIPAutoSwitchStore\Helper\Data $dataHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData $geoIpHelper,
        Context $context
    ) {
        $this->request = $request;
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->url = $context->getUrl();
        $this->geoIpHelper = $geoIpHelper;
        $this->actionFlag = $context->getActionFlag();
    }

    /**
     * {@inheritdoc}
     */
    protected function getStoreId()
    {
        if ($this->currentStoreId == 0) {
            $this->currentStoreId = $this->storeManager->getStore()->getId();
        }
        return $this->currentStoreId;
    }

    /**
     * {@inheritdoc}
     */
    protected function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|bool|void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Are we enabled in config and not disabled by module manager?
        if (!$this->dataHelper->getEnableModule()) {
            return $this; // do nothing
        }

        //Get IP for Tester from Param URL
        $ipForTester = $this->request->getParam('ipTester');
        //Get IP Customer.
        $ipCustomer = $this->dataHelper->getIpCustomer($ipForTester);

        // If customer IP on restricted list, finish
        // Get Ip not Redirects (Array), if this IP is current Customer IP then not Redirects
        if ($this->geoIpHelper->isRestrictedIP($ipCustomer)) {
            return $this;
        }

        // This is Status Block config
        $enableBlackList = $this->dataHelper->getEnableBlackList($this->getStoreId());
        //Get Country List, if Current Country of Customer on this Country list then Not redirects

        if ($enableBlackList && $this->geoIpHelper->isIpBlocked($ipCustomer)) {
            $this->redirectToBlockUrl($observer);
        }

        //Get Bot as Google Bot, Yahoo..., Not redirects it.
        $userBots = $this->dataHelper->restrictionUserAgent($this->getStoreId());
        $countUserBot = $this->geoIpHelper->countBot($userBots);
        if ($countUserBot > 0) {
            return $this;
        }
        //Get Enable Cookie
        $enableCookie = $this->dataHelper->getEnableCookie($this->getStoreId());

        //Now, handle Redirects With Country Code
        return $this->handleRedirectCountry($ipCustomer, $ipForTester, $enableCookie, $enableBlackList, $observer);
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function redirectToBlockUrl($observer)
    {
        $blockUrl = trim($this->dataHelper->getUrlBlackList($this->getStoreId()));
        if (stripos($blockUrl, 'http') === false) {
            $blockUrl = $this->url->getUrl($blockUrl);
        }

        $currentUrl = $this->storeManager->getStore()->getCurrentUrl(false);
        if (strpos($currentUrl, $blockUrl) === false) {
            $this->actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            $observer->getResponse()->setRedirect($blockUrl);
            return true;
        }
        return false;
    }

    /**
     * @param string $ipCustomer
     * @param string $ipForTester
     * @param string $enableCookie
     * @param string $enableBlackList
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function handleRedirectCountry($ipCustomer, $ipForTester, $enableCookie, $enableBlackList, $observer)
    {
        // If not testing, check if we have country code already (from session or cookie)
        $countryCode = null;

        if (!$ipForTester) {
            $countryCode = $this->geoIpHelper->getSession()->getCountryCode();

            if (!$countryCode) {
                $countryCode = $this->geoIpHelper->getCookieGeoIP()->get();
            }
        }

        // If no country code yet, get it
        if (null == $countryCode) {
            $countryCode = $this->dataHelper->getCountryCodeFromIp($ipCustomer);
        }

        // Now if we have a country code, check if we need to switch
        if ($countryCode) {
            //Save Country Code to Cookie If Config Enable
            $this->saveCountryCookie($countryCode, $enableCookie, $ipForTester);

            // Check for blocked countries
            if ($enableBlackList) {
                if ($this->redirectCountryBlackList($countryCode, $observer)) {
                    return true;
                }
            }
            // Instigate redirect
            $this->redirectFunction($countryCode, $observer);
        }

        return true;
    }

    /**
     * @param string $countryCode
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function redirectCountryBlackList($countryCode, $observer)
    {
        $storeId = $this->getStoreId();
        $countryStoreBlackList = $this->dataHelper->getCountriesBlackList($storeId);
        $countryStoreBlackList = explode(',', $countryStoreBlackList);
        if (in_array($countryCode, $countryStoreBlackList)) {
            return $this->redirectToBlockUrl($observer);
        }
        return false;
    }

    /**
     * @param string $countryCode
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function redirectFunction($countryCode, $observer)
    {
        // Find store for country code and switch
        $stores = $this->storeManager->getStores(false);

        // 1. Get Popup status, if it Enable, stop
        $popupStatus = $this->handlePopupStatus($countryCode);

        // 2. Check Stop Redirects Status
        $stopRedirectStatus = $this->checkStopRedirect($countryCode, $stores);

        // 3. get Is Restriction Url
        $restrictionUrl = $this->geoIpHelper->isRestrictionUrl($this->request);

        //Handle Redirects
        $this->handleRedirectCurrencyWithStopRedirects($stopRedirectStatus, $countryCode, $popupStatus, $observer);

        // If 1 in 3, then STOP it
        if ($popupStatus || $stopRedirectStatus || $restrictionUrl) {
            return $this;
        }

        //Now, Redirects

        //Get Redirects Scope to Redirects
        $redirectScope = $this->dataHelper->getRedirectsScope($this->getStoreId());

        foreach ($stores as $store) {
            $countryStore = $this->dataHelper->getCountries($store->getId());
            if (strpos($countryStore, $countryCode) !== false && $store->isActive()) {
                //Set Redirects if it Correct.
                $makeRedirects = $this->makeRedirects($store, $observer, $redirectScope);
                if (!$makeRedirects) {
                    continue;
                } else {
                    return $this;
                }
            }
        }

        //If Current Store is Final Store, Redirects For Currency
        $this->handleRedirectCurrency($countryCode, $observer, $popupStatus);
        return $this;
    }

    /**
     * @param string $stopRedirectStatus
     * @param string $countryCode
     * @param string $popupStatus
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function handleRedirectCurrencyWithStopRedirects(
        $stopRedirectStatus,
        $countryCode,
        $popupStatus,
        $observer
    ) {
        if ($stopRedirectStatus) {
            if ($this->handleRedirectCurrency($countryCode, $observer, $popupStatus)) {
                return $this;
            }
        }
    }

    /**
     * @param string $countryCode
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function handlePopupStatus($countryCode)
    {
        //Check Config Popup Status
        $popupStatus = $this->dataHelper->getPopupStatus($this->getStoreId());

        if ($popupStatus) {
            //Save Remember Popup if Client click to Switch in Popup
            $isFromPopup = $this->request->getPost('is_from_popup');
            if ($isFromPopup == 'true') {
                $this->geoIpHelper->getCookieGeoIP()->setRememberPopup($countryCode);
                $this->handleCurrencySwitch($countryCode);
            }

            //Get Remember Popup Status
            $rememberPopupStatus = $this->geoIpHelper->getCookieGeoIP()->getRememberPopup();
            $isRememberPopup = $this->dataHelper->isRememberPopupStatus();

            //If is The second time, redirects
            if ($rememberPopupStatus && $isRememberPopup) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $countryCode
     * @param \Magento\Framework\Event\Observer $observer
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function handleRedirectCurrency($countryCode, $observer, $popupStatus)
    {
        //Return False if have Cookie or Popup is Enable
        if (($this->geoIpHelper->getCookieGeoIP()->getCurrency() &&
                $this->geoIpHelper->getCookieGeoIP()->getCurrency() != null) || $popupStatus) {
            return false;
        }

        $this->handleCurrencySwitch($countryCode);

        $originalPath = $this->request->getOriginalPathInfo();
        if (strpos($originalPath, 'stores/store/redirect') !== false) {
            return false;
        }

        if (strpos($originalPath, 'stores/store/switch') !== false) {
            return false;
        }

        $this->actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        $response = $observer->getResponse();

        $url = $this->url->getCurrentUrl();
        $request = $observer->getData('request');
        $urlPath = $request->getOriginalPathInfo();

        $currentPath = $this->geoIpHelper->getCurrentPath($urlPath, $url);
        $redirectUrl = $this->storeManager->getStore()->getBaseUrl().$currentPath;
        //Set Redirects and Return True
        $response->setRedirect($redirectUrl);
        return true;
    }

    /**
     * @param object $store
     * @param \Magento\Framework\Event\Observer $observer
     * @param string $redirectScope
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function makeRedirects($store, $observer, $redirectScope)
    {
        $currentStoreView = ','.$store->getId().',';
        if ($redirectScope == 'website') {
            $storeViewIdScope = ','.$this->getStoreIdFromWebsite();
            if (strpos($storeViewIdScope, $currentStoreView) === false) {
                return false;
            }
        }

        if ($redirectScope == 'store') {
            $storeViewIdScope = ','.$this->getStoreIdFromGroup();
            if (strpos($storeViewIdScope, $currentStoreView) === false) {
                return false;
            }
        }

        //Save Store Cookie
        //$this->dataHelper->getStoreCookieManager()->setStoreCookie($store);

        $url = $this->url->getCurrentUrl();
        $request = $observer->getData('request');
        $urlPath = $request->getOriginalPathInfo();

        $currentPath = $this->geoIpHelper->getCurrentPath($urlPath, $url, $this->getStoreId(), $store->getId());

        $redirectUrl = $store->getBaseUrl().$currentPath;

        $this->actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        $response = $observer->getResponse();
        $storeParam = $this->geoIpHelper->getParamStore();
        $query =
            '___from_store='.$this->getStoreCode()
            .'&uenc='.$this->geoIpHelper->getUrlEncode()->encode($redirectUrl)
            .'&is_geoip=true&'.$storeParam.'='.$store->getCode();
        if ($this->geoIpHelper->getSIDResolver()->getUseSessionInUrl()) {
            // allow customers to stay logged in during store switching
            $sidName = $this->geoIpHelper->getSIDResolver()->getSessionIdQueryParam($this->geoIpHelper->getSession());
            $query .= '&'.$sidName.'='.$this->geoIpHelper->getSession()->getSessionId();
        }

        $finalRedirect = $store->getBaseUrl().'stores/store/switch/?'.$query;
        $response->setRedirect($finalRedirect);
        return true;
    }

    /**
     * @param string $countryCode
     * @param array $stores
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function checkStopRedirect($countryCode, $stores)
    {
        $originalPath = $this->request->getOriginalPathInfo();
        // When  switching store, the following two URLs are invoked, ignore.
        if (strpos($originalPath, 'stores/store/redirect') !== false) {
            return true;
        }
        if (strpos($originalPath, 'stores/store/switch') !== false) {
            return true;
        }

        $currentStoreId = $this->getStoreId();

        $countryStoreId = null;
        $storeFinal = null;
        // Find store for country code and switch
        foreach ($stores as $store) {
            $countryStore = $this->dataHelper->getCountries($store->getId());
            if (strpos($countryStore, $countryCode) !== false && $store->isActive()) {
                $storeFinal = $store;
                $countryStoreId = $store->getId();
                break;
            }
        }
        if ($currentStoreId == $countryStoreId) {
            //Save Cookie for Fisrt Redirect
            $this->geoIpHelper->getCookieGeoIP()->setUrl($storeFinal->getBaseUrl(), 86400);
            return true;
        }

        return $this->checkRedirectWithConfigAllowSwitchStore();
    }

    /**
     * @return bool
     */
    protected function checkRedirectWithConfigAllowSwitchStore()
    {
        // Get Allow Switch Store Config. If Allow Switch Store = NO, then User cant Switch Store.
        // Switch Store ON Website with URL Code and non-URL Code
        $allowSwitchStore = $this->dataHelper->getAllowSwitch($this->getStoreId());

        // If allowing switching, check for reasons not to switch
        if ($allowSwitchStore) {
            // Is this a default URL controlled by store settings
            $currentUrl = $this->url->getCurrentUrl();
            $isDefaultUrl = $this->geoIpHelper->isDefaultUrl($currentUrl);
            $isFirstTimeGeoIP =  $this->geoIpHelper->getCookieGeoIP()->getUrl();
            //If allow Switch Store, return True to Stop Redirect.
            //But it is Default URL, then Redirect it
            if ($isDefaultUrl || !$isFirstTimeGeoIP) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @param string $countryCode
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function handleCurrencySwitch($countryCode)
    {
        $this->geoIpHelper->getCookieGeoIP()->setCurrency('NONE', 86400);
        // check currency
        $currencyCode = $this->geoIpHelper->getCurrencyByCountryCode($countryCode);
        $enableSwitchCurrency = $this->dataHelper->isAutoSwitchCurrency();
        // set currency
        if ($currencyCode && $enableSwitchCurrency) {
            $this->geoIpHelper->getCookieGeoIP()->setCurrency($currencyCode, 86400);
            $this->storeManager->getStore()->setCurrentCurrencyCode($currencyCode);
        }
        return true;
    }

    /**
     * @param string $countryCode
     * @param string $enableCookie
     * @param string $ipForTester
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function saveCountryCookie($countryCode, $enableCookie, $ipForTester)
    {
        $this->geoIpHelper->getSession()->setCountryCode($countryCode);

        // Save Country Code in cookie?
        if ($enableCookie && $ipForTester == null) {
            $timeCookie = (int)$this->dataHelper->getTimeCookie($this->getStoreId());
            $timeCookie = $timeCookie*24*60*60;
            $this->geoIpHelper->getCookieGeoIP()->set($countryCode, $timeCookie);
        }
        return true;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreIdFromWebsite()
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $storesView = $this->geoIpHelper
            ->getGroupFactory()
            ->create()
            ->getCollection()
            ->addFieldToFilter('website_id', $websiteId);
        $storeViewId = '';

        foreach ($storesView as $storeView) {
            foreach ($storeView->getStores() as $myStore) {
                $storeViewId = $storeViewId.$myStore->getId().',';
            }
        }
        return $storeViewId;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreIdFromGroup()
    {
        $groupId = $this->storeManager->getStore()->getGroupId();
        $storesView = $this->geoIpHelper
            ->getGroupFactory()
            ->create()
            ->getCollection()
            ->addFieldToFilter('group_id', $groupId);
        $storeViewId = '';

        foreach ($storesView as $storeView) {
            foreach ($storeView->getStores() as $myStore) {
                $storeViewId = $storeViewId.$myStore->getId().',';
            }
        }
        return $storeViewId;
    }
}
