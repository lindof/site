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

namespace Biztech\Deliverydate\Controller\Index;

use Biztech\Deliverydate\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Result\PageFactory;

class Deliveryview extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $_bizHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $bizHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $bizHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_bizHelper = $bizHelper;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        // $this->_bizHelper->sendEmailNotification();
        $objectManager = ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        $customerid = $customerSession->getCustomer()->getEntityId();

        if ($customerSession->isLoggedIn() && $customerid != '') {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('My Delivery Dates'));

            return $resultPage;
        }

        $this->_redirect('/*/*');
        return $this;
    }
}
