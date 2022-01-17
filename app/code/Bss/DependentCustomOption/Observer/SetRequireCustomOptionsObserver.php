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
namespace Bss\DependentCustomOption\Observer;

use Bss\DependentCustomOption\Model\ResourceModel\RemoveRequired;
use Bss\DependentCustomOption\Model\ResourceModel\AddRequired;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Bss\DependentCustomOption\Helper\ModuleConfig;

/**
 * Class SetRequireCustomOptionsObserver
 *
 * @package Bss\DependentCustomOption\Observer
 */
class SetRequireCustomOptionsObserver implements ObserverInterface
{
    const CONFIG_ENABLE_MODULE = 'dependent_co/general/enable';
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $bssHelper;

    /**
     * @var RemoveRequired
     */
    protected $removeRequired;

    /**
     * @var addRequired
     */
    protected $addRequired;

    /**
     * SetRequireCustomOptionsObserver constructor.
     * @param ModuleConfig $bssHelper
     * @param AddRequired $addRequired
     * @param RemoveRequired $removeRequired
     */
    public function __construct(
        ModuleConfig $bssHelper,
        AddRequired $addRequired,
        RemoveRequired $removeRequired
    ) {
        $this->bssHelper = $bssHelper;
        $this->addRequired = $addRequired;
        $this->removeRequired = $removeRequired;
    }

    /**
     * Process custom option 'is_require'
     *
     * @param EventObserver $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(EventObserver $observer)
    {
        $changedPaths = $observer->getChangedPaths();
        if (in_array(self::CONFIG_ENABLE_MODULE, $changedPaths)) {
            if ($this->bssHelper->isModuleEnable() == 1) {
                $this->removeRequired->getValue();
            } else {
                $this->addRequired->getValue();
            }
        }
    }
}
