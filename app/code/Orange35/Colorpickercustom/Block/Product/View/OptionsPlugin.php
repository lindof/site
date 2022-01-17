<?php

namespace Orange35\Colorpickercustom\Block\Product\View;

use Magento\Catalog\Model\Product\Option;
use Orange35\Colorpickercustom\Block\Product\View\Options\Type\Swatch;

class OptionsPlugin
{
    private $swatchRenderer;

    public function __construct(Swatch $swatchRenderer)
    {
        $this->swatchRenderer = $swatchRenderer;
    }

    public function aroundGetOptionHtml(
        \Magento\Catalog\Block\Product\View\Options $subject,
        callable $proceed,
        \Magento\Catalog\Model\Product\Option $option
    ) {
        if ($option->getIsColorpicker()) {
            switch ($option->getType()) {
                case Option::OPTION_TYPE_MULTIPLE:
                    // break was intentionally omitted
                case Option::OPTION_TYPE_CHECKBOX:
                    $option->setType(Option::OPTION_TYPE_MULTIPLE);
                    break;
                case Option::OPTION_TYPE_DROP_DOWN:
                    // break was intentionally omitted
                case Option::OPTION_TYPE_RADIO:
                    $option->setType(Option::OPTION_TYPE_DROP_DOWN);
                    break;
            }
        }

        if ($option->getIsColorpicker()) {
            $this->swatchRenderer->setProduct($subject->getProduct())->setOption($option);
            $result = $this->swatchRenderer->getValuesHtml();
        } else {
            $result = $proceed($option);
        }

        return $result;
    }
}
