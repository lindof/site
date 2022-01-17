<?php

namespace Orange35\Colorpickercustom\Plugin\Catalog\Model\Product\Type;

use Orange35\Colorpickercustom\Helper\ConfigHelper;
use Orange35\Colorpickercustom\Helper\OptionHelper;
use Orange35\Colorpickercustom\Model\Config\Source\CategoryMode;

class Simple
{
    private $configHelper;
    private $optionHelper;

    public function __construct(ConfigHelper $configHelper, OptionHelper $optionHelper)
    {
        $this->configHelper = $configHelper;
        $this->optionHelper = $optionHelper;
    }
    //phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
    public function afterIsPossibleBuyFromList(
        \Magento\Catalog\Model\Product\Type\Simple $subject,
        $result,
        \Magento\Catalog\Model\Product $product
    ) {
        if (!$result) {
            switch ($this->configHelper->getCategoryMode()) {
                case CategoryMode::ALL:
                    // break was intentionally omitted
                case CategoryMode::REQUIRED:
                    // all required options are shown so product can be bought from a list
                    return true;
                case CategoryMode::CUSTOM:
                    /** @var \Magento\Catalog\Model\Product\Option $option */
                    foreach ($product->getOptions() ?: [] as $option) {
                        if ($option->getIsRequire() && !$this->optionHelper->isShownInList($option)) {
                            return false;
                        }
                    }
                    return true;
            }
        }
        return $result;
    }
}
