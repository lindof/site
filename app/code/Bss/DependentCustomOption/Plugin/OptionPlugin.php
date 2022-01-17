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

use Magento\Catalog\Model\Product\Option;

/**
 * Class OptionPlugin
 *
 * @package Bss\DependentCustomOption\Plugin
 */
class OptionPlugin
{
    /**
     * @var \Bss\DependentCustomOption\Model\DependOptionFactory
     */
    private $dependOptionFactory;

    /**
     * @var \Bss\DependentCustomOption\Helper\ModuleConfig
     */
    private $moduleConfig;

    /**
     * OptionPlugin constructor.
     * @param \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory
     * @param \Bss\DependentCustomOption\Helper\ModuleConfig $moduleConfig
     */
    public function __construct(
        \Bss\DependentCustomOption\Model\DependOptionFactory $dependOptionFactory,
        \Bss\DependentCustomOption\Helper\ModuleConfig $moduleConfig
    ) {
        $this->dependOptionFactory = $dependOptionFactory;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * AfterSave
     *
     * @param Option $subject
     * @param Option $result
     * @return Option
     */
    public function afterSave(
        Option $subject,
        $result
    ) {
        $dependOptionModel = $this->dependOptionFactory->create()->load($subject->getDependentId());
        $dependOptionModel
        ->setDependentId($subject->getDependentId())
        ->setOptionId($result->getOptionId());
        $dependOptionModel->save();
        return $result;
    }

    /**
     * AfterGetIsRequire
     *
     * @param Option $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetIsRequire(
        Option $subject,
        $result
    ) {
        if ($this->moduleConfig->isModuleEnable()) {
            return false;
        } else {
            return $result;
        }
    }

    /**
     * AroundGetData
     *
     * @param Option $subject
     * @param callable $proceed
     * @param string $key
     * @param string $index
     * @return mixed
     */
    public function aroundGetData(
        Option $subject,
        $proceed,
        $key = '',
        $index = null
    ) {
        $result = $proceed($key, $index);
        if ($key === '') {
            if (isset($result['option_id'])) {
                $dependData = $this->dependOptionFactory->create()->getCollection()
                ->getItemByColumnValue('option_id', $result['option_id']);
                if ($dependData) {
                    $result['dependent_id'] = $dependData->getData('dependent_id');
                }
            } else {
                $result['dependent_id'] = null;
            }
            return $result;
        }
        if (($key === 'dependent_id') && !$subject->hasData('dependent_id')) {
            $dependData = $this->dependOptionFactory->create()->getCollection()
            ->getItemByColumnValue('option_id', $subject->getData('option_id'));
            if ($dependData) {
                $dependData = $dependData->getData('dependent_id');
            }
            return $dependData;
        }
        return $result;
    }
}
