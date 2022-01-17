<?php

namespace Orange35\ImageConstructor\Plugin\Sales\Block\Adminhtml\Order\View\Items\Renderer;

use Orange35\ImageConstructor\Block\OrderImage;

class DefaultRenderer
{
    protected $block;

    public function __construct(OrderImage $block)
    {
        $this->block = $block;
    }

    public function afterGetColumnHtml(
        \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer $renderer,
        $html,
        \Magento\Framework\DataObject $item,
        $column
    ) {
        if ('product' === $column) {
            $html = $this->block->getImage($item)->toHtml() . $html;
        }
        return $html;
    }
}
