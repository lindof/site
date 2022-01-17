<?php

namespace Biztech\Deliverydate\Block\Email;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Notification extends Template
{

    // protected $_orders;
    protected $_template = 'Biztech_Deliverydate::email/email_notification.phtml';

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}
