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

use Bss\DependentCustomOption\Model\DependOptionFactory;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ReplaceDependData
 *
 * @package Bss\DependentCustomOption\Observer
 */
class ReplaceDependData implements ObserverInterface
{
    /**
     * @var DependOptionFactory
     */
    private $dependOptionFactory;

    /**
     * ReplaceDependData constructor.
     * @param DependOptionFactory $dependOptionFactory
     */
    public function __construct(
        DependOptionFactory $dependOptionFactory
    ) {
        $this->dependOptionFactory = $dependOptionFactory;
    }

    /**
     * Execute
     *
     * @param EventObserver $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(EventObserver $observer)
    {
        $emptyIds = $this->dependOptionFactory->create()->getCollection()
        ->addFieldToFilter('option_type_id', ['null' => true])->addFieldToFilter('option_id', ['null' => true]);
        $emptyIds->walk('delete');
    }
}
