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
namespace Bss\DependentCustomOption\Controller\Adminhtml\Json;

use Bss\DependentCustomOption\Model\DependOptionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Generator
 *
 * @package Bss\DependentCustomOption\Controller\Adminhtml\Json
 */
class Generator extends Action
{
    /**
     * @var DependOptionFactory
     */
    protected $dependOptionFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Generator constructor.
     * @param Context $context
     * @param DependOptionFactory $dependOptionFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        DependOptionFactory $dependOptionFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->dependOptionFactory = $dependOptionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $newModel = $this->dependOptionFactory->create()->setDependValue('')->save();
            return $resultJson->setData($newModel->getDependentId());
        } else {
            return $resultJson->setData([]);
        }
    }
}
