<?php

namespace Orange35\Colorpickercustom\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Orange35\Colorpickercustom\Model\Uploader;

class ProductSave implements ObserverInterface
{
    private $uploader;
    private $optionNullables = ['swatch_width', 'swatch_height'];

    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function execute(Observer $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product **/
        $product = $observer->getEvent()->getData('product');
        $productOptions = $product->getOptions() ?: [];
        $newOptions = [];

        /** @var \Magento\Catalog\Model\Product\Option $option */
        foreach ($productOptions as $option) {
            foreach ($this->optionNullables as $field) {
                if (!$option->getData($field)) {
                    $option->setData($field, null);
                }
            }
            $values = $option->getData('values') ?: [];
            $newValues = [];
            foreach ($values as $value) {
                $newValue = ['image' => null];
                if (isset($value['image'])) {
                    $name = $this->uploader->uploadFileAndGetName('image', $value);
                    $newValue['image'] = $name;
                }
                $newValues[] = array_replace_recursive($value, $newValue);
            }
            $option->setData('values', $newValues);
            $newOptions[] = $option;
        }
        $product->setOptions($newOptions);

        return $this;
    }
}
