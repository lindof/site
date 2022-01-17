<?php

namespace Cminds\Supplierfrontendproductuploader\Block\Product;

use Magento\Framework\View\Element\Template;

class DisableAttribute extends Template
{
    public function getAction()
    {
        return $this->getUrl('supplier/product/disableattributepost');
    }
}
