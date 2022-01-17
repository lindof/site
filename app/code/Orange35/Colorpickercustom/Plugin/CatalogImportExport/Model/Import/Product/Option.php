<?php
/** @noinspection PhpUnusedParameterInspection */

namespace Orange35\Colorpickercustom\Plugin\CatalogImportExport\Model\Import\Product;

use Orange35\CatalogImportExport\Model\Import\Product\Option as ImportProductOption;
use Orange35\CatalogImportExport\Model\Import\Product\Option\FileInterface;
use Orange35\Colorpickercustom\Helper\Image as ImageHelper;

class Option
{
    const COLUMN_COLOR_PICKER  = '_custom_option_is_colorpicker';
    const COLUMN_SWATCH_WIDTH  = '_custom_option_swatch_width';
    const COLUMN_SWATCH_HEIGHT = '_custom_option_swatch_height';
    const COLUMN_TOOLTIP       = '_custom_option_tooltip';
    const COLUMN_ROW_COLOR     = '_custom_option_row_color';
    const COLUMN_ROW_IMAGE     = '_custom_option_row_image';

    private $imageHelper;
    private $file;
    private $map = [
        self::COLUMN_COLOR_PICKER  => 'is_colorpicker',
        self::COLUMN_SWATCH_WIDTH  => 'swatch_width',
        self::COLUMN_SWATCH_HEIGHT => 'swatch_height',
        self::COLUMN_TOOLTIP       => 'tooltip',
    ];
    private $rowMap = [
        self::COLUMN_ROW_COLOR => 'color',
        self::COLUMN_ROW_IMAGE => 'image',
    ];

    public function __construct(ImageHelper $imageHelper, FileInterface $file)
    {
        $this->imageHelper = $imageHelper;
        $this->file = $file;
    }

    /**
     * Copy all module fields to $optionRow for further processing
     * @param ImportProductOption $subject
     * @param $result
     * @param $name
     * @param $optionRow
     * @return mixed
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function afterProcessOptionRow(ImportProductOption $subject, $result, $name, $optionRow)
    {
        foreach (array_merge($this->map, $this->rowMap) as $to => $from) {
            $result[$to] = $this->getArrayField($optionRow, $from);
        }

        return $result;
    }

    //phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
    public function aroundGetOptionData(
        ImportProductOption $subject,
        callable $proceed,
        array $rowData,
        $productId,
        $optionId,
        $type
    ) {
        $option = $proceed($rowData, $productId, $optionId, $type);
        foreach ($this->map as $from => $to) {
            $option[$to] = $this->getArrayField($rowData, $from);
        }
        return $option;
    }

    private function getArrayField(array $array, $field)
    {
        return array_key_exists($field, $array) ? $array[$field] : null;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function afterGetSpecificTypeData(
        ImportProductOption $subject,
        $result,
        array $rowData,
        $optionTypeId,
        $defaultStore = true
    ) {
        if ($defaultStore) {
            foreach ($this->rowMap as $from => $to) {
                $value = $this->getArrayField($rowData, $from);
                if ($value && $from === self::COLUMN_ROW_IMAGE) {
                    $value = $this->file->import(
                        $rowData,
                        self::COLUMN_ROW_IMAGE,
                        $this->imageHelper->getImportPath(),
                        $this->imageHelper->getBasePath()
                    );
                }
                $result['value'][$to] = $value;
            }
        }
        return $result;
    }
}
