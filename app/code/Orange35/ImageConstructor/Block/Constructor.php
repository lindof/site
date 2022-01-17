<?php

namespace Orange35\ImageConstructor\Block;

use Magento\Framework\View\Element\Template\Context;
use Orange35\ImageConstructor\Helper\ConfigHelper;
use Orange35\ImageConstructor\Helper\Image as ImageHelper;
use LogicException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option\Value;

class Constructor extends Template
{
    /** @var Product */
    protected $_product;

    /** @var Registry */
    protected $_registry;

    /** @var ImageHelper */
    private $imageHeper;
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        ConfigHelper $configHelper,
        ImageHelper $imageHeper,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->configHelper = $configHelper;
        $this->imageHeper = $imageHeper;

        parent::__construct($context, $data);
    }

    public function getOptionsJson()
    {
        $options = [
            'productId'           => $this->getProduct()->getId(),
            'mergeUrl'            => $this->getUrl('image_constructor/index/RenderImage'),
            'singleImageStrategy' => $this->configHelper->isEnabledSingleImageStrategy(),
        ];
        foreach ($this->getProduct()->getOptions() as $option) {
            if (!is_array($option->getValues())) {
                continue;
            }
            /** @var Value $value */
            foreach ($option->getValues() as $value) {
                if (!($image = $value->getLayer())) {
                    continue;
                }

                $layer = [
                    'valueId'         => $value->getOptionTypeId(),
                    'sortOrder'       => $value->getSortOrder(),
                    'sortOrderOption' => $option->getSortOrder(),
                ];

                $map = $this->configHelper->getLayerImageMap();

                foreach ($map as $property => $imageId) {
                    $this->imageHeper->init($this->getProduct(), $imageId);
                    $layer[$property] = $this->imageHeper->getLayerImage($image, 'src');
                }

                $options['layers'][] = $layer;
            }
        }
        return json_encode($options, JSON_PRETTY_PRINT);
    }

    /**
     * @return Product
     * @throws LogicException
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if ($this->_registry->registry('current_product')) {
                $this->_product = $this->_registry->registry('current_product');
            } else {
                throw new LogicException('Product is not defined');
            }
        }
        return $this->_product;
    }
}
