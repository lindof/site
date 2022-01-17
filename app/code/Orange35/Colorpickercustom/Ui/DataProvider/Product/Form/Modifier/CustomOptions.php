<?php

namespace Orange35\Colorpickercustom\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions as OriginalCustomOptions;
use Magento\GroupedProduct\Ui\DataProvider\Product\Form\Modifier\CustomOptions as GroupedOptions;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Orange35\Colorpickercustom\Helper\ConfigHelper;
use Orange35\Colorpickercustom\Helper\Image as ImageHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Orange35\Colorpickercustom\Model\Config\Source\CategoryMode;
use Orange35\Colorpickercustom\Model\Config\Source\TooltipType;

class CustomOptions extends OriginalCustomOptions
{
    const FIELD_IMAGE_UPLOAD_NAME   = 'image';
    const FIELD_IMAGE_PREVIEW_NAME  = 'image_preview';
    const FIELD_IMAGE_DELETE_NAME   = 'delete_image';
    const FIELD_COLOR_NAME          = 'color';
    const FIELD_IS_COLORPICKER_NAME = 'is_colorpicker';
    const FIELD_SHOW_IN_LIST        = 'show_in_list';
    const FIELD_TOOLTIP_NAME        = 'tooltip';
    const FIELD_WIDTH_NAME          = 'swatch_width';
    const FIELD_HEIGHT_NAME         = 'swatch_height';

    private $imageHelper;
    private $scopeConfig;
    private $configHelper;
    private $tooltipType;

    /**
     * CustomOptions constructor.
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $productOptionsConfig
     * @param ProductOptionsPrice $productOptionsPrice
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     * @param ImageHelper $helper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ConfigInterface $productOptionsConfig,
        ProductOptionsPrice $productOptionsPrice,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        ImageHelper $helper,
        ConfigHelper $configHelper,
        ScopeConfigInterface $scopeConfig,
        TooltipType $tooltipType
    ) {
        $this->configHelper = $configHelper;
        $this->imageHelper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->tooltipType = $tooltipType;
        parent::__construct(
            $locator,
            $storeManager,
            $productOptionsConfig,
            $productOptionsPrice,
            $urlBuilder,
            $arrayManager
        );
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        if ($product->getTypeId() === GroupedOptions::PRODUCT_TYPE_GROUPED) {
            return $data;
        }

        $options = [];
        $productOptions = $product->getOptions() ?: [];

        /** @var \Magento\Catalog\Model\Product\Option $option */

        foreach ($productOptions as $index => $option) {
            $values = $option->getValues() ?: [];

            /** @var \Magento\Catalog\Model\Product\Option $value */
            foreach ($values as $value) {
                $valueData = $value->getData();
                if (null !== $value->getImage()) {
                    $imageUrl = $this->imageHelper->getImageUrl($value->getImage());
                    $valueData = array_replace_recursive(
                        $value->getData(),
                        [self::FIELD_IMAGE_UPLOAD_NAME => [0 => [
                            'name' => $value->getImage(),
                            'url'  => $imageUrl,
                        ]]]
                    );
                }
                $options[$index][static::GRID_TYPE_SELECT_NAME][] = $valueData;
            }
        }

