<?php

namespace Orange35\Colorpickercustom\Block\Product\View;

use Magento\Framework\View\Element\Template;
use Orange35\Colorpickercustom\Helper\OptionHelper;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use LogicException;

class Options extends Template
{
    private $product;
    private $registry;
    private $optionHelper;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        OptionHelper $optionHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->optionHelper = $optionHelper;
        $this->registry = $registry;
    }

    public function getOptionHelper()
    {
        return $this->optionHelper;
    }

    /**
     * Retrieve product object
     *
     * @return Product
     * @throws LogicException
     */
    public function getProduct()
    {
        if (!$this->product) {
            if ($this->registry->registry('current_product')) {
                $this->product = $this->registry->registry('current_product');
            } else {
                throw new LogicException('Product is not defined');
            }
        }
        return $this->product;
    }

    public function hasSwatches()
    {
        return $this->getProduct()->getProductOptionsCollection()
            ->clear()
            ->addFieldToFilter('is_colorpicker', 1)
            ->addValuesToResult()
            ->load()
            ->count();
    }

    public function getColorPickerOptions()
    {
        $options = [];
        foreach ($this->getProduct()->getOptions() as $option) {
            if ($this->optionHelper->isColorPicker($option)) {
                $options[] = $option;
            }
        }
        return $options;
    }
}
