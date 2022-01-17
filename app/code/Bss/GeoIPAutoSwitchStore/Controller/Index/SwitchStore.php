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
 * @category   BSS
 * @package    Bss_GeoIPAutoSwitchStore
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GeoIPAutoSwitchStore\Controller\Index;

use Magento\Framework\App\Action\Context;

class SwitchStore extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\Data
     */
    public $moduleHelper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $resultJsonFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    public $countryFactory;

    /**
     * @var Context
     */
    public $context;

    /**
     * @var \Magento\Framework\Url\Encoder
     */
    public $urlEncoder;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $session;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData
     */
    private $geoIpHelper;

    /**
     * SwitchStore constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Bss\GeoIPAutoSwitchStore\Helper\Data $moduleHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\Url\Encoder $urlEncoder
     * @param \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData $geoIpHelper
     * @param \Magento\Framework\Session\Generic $session
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Bss\GeoIPAutoSwitchStore\Helper\Data $moduleHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Url\Encoder $urlEncoder,
        \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData $geoIpHelper,
        \Magento\Framework\Session\Generic $session
    ) {
        $this->storeManager = $storeManager;
        $this->moduleHelper = $moduleHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->context = $context;
        $this->countryFactory = $countryFactory;
        $this->urlEncoder = $urlEncoder;
        $this->session = $session;
        $this->geoIpHelper = $geoIpHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $currentUrl = $this->getRequest()->getPost('current_url');
            $currentPath = $this->getRequest()->getPost('current_path');

            $status = false;
            $countryCode = $this->session->getCountryCode();
            if (!$countryCode) {
                $countryCode = $this->moduleHelper->getCountryCodeFromIp();
            }
            $openPopup = $this->session->getOpenPopup();
            $dataCountry = [];
            $sysMessage = $countryCode;
            if ($countryCode && !$openPopup) {
                try {
                    $countryName = $this->getCountryName($countryCode);
                    $storeCountry = $this->getStoreByCountryCode($countryCode);

                    if ($storeCountry && $storeCountry->getId() !== $this->storeManager->getStore()->getId()) {
                        $returnData = $this->setData($currentPath, $currentUrl, $storeCountry, $countryName);
                        $status = $returnData['status'];
                        $dataCountry = $returnData['data'];
                    }

                } catch (\Exception $e) {
                    $sysMessage = $e->getMessage();
                }

            } else {
                $dataCountry = [];
            }

            $dataResult = [
                'status' => $status,
                'message' => $sysMessage,
                'data' => $dataCountry
            ];

            $result->setData($dataResult);
        }
        return $result;
    }

    /**
     * @param string $currentPath
     * @param string $currentUrl
     * @param \Magento\Store\Model\Store $storeCountry
     * @param string $countryName
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function setData($currentPath, $currentUrl, $storeCountry, $countryName)
    {
        $returnResult['status'] = false;
        $returnResult['data'] = [];

        //Get Redirects Scope to Redirects
        $redirectScope = $this->moduleHelper->getRedirectsScope($this->storeManager->getStore()->getId());

        $currentStoreView = ',' . $storeCountry->getId() . ',';
        if ($redirectScope == 'website') {
            $storeViewIdScope = ',' . $this->getStoreIdFromWebsite();
            if (strpos($storeViewIdScope, $currentStoreView) === false) {
                return $returnResult;
            }
        }

        if ($redirectScope == 'store') {
            $storeViewIdScope = ',' . $this->getStoreIdFromGroup();
            if (strpos($storeViewIdScope, $currentStoreView) === false) {
                return $returnResult;
            }
        }

        $currentPath = $this->geoIpHelper->getCurrentPath(
            $currentPath,
            $currentUrl,
            $this->storeManager->getStore()->getId(),
            $storeCountry->getId()
        );

        $currentUrl = $storeCountry->getBaseUrl() . $currentPath;

        $baseUrl = $this->moduleHelper->getBaseUrl();
        $dataPost = $this->moduleHelper->getTargetStorePostData($storeCountry, $currentUrl);
        $message = $this->moduleHelper->getPopupMessage($storeCountry->getId());
        $button = $this->moduleHelper->getPopupButton($storeCountry->getId());
        $returnResult['data'] = [
            'base_url' => $baseUrl,
            'data_post' => $dataPost,
            'message' => $message,
            'country_name' => $countryName,
            'button' => $button,
            'store_name' => $storeCountry->getName()
        ];
        $returnResult['status'] = true;
        $this->session->setOpenPopup(true);
        return $returnResult;
    }

    /**
     * @param string $countryCode
     * @return string
     */
    protected function getCountryName($countryCode)
    {
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    /**
     * @param string $countryCode
     * @return bool|\Magento\Store\Api\Data\StoreInterface
     */
    protected function getStoreByCountryCode($countryCode)
    {
        //Get All Store view
        $stores = $this->storeManager->getStores(false);

        foreach ($stores as $store) {
            $countryStore = $this->moduleHelper->getCountries($store->getId());

            if (strpos($countryStore, $countryCode) !== false && $store->isActive()) {
                return $store;
            }
        }
        return false;
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
                $storeViewId = $storeViewId . $myStore->getId() . ',';
            }
        }
        return $storeViewId;
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
                $storeViewId = $storeViewId . $myStore->getId() . ',';
            }
        }
        return $storeViewId;
    }
}
