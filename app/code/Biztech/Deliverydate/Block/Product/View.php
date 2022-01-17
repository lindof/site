<?php

namespace Biztech\Deliverydate\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;

class View extends \Magento\Catalog\Block\Product\View
{

    /**
     * @var Biztech\Deliverydate\Helper/Data;
     */
    public $deliveryDateHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context              $context
     * @param \Magento\Framework\Url\EncoderInterface             $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface            $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils               $string
     * @param \Magento\Catalog\Helper\Product                     $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface           $localeFormat
     * @param \Magento\Customer\Model\Session                     $customerSession
     * @param ProductRepositoryInterface                          $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface   $priceCurrency
     * @param \Biztech\Deliverydate\Helper\Data                   $helper
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Biztech\Deliverydate\Helper\Data $helper,
        array $data = []
    ) {
        $this->deliveryDateHelper = $helper;
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    public function getDeliveryDateConfig()
    {
        return $this->deliveryDateHelper->getProductLevelConfig();
    }

    public function isEnableAtProductPage()
    {
        $isCustomerApplicable = $this->deliveryDateHelper->checkCurrentUserAllowed($this->customerSession->getCustomer()->getGroupId());
        $isAppliableWithCategory = $this->deliveryDateHelper->isAppliableWithCategory($this->getProduct());
        return ($isAppliableWithCategory && $isCustomerApplicable && $this->deliveryDateHelper->isEnableAtProductPage() && $this->getProduct()->getTypeId() !== \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL && $this->getProduct()->getTypeId() !== 'downloadable');
    }
}
