<?php
namespace Mexbs\ApBase\Block\Bundle\Catalog\Product\View\Type;

use Magento\Bundle\Model\Option;

class Bundle extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle{
    public function getOptionHtml(Option $option)
    {
        $optionBlock = $this->getChildBlock($option->getType());
        if (!$optionBlock) {
            return __('There is no defined renderer for "%1" option type.', $option->getType());
        }
        return $optionBlock->setProduct($this->getProduct())->setOption($option)->toHtml();
    }

    public function resetOptions(){
        $this->options = null;
    }
}