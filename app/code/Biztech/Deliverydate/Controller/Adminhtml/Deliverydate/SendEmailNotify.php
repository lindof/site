<?php

namespace Biztech\Deliverydate\Controller\Adminhtml\Deliverydate;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;

class SendEmailNotify extends Action
{

    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
    }
}
