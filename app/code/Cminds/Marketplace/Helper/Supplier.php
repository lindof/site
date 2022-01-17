<?php
/**
 * @category Cminds
 * @package  Marketplace
 * @author   Cminds Core Team <info@cminds.com>
 */
declare(strict_types=1);

namespace Cminds\Marketplace\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Cminds\Marketplace\Model\ResourceModel\Methods\CollectionFactory;
use Cminds\Marketplace\Model\Methods;
use Magento\Framework\App\Helper\Context;
use Cminds\Marketplace\Model\Shipping\Carrier\Marketplace\Shipping;
use Magento\Quote\Model\Quote\Item;
use Cminds\Marketplace\Model\Config\Source\Shipping\GroupLabel;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;
use Cminds\Marketplace\Helper\Data as MarketplaceHelper;
use Cminds\Supplierfrontendproductuploader\Helper\Price;
use Cminds\Marketplace\Model\Fields;

/**
 * Class Supplier
 * @package Cminds\Marketplace\Helper
 */
class Supplier extends AbstractHelper
{
    /**
     * Collection factory object.
     *
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Carrier object.
     *
     * @var Shipping
     */
    protected $carrier;

    /**
     * Helper object.
     *
     * @var Data
     */
    protected $marketplaceHelper;

    /**
     * Customer factory object.
     *
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerResource
     */
    protected $customerResource;

    /**
     * @var Price
     */
    protected $priceHelper;

    /**
     * Price Currency Object.
     *
     * @var PriceCurrency
     */
    protected $priceCurrency;

    /**
     * Fields object.
     *
     * @var Fields
     */
    protected $fields;

    /**
     * Supplier constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Shipping $carrier
     * @param CustomerFactory $customerFactory
     * @param CustomerResource $customerResource
     * @param Data $marketplaceHelper
     * @param Price $price
     * @param Fields $fields
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Shipping $carrier,
        CustomerFactory $customerFactory,
        CustomerResource $customerResource,
        MarketplaceHelper $marketplaceHelper,
        Price $price,
        Fields $fields
    ) {
        parent::__construct($context);

        $this->collectionFactory = $collectionFactory;
        $this->carrier = $carrier;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->priceHelper = $price;
        $this->fields = $fields;
    }

    /**
     * Return shipping methods which belongs to supplier with provided id.
     *
     * @param int    $supplierId Supplier id.
     * @param Item[] $cartItems  Cart items array.
     *
     * @return Methods[]
     */
    public function getShippingMethods($supplierId, $cartItems)
    {
        $supplierRates = [];

        $collection = $this->collectionFactory->create();
        $collection->addFilter('supplier_id', $supplierId);

        foreach ($collection as $method) {
            $price = $this->carrier->getPrice($method, $cartItems);
            //$price = $this->priceHelper->convertToCurrentCurrencyPrice($price);
            $method->setPrice($price);
            $supplierRates[] = $method;
        }

        return $supplierRates;
    }

    /**
     * Get total shipping price by array with selected shipping methods
     *
     * @param array $shippingMethods Selected shipping methods data.
     *
     * @return float
     */
    public function calculateTotalShippingPrice($shippingMethods)
    {
        $totalPrice = 0;
        foreach ($shippingMethods as $method) {
            $totalPrice += $method['price'];
        }

        return $totalPrice;
    }

    /**
     * Get supplier name for supplier shipping methods.
     *
     * @param $supplierId
     *
     * @return string
     */
    public function getSupplierNameForShippingMethods($supplierId) {
        $customer = $this->customerFactory->create();
        $this->customerResource->load($customer, $supplierId);
        $supplierName = $customer->getName();

        $checkConfig = (int)$this->carrier->getConfigData('shipping_methods_group_label') ===
            (int)GroupLabel::PROFILE_NAME;

        if ($this->marketplaceHelper->supplierPagesEnabled() &&
            !empty($customer->getSupplierName()) &&
            $checkConfig
        ) {
            $supplierName = $customer->getSupplierName();
        }

        return $supplierName;
    }

    /**
     * @param $supplierId
     * @return string
     */
    public function getShippingTermsForSupplier($supplierId)
    {
        $customer = $this->customerFactory->create();
        $this->customerResource->load($customer, $supplierId);
        $retName['terms'] = '';
        $retName['supplier_name'] = $customer->getSupplierName();
        if (!$customer->getCustomFieldsValues()) {
            return $retName;
        }

        $name = 'shipping_terms';

        $dbValues = unserialize($customer->getCustomFieldsValues());
        foreach ($dbValues AS $value) {
            $v = $this->fields->load($value['name'], 'name');

            if (isset($v)) {
                if ($value['name'] == $name) {
                    $retName['terms'] = $value['value'];
                }
            }
        }

        return $retName;
    }

    /**
     * @param $supplierId
     * @return bool
     */
    public function isSupplierHasShippingTerms($supplierId)
    {
        $customer = $this->customerFactory->create();
        $this->customerResource->load($customer, $supplierId);
        $ret = false;
        if (!$customer->getCustomFieldsValues()) {
            return $ret;
        }

        $name = 'shipping_terms';

        $dbValues = unserialize($customer->getCustomFieldsValues());
        foreach ($dbValues AS $value) {
            $v = $this->fields->load($value['name'], 'name');

            if (isset($v)) {
                if ($value['name'] == $name) {
                    $ret = true;
                }
            }
        }

        return $ret;
    }
}
