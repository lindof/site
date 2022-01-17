<?php

namespace Biztech\Deliverydate\Cron;

use Biztech\Deliverydate\Helper\Data;
use Magento\Framework\View\Element\BlockFactory;
use Psr\Log\LoggerInterface;

class Notify
{

    protected $_logger;
    protected $_bizHelper;
    protected $_blockFactory;

    /**
     * @param LoggerInterface $logger
     * @param Data $bizHelper
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Data $bizHelper,
        BlockFactory $blockFactory
    ) {
        $this->_logger = $logger;
        $this->_bizHelper = $bizHelper;
        $this->_blockFactory = $blockFactory;
    }

    public function execute()
    {
        $this->_bizHelper->sendEmailNotification();
        return $this;
    }
}
