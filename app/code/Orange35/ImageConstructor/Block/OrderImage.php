<?php

namespace Orange35\ImageConstructor\Block;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order\Item;
use Orange35\ImageConstructor\Helper\OrderImage as OrderImageHelper;

class OrderImage extends Template
{
    private $helper;
    protected $_template = 'Orange35_ImageConstructor::order/items/image.phtml';

    public function __construct(Template\Context $context, OrderImageHelper $helper, array $data = [])
    {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    public function getImage(Item $item)
    {
        $this->setData($this->helper->setItem($item)->getThumbnail());
        return $this;
    }
}
