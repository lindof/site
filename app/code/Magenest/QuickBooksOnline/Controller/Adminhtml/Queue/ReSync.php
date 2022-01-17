<?php
/**
 * Copyright © 2017 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\QuickBooksOnline\Controller\Adminhtml\Queue;

use Magenest\QuickBooksOnline\Controller\Adminhtml\AbstractQueue;
use Magenest\QuickBooksOnline\Helper\ReSynchronization;
use Magenest\QuickBooksOnline\Model\QueueFactory;
use Magenest\QuickBooksOnline\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magento\Backend\App\Action\Context;
use Magenest\QuickBooksOnline\Model\Config;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class Sync
 * @package Magenest\QuickBooksOnline\Controller\Adminhtml\Queue
 */
class ReSync extends AbstractQueue
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Sync constructor.
     * @param Context $context
     * @param QueueFactory $queueFactory
     * @param Filter $filter
     * @param QueueCollectionFactory $queueCollection
     * @param Config $config
     */
//    public function __construct(
//        Context $context,
//        QueueFactory $queueFactory,
//        Config $config
//    ) {
//        parent::__construct($context, $queueFactory);
//        $this->config = $config;
//    }
    public function __construct(Context $context, QueueFactory $queueFactory, Filter $filter, QueueCollectionFactory $queueCollection,Config $config)
    {
        parent::__construct($context, $queueFactory, $filter, $queueCollection);
        $this->config = $config;
    }

    /**
     * Execute the action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $connect = $this->config->getConnected();
        if ($connect && $connect == 1) {
            /** @var ReSynchronization $cronjob */
            $cronJob = $this->_objectManager->create(ReSynchronization::class);
            $cronJob->execute();
        } else {
            $this->messageManager->addErrorMessage(__('You\'re not connected to QuickBooks Online. Please go to Configuration to finish the connection.'));
        }

        return $this->_redirect('*/log/');
    }
}
