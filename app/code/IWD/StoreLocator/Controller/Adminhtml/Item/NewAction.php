<?php

namespace IWD\StoreLocator\Controller\Adminhtml\Item;

/**
 * Class NewAction
 * @package IWD\StoreLocator\Controller\Adminhtml\Item
 */
class NewAction extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'IWD_StoreLocator::Item';

    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    private $resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
