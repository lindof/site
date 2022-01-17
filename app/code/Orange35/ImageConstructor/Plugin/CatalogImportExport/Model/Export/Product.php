<?php
/** @noinspection PhpUnusedParameterInspection */

namespace Orange35\ImageConstructor\Plugin\CatalogImportExport\Model\Export;

use Magento\Catalog\Model\Product\Option\Value;

class Product
{
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
    public function aroundGetCustomOptionValueAdditionalFields(
        \Orange35\CatalogImportExport\Model\Export\Product $product,
        callable $proceed,
        Value $value
    ) {
        $result = $proceed($value);
        $result['layer'] = $value->getData('layer');
        return $result;
    }
}
