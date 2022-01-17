<?php

namespace Orange35\Colorpickercustom\Block\Product\Renderer\Listing;

use Magento\Catalog\Model\Product\Option;
use Magento\Framework\View\Element\Template;
use Orange35\Colorpickercustom\Helper\ConfigHelper;
use Orange35\Colorpickercustom\Helper\OptionHelper;
use Orange35\Colorpickercustom\Model\Config\Source\CategoryMode;

class Swatches extends Template
{
    private $optionHelper;
    private $configHelper;
    private $optionsCache = [];

    public function __construct(
        Template\Context $context,
        OptionHelper $optionHelper,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->optionHelper = $optionHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->getData('product');
    }

    /**
     * @return Option[]
     */
    public function getColorPickerOptions()
    {
        if (!($product = $this->getProduct()) || !($options = $product->getOptions())) {
            return [];
        }
        $productId = $product->getId();
        if (array_key_exists($productId, $this->optionsCache)) {
            return $this->optionsCache[$productId];
        }
        $mode = $this->configHelper->getCategoryMode();
        /** @var Option $option */
        $this->optionsCache[$productId] = [];
        foreach ($options as $option) {
            if (!$this->optionHelper->isColorPicker($option)) {
                continue;
            }
            if ($mode == CategoryMode::REQUIRED && !$option->getIsRequire()) {
                continue;
            }
            if ($mode == CategoryMode::CUSTOM && !$this->optionHelper->isShownInList($option)) {
                continue;
            }
            $this->optionsCache[$productId][] = $option;
        }
        return $this->optionsCache[$productId];
    }

    public function getOptionHelper()
    {
        return $this->optionHelper;
    }

    // phpcs:ignore MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected function _toHtml()
    {
        if (CategoryMode::NONE == $this->configHelper->getCategoryMode()) {
            return '';
        }
        return parent::_toHtml();
    }
}
