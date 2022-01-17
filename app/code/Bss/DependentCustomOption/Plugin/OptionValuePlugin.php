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
namespace Bss\DependentCustomOption\Plugin;

use Magento\Catalog\Model\Product\Option\Value;

/**
 * Class OptionValuePlugin
 *
 * @package Bss\DependentCustomOption\Plugin
 */
class OptionValuePlugin
{
    /**
     * @var \Bss\DependentCustomOption\Model\DependOptionFactory
     */
    private $dependOptionFactory;

    /**
     * OptionValuePlugin constructor.
     * @param \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory
     */
    public function __construct(
        \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory
    ) {
        $this->dependOptionFactory = $dependOptionFactory;
    }

    /**
     * AfterSave
     *
     * @param Value $subject
     * @param Value $result
     * @return Value
     */
    public function afterSave(
        Value $subject,
        $result
    ) {
        $dependOptionModel = $this->dependOptionFactory->create()->load($subject->getDependentId());
        $dependOptionModel
        ->setDependentId($subject->getDependentId())
        ->setDependValue($subject->getDependValue())
        ->setOptionTypeId($result->getOptionTypeId());
        $dependOptionModel->save();
        return $result;
    }

    /**
     * AroundGetData
     *
     * @param Value $subject
     * @param callable $proceed
     * @param string $key
     * @param string $index
     * @return mixed
     */
    public function aroundGetData(
        Value $subject,
        $proceed,
        $key = '',
        $index = null
    ) {
        $result = $proceed($key, $index);
        if ($key === '') {
            if (isset($result['option_type_id'])) {
                $dependData = $this->dependOptionFactory->create()->getCollection()
                ->getItemByColumnValue('option_type_id', $result['option_type_id']);
                if ($dependData) {
                    $result = array_merge($dependData->getData(), $result);
                }
            } else {
                $result['dependent_id'] = null;
                $result['depend_value'] = null;
            }
            return $result;
        }
        if (($key === 'dependent_id' || $key === 'depend_value') && !$subject->hasData($key)) {
            $dependData = $this->dependOptionFactory->create()->getCollection()
            ->getItemByColumnValue('option_type_id', $subject->getData('option_type_id'));
            if ($dependData) {
                $dependData = $dependData->getData($key);
            }
            return $dependData;
        }
        return $result;
    }
}
