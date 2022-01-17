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
namespace Bss\DependentCustomOption\Observer\Adminhtml;

use Bss\DependentCustomOption\Helper\ModuleConfig;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;

/**
 * Class AddBackendCommonField
 *
 * @package Bss\DependentCustomOption\Observer\Adminhtml
 */
class AddBackendCommonField implements ObserverInterface
{
    const BSS_DEPEND_ID = 'dependent_id';

    const BSS_DCO_REQUIRE = 'bss_dco_require';

    const IS_REQUIRE = 'is_require';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * AddBackendCommonField constructor.
     * @param UrlInterface $urlBuilder
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ModuleConfig $moduleConfig
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isModuleEnable()) {
            $optionQtyField = [
                40 => ['index' => static::IS_REQUIRE, 'field' => $this->getHidenRequireField(40)],
                50 => ['index' => static::BSS_DCO_REQUIRE, 'field' => $this->getDependentRequireField(50)],
                60 => ['index' => static::BSS_DEPEND_ID, 'field' => $this->getDependentIdField(60)]
            ];
        } else {
            $optionQtyField = [
                60 => ['index' => static::BSS_DEPEND_ID, 'field' => $this->getDependentIdField(60)]
            ];
        }
        $observer->getChild()->addData($optionQtyField);
    }

    /**
     * GetDependentIdField
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getDependentIdField($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Input::NAME,
                        'componentType' => Field::NAME,
                        'label' => __('Option ID'),
                        'component' => 'Bss_DependentCustomOption/js/depend-id-control',
                        'elementTmpl' => 'Bss_DependentCustomOption/depend-id',
                        'dataScope' => static::BSS_DEPEND_ID,
                        'dataType' => Number::NAME,
                        'sortOrder' => $sortOrder,
                        'jsonUrl' => $this->urlBuilder->getUrl('bss_dco/json/generator')
                    ],
                ],
            ],
        ];
    }

    /**
     * GetDependentRequireField
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getDependentRequireField($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Required'),
                        'componentType' => Field::NAME,
                        'formElement' => Checkbox::NAME,
                        'dataScope' => static::BSS_DCO_REQUIRE,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'value' => '0',
                        'valueMap' => [
                            'true' => '1',
                            'false' => '0'
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * GetHidenRequireField
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getHidenRequireField($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Hidden::NAME,
                        'componentType' => Field::NAME,
                        'dataScope' => static::IS_REQUIRE,
                        'visible' => false,
                        'value' => '0',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }
}
