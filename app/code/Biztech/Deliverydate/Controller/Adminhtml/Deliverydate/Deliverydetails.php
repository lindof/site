<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Biztech\Deliverydate\Controller\Adminhtml\Deliverydate;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;

class Deliverydetails extends Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $_layoutFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page.
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $layout = $this->_layoutFactory->create();
        $output = $layout->createBlock('Biztech\Deliverydate\Block\Adminhtml\Dashboard\Deliverydetails')->toHtml();
        $this->getResponse()->appendBody($output);
    }
}
