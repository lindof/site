<?php
/**
 * @category Cminds
 * @package  Cminds_Marketplace
 * @author   Cminds Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Controller\Checkout;

use Cminds\Marketplace\Helper\Supplier;
use Cminds\Marketplace\Model\Methods as MethodsModel;
use Cminds\Marketplace\Model\RatesFactory;
use Cminds\Marketplace\Model\ResourceModel\Rates as RatesResource;
use Cminds\Marketplace\Model\Shipping\Carrier\Marketplace\Shipping;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\CustomerFactory as CustomerFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\ResourceModel\Country as CountryResource;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region as RegionResource;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Cminds\Marketplace\Helper\Data as MarketplaceHelper;
use Cminds\Supplierfrontendproductuploader\Helper\Price;

/**
 * Class GetProductsByVendors
 * @package Cminds\Marketplace\Controller\Checkout
 */
class GetProductsByVendors extends Action
{
    protected $productFactory;
    protected $productResource;
    protected $customerFactory;
    protected $store;
    protected $methods;
    protected $carrierModel;
    protected $supplierHelper;
    protected $session;
    protected $jsonResultFactory;
    protected $marketplaceHelper;
    protected $priceHelper;
    private $ratesFactory;
    private $ratesResource;
    private $json;
    private $regionFactory;
    private $regionResource;
    private $countryFactory;
    private $countryResource;

