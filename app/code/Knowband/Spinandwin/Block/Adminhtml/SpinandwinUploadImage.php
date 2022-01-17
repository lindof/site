<?php

namespace Knowband\Spinandwin\Block\Adminhtml;

use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Catalog\Api\Data\ProductInterface;

class Gallery extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery
{
    /**
     * @var here you set your ui form 
     */
    protected $formName = 'sample_form';

}