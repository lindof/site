<?php
/** @noinspection PhpUnusedParameterInspection */

namespace Orange35\ImageConstructor\Plugin\CatalogImportExport\Model\Import\Product;

use Orange35\CatalogImportExport\Model\Import\Product\Option as ProductOption;
use Orange35\CatalogImportExport\Model\Import\Product\Option\FileInterface;
use Orange35\ImageConstructor\Helper\Image;

class Option
{
    const COLUMN_ROW_LAYER = '_custom_option_row_layer';

    private $imageHelper;
    private $file;

    public function __construct(Image $imageHelper, FileInterface $file)
    {
        $this->imageHelper = $imageHelper;
        $this->file = $file;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function afterProcessOptionRow(ProductOption $subject, $result, $name, $optionRow)
    {
        if (array_key_exists('layer', $optionRow)) {
            $result[self::COLUMN_ROW_LAYER] = $optionRow['layer'];
        }
        return $result;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function afterGetSpecificTypeData(
        ProductOption $option,
        $result,
        array $rowData,
        $optionTypeId,
        $defaultStore = true
    ) {
        if ($defaultStore) {
            if (array_key_exists(self::COLUMN_ROW_LAYER, $rowData)) {
                $result['value']['layer'] = $this->file->import(
                    $rowData,
                    self::COLUMN_ROW_LAYER,
                    $this->imageHelper->getImportPath(),
                    $this->imageHelper->getBasePath()
                );
            }
        }
        return $result;
    }
}