        return array_replace_recursive(
            $data,
            [
                $this->locator->getProduct()->getId() => [
                    static::DATA_SOURCE_DEFAULT => [
                        static::FIELD_ENABLE      => 1,
                        static::GRID_OPTIONS_NAME => $options,
                    ],
                ],
            ]
        );
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getProduct()->getTypeId() === GroupedOptions::PRODUCT_TYPE_GROUPED) {
            return $meta;
        }

        $this->meta = $meta;
        $this->createCustomOptionsPanel();
        $this->setColumnOrder('is_delete', 1000);
        return $this->meta;
    }

    /**
     * @param $name
     * @param $order
     * return void
     */
    private function setColumnOrder($name, $order)
    {
        $columns = &$this->meta[static::GROUP_CUSTOM_OPTIONS_NAME]['children'][static::GRID_OPTIONS_NAME]
        ['children']['record']['children'][static::CONTAINER_OPTION]['children'][static::GRID_TYPE_SELECT_NAME]
        ['children']['record']['children'];
        $columns[$name]['arguments']['data']['config']['sortOrder'] = $order;
        $this->sortColumns($columns);
    }

    private function sortColumns(array &$columns)
    {
        uksort($columns, function ($key1, $key2) use ($columns) {
            return $columns[$key1]['arguments']['data']['config']['sortOrder'] -
                $columns[$key2]['arguments']['data']['config']['sortOrder'];
        });
    }

    protected function createCustomOptionsPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_CUSTOM_OPTIONS_NAME => [
                    'children' => [
                        static::GRID_OPTIONS_NAME => $this->getOptionsGridConfig(30),
                    ],
                ],
            ]
        );

        return $this;
    }

    protected function getOptionsGridConfig($sortOrder)
    {
        return [
            'children' => [
                'record' => [
                    'children' => [
                        static::CONTAINER_OPTION => [
                            'children' => [
                                static::CONTAINER_COMMON_NAME => $this->getCommonContainerConfig(10),
                                static::GRID_TYPE_SELECT_NAME => $this->getSelectTypeGridConfig(30),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getSelectTypeGridConfig($sortOrder)
    {
        $imagesGrid = [
            'children' => [
                'record' => [
                    'children' => [
                        static::FIELD_IMAGE_UPLOAD_NAME => $this->getImgConfig(70),
                        static::FIELD_COLOR_NAME        => $this->getColorpickerConfig(80),
                    ],
                ],
            ],
        ];

        return $imagesGrid;
    }

    protected function getCommonContainerConfig($sortOrder)
    {
        $config = [
            static::FIELD_IS_COLORPICKER_NAME => $this->getIsColorpickerConfig(50),
        ];
        if (CategoryMode::CUSTOM === $this->configHelper->getCategoryMode()) {
            $config[static::FIELD_SHOW_IN_LIST] = $this->getShowInListConfig(51);
        }
        $config = array_merge($config, [
            static::FIELD_TOOLTIP_NAME => $this->getTooltipConfig(60),
            static::FIELD_WIDTH_NAME   => $this->getWidthConfig(70),
            static::FIELD_HEIGHT_NAME  => $this->getHeightConfig(80),
        ]);
        return [
            'children' => $config,
        ];
    }

    private function getIsColorpickerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Colorpicker'),
                        'componentType' => Field::NAME,
                        'formElement'   => Checkbox::NAME,
                        'dataScope'     => 'is_colorpicker',
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                        'value'         => '0',
                        'valueMap'      => [
                            'true'  => '1',
                            'false' => '0',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getShowInListConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Show in list'),
                        'componentType' => Field::NAME,
                        'component'     => 'Orange35_Colorpickercustom/js/components/single-checkbox',
                        'formElement'   => Checkbox::NAME,
                        'dataScope'     => 'show_in_list',
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                        'value'         => '0',
                        'valueMap'      => [
                            'true'  => '1',
                            'false' => '0',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getTooltipConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Tooltip Type'),
                        'componentType' => Field::NAME,
                        'component'     => 'Orange35_Colorpickercustom/js/components/tooltip',
                        'formElement'   => Select::NAME,
                        'dataScope'     => 'tooltip',
                        'dataType'      => Number::NAME,
                        'sortOrder'     => $sortOrder,
                        'options'       => $this->tooltipType->toOptionArray(),
                    ],
                ],
            ],
        ];
    }

    private function getWidthConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Swatch Width'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'component'     => 'Orange35_Colorpickercustom/js/components/input',
                        'dataScope'     => 'swatch_width',
                        'dataType'      => Number::NAME,
                        'sortOrder'     => $sortOrder,
                        'placeholder'   => $this->configHelper->getSwatchFinalWidth() . ' by default',
                        'validation'    => [
                            'validate-greater-than-zero' => true,
                            'validate-number'            => true,
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getHeightConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Swatch Height'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'component'     => 'Orange35_Colorpickercustom/js/components/input',
                        'dataScope'     => 'swatch_height',
                        'dataType'      => Number::NAME,
                        'sortOrder'     => $sortOrder,
                        'placeholder'   => $this->configHelper->getSwatchFinalHeight() . ' by default',
                        'validation'    => [
                            'validate-greater-than-zero' => true,
                            'validate-number'            => true,
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getImgConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'           => __('Swatch Image'),
                        'componentType'   => 'field',
                        'formElement'     => 'fileUploader',
                        'dataScope'       => static::FIELD_IMAGE_UPLOAD_NAME,
                        'dataType'        => 'file',
                        'fileInputName'   => 'image',
                        'sortOrder'       => $sortOrder,
                        'isMultipleFiles' => false,
                        'component'       => 'Orange35_Colorpickercustom/js/components/file-uploader',
                        'template'        => 'Orange35_Colorpickercustom/components/uploader',
                        'previewTmpl'     => 'Orange35_Colorpickercustom/image-preview',
                        'uploaderConfig'  => [
                            'url'      => 'o35_colorpicker_custom/image/upload/field/image',
                            'imageUrl' => $this->imageHelper->getBaseUrl() . $this->imageHelper->getBasePath(),
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getColorpickerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Color'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => 'color',
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                        'component'     => 'Orange35_Colorpickercustom/js/components/colorpicker',
                    ],
                ],
            ],
        ];
    }
}
