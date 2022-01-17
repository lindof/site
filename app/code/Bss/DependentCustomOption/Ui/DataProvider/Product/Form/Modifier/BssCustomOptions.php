<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_DependentCustomOption
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\DependentCustomOption\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;

/**
 * Class BssCustomOptions
 *
 * @package Bss\DependentCustomOption\Ui\DataProvider\Product\Form\Modifier
 */
class BssCustomOptions extends CustomOptions
{
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * BssCustomOptions constructor.
     *
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $productOptionsConfig
     * @param ProductOptionsPrice $productOptionsPrice
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     * @param DataObjectFactory $dataObjectFactory
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ConfigInterface $productOptionsConfig,
        ProductOptionsPrice $productOptionsPrice,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        DataObjectFactory $dataObjectFactory,
        ManagerInterface $eventManager
    ) {
        parent::__construct(
            $locator,
            $storeManager,
            $productOptionsConfig,
            $productOptionsPrice,
            $urlBuilder,
            $arrayManager
        );
        $this->dataObjectFactory = $dataObjectFactory;
        $this->eventManager = $eventManager;
    }

    /**
     * GetCommonContainerConfig
     *
     * @param int $sortOrder
     * @return array
     */
    public function getCommonContainerConfig($sortOrder)
    {
        $commonContainer = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'breakLine' => false,
                        'showLabel' => false,
                        'additionalClasses' => 'admin__field-group-columns admin__control-group-equal',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => $this->getCoapCommonChildren()
        ];

        if ($this->locator->getProduct()->getStoreId()) {
            $useDefaultConfig = [
                'service' => [
                    'template' => 'Magento_Catalog/form/element/helper/custom-option-service',
                ]
            ];
            $titlePath = $this->arrayManager->findPath(static::FIELD_TITLE_NAME, $commonContainer, null)
                . static::META_CONFIG_PATH;
            $commonContainer = $this->arrayManager->merge($titlePath, $commonContainer, $useDefaultConfig);
        }

        return $commonContainer;
    }

    /**
     * GetCoapCommonChildren
     *
     * @return array
     */
    public function getCoapCommonChildren()
    {
        $customTitle = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Option Title'),
                        'component' => 'Magento_Catalog/component/static-type-input',
                        'valueUpdate' => 'input',
                        'imports' => [
                            'optionId' => '${ $.provider }:${ $.parentScope }.option_id'
                        ]
                    ],
                ],
            ],
        ];
        $childs = [
            10 => ['index' => static::FIELD_OPTION_ID, 'field' => $this->getOptionIdFieldConfig(10)],
            20 => ['index' => static::FIELD_TITLE_NAME, 'field' => $this->getTitleFieldConfig(20, $customTitle)],
            30 => ['index' => static::FIELD_TYPE_NAME, 'field' => $this->getTypeFieldConfig(30)],
            40 => ['index' => static::FIELD_IS_REQUIRE_NAME, 'field' => $this->getIsRequireFieldConfig(40)]
        ];

        $childObject = $this->dataObjectFactory->create()->addData($childs);

        $this->eventManager->dispatch(
            'bss_custom_options_common_container_add_child_before',
            ['child' => $childObject]
        );
        $sortedChild = $childObject->getData();
        ksort($sortedChild);
        $result = [];
        foreach ($sortedChild as $child) {
            $result[$child['index']] = $child['field'];
        }
        return $result;
    }

    /**
     * GetSelectTypeGridConfig
     *
     * @param int $sortOrder
     * @return array
     */
    public function getSelectTypeGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Value'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => static::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => $this->getSelectTypeGridChildConfig()
                ]
            ]
        ];
    }

    /**
     * GetSelectTypeGridChildConfig
     *
     * @return array
     */
    public function getSelectTypeGridChildConfig()
    {
        $childs = [
            50 => ['index' => static::FIELD_TITLE_NAME, 'field' => $this->getTitleFieldConfig(50)],
            100 => ['index' => static::FIELD_PRICE_NAME, 'field' => $this->getPriceFieldConfig(100)],
            150 => [
                'index' => static::FIELD_PRICE_TYPE_NAME,
                'field' => $this->getPriceTypeFieldConfig(150, ['fit' => true])
            ],
            200 => ['index' => static::FIELD_SKU_NAME, 'field' => $this->getSkuFieldConfig(200)],
            250 => ['index' => static::FIELD_SORT_ORDER_NAME, 'field' => $this->getPositionFieldConfig(250)],
            300 => ['index' => static::FIELD_IS_DELETE, 'field' => $this->getIsDeleteFieldConfig(300)]
        ];

        $childObject = $this->dataObjectFactory->create()->addData($childs);

        $this->eventManager->dispatch(
            'bss_custom_options_select_type_add_child_before',
            ['child' => $childObject]
        );
        $sortedChild = $childObject->getData();
        ksort($sortedChild);
        $result = [];
        foreach ($sortedChild as $child) {
            $result[$child['index']] = $child['field'];
        }
        return $result;
    }
}
