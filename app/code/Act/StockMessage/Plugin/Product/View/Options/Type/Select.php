<?php

namespace Act\StockMessage\Plugin\Product\View\Options\Type;


use Magento\Catalog\Block\Product\View\Options\Type\Select as TypeSelect;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\OptionFeatures\Helper\Data as Helper;
use MageWorx\OptionFeatures\Model\Price as AdvancedPricingPrice;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionBase\Helper\Price as BasePriceHelper;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;
use MageWorx\OptionBase\Helper\System as SystemHelper;
use MageWorx\OptionBase\Model\HiddenDependents as HiddenDependentsModel;
use MageWorx\OptionSwatches\Model\ResourceModel\Catalog\ProductUrls;

use Magento\CatalogInventory\Api\StockRegistryInterface;

class Select extends \MageWorx\OptionSwatches\Plugin\Product\View\Options\Type\Select {
    protected $stockRegistry;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var BasePriceHelper
     */
    protected $basePriceHelper;

    /**
     * @var AdvancedPricingPrice
     */
    protected $advancedPricingPrice;

    /**
     * @var SystemHelper
     */
    protected $systemHelper;

    /**
     * @var HiddenDependentsModel
     */
    protected $hiddenDependentsModel;

    /**
     * @var ProductUrls
     */
    protected $productUrls;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    protected $_stockItemRepository;

    private $productRepository;

    /**
     * Select constructor.
     * @param PricingHelper $pricingHelper
     * @param Helper $helper
     * @param BaseHelper $baseHelper
     * @param BasePriceHelper $basePriceHelper
     * @param AdvancedPricingPrice $advancedPricingPrice
     * @param State $state
     * @param SystemHelper $systemHelper
     * @param HiddenDependentsModel $hiddenDependentsModel
     * @param ProductUrls $productUrls
     * @param StoreManagerInterface $storeManager
     * @param StockRegistryInterface $storeManager
     */
    public function __construct(
        PricingHelper $pricingHelper,
        Helper $helper,
        BaseHelper $baseHelper,
        BasePriceHelper $basePriceHelper,
        AdvancedPricingPrice $advancedPricingPrice,
        State $state,
        SystemHelper $systemHelper,
        HiddenDependentsModel $hiddenDependentsModel,
        ProductUrls $productUrls,
        StoreManagerInterface $storeManager,
        StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->pricingHelper         = $pricingHelper;
        $this->helper                = $helper;
        $this->baseHelper            = $baseHelper;
        $this->basePriceHelper       = $basePriceHelper;
        $this->advancedPricingPrice  = $advancedPricingPrice;
        $this->state                 = $state;
        $this->systemHelper          = $systemHelper;
        $this->hiddenDependentsModel = $hiddenDependentsModel;
        $this->productUrls           = $productUrls;
        $this->storeManager          = $storeManager;
        $this->stockRegistry = $stockRegistry;
        $this->_stockItemRepository = $stockItemRepository;
        $this->productRepository = $productRepository;


        parent::__construct(
            $pricingHelper,
            $helper,
            $baseHelper,
            $basePriceHelper,
            $advancedPricingPrice,
            $state,
            $systemHelper,
            $hiddenDependentsModel,
            $productUrls,
            $storeManager
        );
    }


    /**
     * @param TypeSelect $subject
     * @param \Closure $proceed
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundGetValuesHtml(TypeSelect $subject, \Closure $proceed) {
        $parent = parent::aroundGetValuesHtml($subject, $proceed);

        $option     = $subject->getOption();
        $productSKu = $subject->getProduct()->getSku();
        $renderSwatchOptions = '';
        if (($option->getType() == Option::OPTION_TYPE_DROP_DOWN ||
                $option->getType() == Option::OPTION_TYPE_MULTIPLE) &&
            $this->state->getAreaCode() !== Area::AREA_ADMINHTML &&
            $option->getIsSwatch()
        ) {

            $isHiddenOutOfStockOptions = $this->baseHelper->isHiddenOutOfStockOptions();
            
            /** @var ProductCustomOptionValuesInterface $value */
            foreach ($option->getValues() as $value) {
                if ($value->getManageStock() && $value->getQty() <= 0 && $isHiddenOutOfStockOptions) {
                    $renderSwatchOptions .= "";
                } else {
                    $renderSwatchOptions .= $this->getOptionProductStockHtml(
                        $option,
                        $value
                    );
                }
            }
        }
        $parent .= $renderSwatchOptions;

        // var_dump($renderSwatchOptions);
        // exit(0);

        return $parent;
    }

    /**
     * Get html for visible part of swatch element
     *
     * @param Option $option
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface|\Magento\Catalog\Model\Product\Option\Value $optionValue
     * @return string
     */
    private function getOptionProductStockHtml($option, $optionValue) {
        $type = $optionValue->getBaseImageType() ? $optionValue->getBaseImageType() : 'text';
        $optionValue->getTitle() ? $label = $optionValue->getTitle() : $label = '';
        $store = $option->getProduct()->getStore();
        $value = $this->helper->getThumbImageUrl(
            $optionValue->getBaseImage(),
            Helper::IMAGE_MEDIA_ATTRIBUTE_SWATCH_IMAGE
        );
        if (!$value) {
            $value = $label;
        }


        $showSwatchTitle = $this->helper->isShowSwatchTitle();
        $showSwatchPrice = $this->helper->isShowSwatchPrice();
        $hiddenValues    = $this->hiddenDependentsModel->getHiddenValues($option->getProduct());
        $hiddenOptions   = $this->hiddenDependentsModel->getHiddenOptions($option->getProduct());
        $skuValue        = $optionValue->getSku();
        $stock = 0;
        $html = '';
        /** @var Magento\Catalog\Model\Product\Interceptor $product */
        $product = $option->getProduct();

        if (!$product) {
            return;
        }

        $skuValue        = $optionValue->getSku();

        if (empty($skuValue)) {
            return;
        }

        // Get product from SKU custom options field
        $_product = $this->productRepository->get($skuValue);

        if (!$_product) {
            return;
        }

        // $stockitem = $this->stockRegistry->getStockItem(
        //     $product->getId(),
        //     $product->getStore()->getWebsiteId()
        // );
        // $stock = $stockitem->getQty();

        try {
            $_productStock = $this->getStockItem($_product->getId());
            $stock = $_productStock->getQty();

            $html = '<div style="display:none;" class="act-mageworx-swatch-container"';
            $html .= ' data-option-id="' . $option->getId() . '"' .
                ' data-option-sku="' . $skuValue . '"' .
                ' data-option-product-name="' . $_product->getName() . '"' .
                ' data-option-label="' . $label . '"' .
            ' data-option-stock="' . $stock . '"';
            $html .= '>';

            $html .= '&nbsp;</div>';
        } catch (\Throwable $th) {
            //throw $th;
        }


        return $html;
    }

    /**
     * Get the product stock quantity
     */
    public function getStockItem($productId) {
        return $this->_stockItemRepository->get($productId);
    }    
}
