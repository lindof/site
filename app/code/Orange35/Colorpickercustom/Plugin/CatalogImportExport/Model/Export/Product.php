<?php
/** @noinspection PhpUnusedParameterInspection */

namespace Orange35\Colorpickercustom\Plugin\CatalogImportExport\Model\Export;

class Product
{
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
    public function aroundGetCustomOptionAdditionalFields(
        \Orange35\CatalogImportExport\Model\Export\Product $product,
        callable $proceed,
        \Magento\Catalog\Model\Product\Option $option
    ) {
        $result = $proceed($option);
        $result['tooltip'] = $option->getData('tooltip');
        $result['is_colorpicker'] = $option->getData('is_colorpicker');
        $result['swatch_width'] = $option->getData('swatch_width');
        $result['swatch_height'] = $option->getData('swatch_height');
        return $result;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
    public function aroundGetCustomOptionValueAdditionalFields(
        \Orange35\CatalogImportExport\Model\Export\Product $product,
        callable $proceed,
        \Magento\Catalog\Model\Product\Option\Value $value
    ) {
        $result = $proceed($value);
        $result['color'] = $value->getData('color');
        $result['image'] = $value->getData('image');
        return $result;
    }
}
