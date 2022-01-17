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
namespace Bss\DependentCustomOption\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class MultipleParent
 *
 * @package Bss\DependentCustomOption\Model\Config\Source
 */
class MultipleParent implements ArrayInterface
{
    /**
     * ToOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'atleast_one', 'label' => __('When at least one parent value is selected')],
            ['value' => 'all', 'label' => __('When all parent values are selected')]
        ];
    }
}
