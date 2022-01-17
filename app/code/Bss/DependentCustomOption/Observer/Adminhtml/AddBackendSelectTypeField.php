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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;

/**
 * Class AddBackendSelectTypeField
 * @package Bss\DependentCustomOption\Observer\Adminhtml
 */
class AddBackendSelectTypeField implements ObserverInterface
{
    const BSS_DEPEND_ID = 'dependent_id';

    const BSS_DEPEND_FIELD = 'depend_value';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * AddBackendField constructor.
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $optionQtyField = [
            15 => ['index' => static::BSS_DEPEND_ID, 'field' => $this->getDependentIdField(15)],
            210 => ['index' => static::BSS_DEPEND_FIELD, 'field' => $this->getDependentConfigField(210)]
        ];
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
                        'label' => __('ID'),
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
     * GetDependentConfigField
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getDependentConfigField($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Input::NAME,
                        'componentType' => Field::NAME,
                        'label' => __("Dependent Options' IDs"),
                        'component' => 'Bss_DependentCustomOption/js/depend-config-control',
                        'elementTmpl' => 'Bss_DependentCustomOption/depend-control',
                        'dataScope' => static::BSS_DEPEND_FIELD,
                        'dataType' => Number::NAME,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }
}
