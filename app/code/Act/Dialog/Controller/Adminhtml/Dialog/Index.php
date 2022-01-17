<?php
namespace Act\Dialog\Controller\Adminhtml\Dialog;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 */
class Index extends Action implements HttpGetActionInterface
{
    const MENU_ID = 'Act_Dialog::dialogconfig';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Cms\Model\BlockRepository
     */
    protected $blockRepository;    

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Cms\Model\BlockRepository $blockRepository
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->blockRepository = $blockRepository;
    }

    /**
     * Redirect Page to Dialog CMS Block
     *
     * @return Page
     */
    public function execute()
    {
      $resultPage = $this->resultPageFactory->create();
      $resultPage->setActiveMenu(static::MENU_ID);
      $resultPage->getConfig()->getTitle()->prepend(__('Configure'));

      /** @var \Magento\Cms\Model\Block $block */
      $block = $this->blockRepository->getById('dialog_box');

      $this->_redirect('cms/block/edit/block_id/'.$block->getId());

      return $resultPage;
    }
}
