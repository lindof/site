<?php

namespace IWD\StoreLocator\Controller\Adminhtml\Item;

/**
 * Class Index
 * @package IWD\StoreLocator\Controller\Adminhtml\Item
 */
class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'IWD_StoreLocator::Item';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('IWD_StoreLocator::storelocator_item');
        $resultPage->getConfig()->getTitle()->prepend(__('Store Locator Items'));

        return $resultPage;
    }
}