    /**
     * GetProductsByVendors constructor.
     *
     * @param Context $context
     * @param ProductFactory $productFactory
     * @param ProductResource $productResource
     * @param CustomerFactory $customerFactory
     * @param StoreManagerInterface $store
     * @param MethodsModel $methodsModel
     * @param Shipping $carrierModel
     * @param Supplier $supplierHelper
     * @param Session $session
     * @param JsonFactory $jsonFactory
     * @param MarketplaceHelper $marketplaceHelper
     * @param Price $price
     * @param RatesFactory $ratesFactory
     * @param RatesResource $ratesResource
     * @param Json $json
     * @param RegionFactory $regionFactory
     * @param RegionResource $regionResource
     * @param CountryFactory $countryFactory
     * @param CountryResource $countryResource
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        ProductResource $productResource,
        CustomerFactory $customerFactory,
        StoreManagerInterface $store,
        MethodsModel $methodsModel,
        Shipping $carrierModel,
        Supplier $supplierHelper,
        Session $session,
        JsonFactory $jsonFactory,
        MarketplaceHelper $marketplaceHelper,
        Price $price,
        RatesFactory $ratesFactory,
        RatesResource $ratesResource,
        Json $json,
        RegionFactory $regionFactory,
        RegionResource $regionResource,
        CountryFactory $countryFactory,
        CountryResource $countryResource
    ) {
        parent::__construct($context);

        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->customerFactory = $customerFactory;
        $this->store = $store;
        $this->methods = $methodsModel;
        $this->carrierModel = $carrierModel;
        $this->supplierHelper = $supplierHelper;
        $this->session = $session;
        $this->jsonResultFactory = $jsonFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->priceHelper = $price;
        $this->ratesFactory = $ratesFactory;
        $this->ratesResource = $ratesResource;
        $this->json = $json;
        $this->regionFactory = $regionFactory;
        $this->regionResource = $regionResource;
        $this->countryFactory = $countryFactory;
        $this->countryResource = $countryResource;
    }

    /**
     * GetProductsByVendors controller
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $json = $this->getRequest()->getParams();
        $items = json_decode($json['json'], true);
        $shippingAddress = $this->json->unserialize($json['shippingAddress'] ?? []);
        $productsBySuppliers = [];
        $currencySymbol = $this->priceHelper->getCurrentCurrencySymbol();

        foreach ($items as $item) {
            $product = $this->productFactory->create();
            $this->productResource->load($product, $item['product']['entity_id']);
            $product->setQty($item['qty'] ?? 1);
            $product->setCartPrice($item['price_incl_tax']);
            $productData = $product->getData();
            if (isset($productData['thumbnail'])) {
                $productData['productImage'] = $this->store->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                    . 'catalog/product' . $productData['thumbnail'];
            } else {
                $productData['productImage'] = $this->store->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_STATIC)
                    . 'frontend/Magento/luma/en_US/Magento_Catalog/'
                    . 'images/product/placeholder/image.jpg';
            }

            if ($product->getCreatorId() === null) {
                $productsBySuppliers[0][] = $productData;
            } else {
                $productsBySuppliers[$product->getCreatorId()][] = $productData;
            }
        }

        $output = [];
        $nonSupplierPrice = 0;
        foreach ($productsBySuppliers as $supplierId => $products) {
            $methodsArr = [];
            $methodsNoSupArr = [];
            $selected = $this->session->getMarketplaceShippingMethods();

            if ($supplierId == 0 || $supplierId === 'non_supplier') {

                $nonSupplierPrice = $this->carrierModel->getSupplierShippingPriceNonSupplier();
                $selected['non_supplier'] = [
                    'method_id' => null,
                    'price' => $nonSupplierPrice,
                ];

                $price_total = $this->supplierHelper
                    ->calculateTotalShippingPrice($selected);
                $this->session
                    ->setMarketplaceShippingMethods($selected);
                $this->session
                    ->setMarketplaceShippingPrice($price_total);
                $methodNoSupData['id'] = "0";
                $methodNoSupData['supplier_id'] = "0";
                $methodNoSupData['name'] = 'shipping price for non-supplier products';
                $methodNoSupData['price'] = $nonSupplierPrice;
                $methodNoSupData['checked'] = true;
                $convertedPrice = $this->priceHelper->convertToCurrentCurrencyPrice((double)$nonSupplierPrice);
                $methodNoSupData['converted_price'] = $currencySymbol . $convertedPrice;
                $methodsNoSupArr[] = $methodNoSupData;
            } else {
                $supplierName = $this->supplierHelper->getSupplierNameForShippingMethods($supplierId);
                //$shippingTerms = $this->supplierHelper->getShippingTermsForSupplier($supplierId);
                $shippingTerms = $this->supplierHelper->isSupplierHasShippingTerms($supplierId);
                $methods = $this->supplierHelper->getShippingMethods($supplierId, $products);
                foreach ($methods as $method) {
                    $methodData = $method->getData();

                    if (isset($selected[$supplierId]) && $selected[$supplierId]['method_id'] === $methodData['id']) {
                        $methodData['checked'] = true;
                    } else {
                        $methodData['checked'] = false;
                    }

                    $totalItems = [];
                    foreach ($products as $product) {
                        if ($product['type_id'] != 'configurable') {
                            $qty = $product['qty'];

                            if ($creatorId = $product['creator_id']) {
                                $totalItems[$creatorId][] = [
                                    $product['weight'] ?? '',
                                    $product['cart_price'],
                                    $qty,
                                ];
                            }
                        }
                    }

                    if ($methodData['table_rate_available']) {
                        $tableRateFee  = $methodData['table_rate_fee'];
                        $rateModel = $this->ratesFactory->create();
                        $this->ratesResource->load($rateModel, $methodData['id'], 'method_id');

                        $price = $rateModel->getId() ? $this->calculateTableRate(
                            $totalItems, $shippingAddress, $rateModel, $methodData['table_rate_condition']
                        ) : null;

                        if (is_null($price)) {
                            continue;
                        }

                        $methodData['price'] = $price + $tableRateFee;
                    }

                    if (isset($methodData['price'])) {
                        $convertedPrice = $this->priceHelper->convertToCurrentCurrencyPrice((double)$methodData['price']);
                        $methodData['converted_price'] = $currencySymbol . $convertedPrice;
                    }

                    $methodsArr[] = $methodData;
                }
            }

            if ($supplierId != 0 && !empty($methodsArr)) {
                if ($this->marketplaceHelper->shippingMethodsEnabled()) {
                    $linkLabel = __('Shipping terms and conditions of ') . $supplierName;
                    $output[] = [
                        'supplier_id' => $supplierId,
                        'supplier_name' => $supplierName,
                        'shipping_terms' => $shippingTerms,
                        'products' => $products,
                        'methods' => $methodsArr,
                        'link' => $linkLabel,
                        'url' => '/marketplace/shippingterms?id=' . $supplierId
                    ];
                } else {
                    $output[] = [
                        'supplier_id' => $supplierId,
                        'supplier_name' => $supplierName,
                        'shipping_terms' => false,
                        'products' => $products,
                        'methods' => $methodsArr
                    ];
                }
            } else {
                $output[] = [
                    'supplier_id' => $supplierId,
                    'supplier_name' => 'Non-Supplier',
                    'shipping_terms' => false,
                    'products' => $products,
                    'methods' => $methodsNoSupArr,
                ];
            }
        }

        return $result->setData($output);
    }

    /**
     * Calculate Table Rate Fee
     *
     * @param $items
     * @param $address
     * @param $rateModel
     * @param $type
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function calculateTableRate($items, $address, $rateModel, $type)
    {
        return $this->calculateTableFee($items, $address, $rateModel, $type);
    }

    /**
     * Calculate Table Rate Fee
     *
     * @param $items
     * @param $address
     * @param $model
     * @param $type
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function calculateTableFee($items, $address, $model, $type)
    {
        $country = $address['countryId'] ?? null;
        if (is_null($country)) {
            return null;
        }

        $region = $address['regionId'] ?? $address['region'] ?? null;
        $regionModel = $this->regionFactory->create();
        if (is_numeric($region)) {
            $this->regionResource->load($regionModel, $region);
        } else {
            $this->regionResource->loadByName($regionModel, $region, $country);
        }
        $region = $regionModel->getCode();

        /* use iso3 code of country, for example USA (not US) */
        $countryModel = $this->countryFactory->create();
        $this->countryResource->loadByCode($countryModel, $country);
        if (!$countryModel->getId()) {
            return null;
        }
        $country = $countryModel->getId();

        $postcode = $address['postcode'] ?? null;

        $total = 0;
        foreach ($items as $products) {
            foreach ($products as $key => $product) {
                if (isset($product[$type - 1])) {
                    if ($type == 3) {
                        $total += $product[$type - 1];
                    } else {
                        $total += ($product[$type - 1] * $product[2]);
                    }
                }
            }
        }

        switch ($type) {
            case 1:
                return $model->getRateByWeight(
                    $country,
                    $region,
                    $postcode,
                    $total
                );
            case 2:
                return $model->getRateByPrice(
                    $country,
                    $region,
                    $postcode,
                    $total
                );
            case 3:
                return $model->getRateByQty(
                    $country,
                    $region,
                    $postcode,
                    $total
                );
            default:
                return 0;
        }
    }

}
