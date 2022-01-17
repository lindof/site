<?php

namespace Knowband\Spinandwin\Controller\Adminhtml\Spinandwin;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\LayoutFactory;

class Spinandwinuserlistajax extends \Magento\Framework\App\Action\Action
{
    protected $sp_resultRawFactory;
    protected $sp_request;
    protected $sp_helper;
    protected $sp_scopeConfig;
    protected $inlineTranslation;
    protected $sp_transportBuilder;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Knowband\Spinandwin\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultRawFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        LayoutFactory $viewLayoutFactory
    ) {
        parent::__construct($context);
        $this->sp_request = $request;
        $this->sp_scopeConfig = $scopeConfig;
        $this->sp_helper = $helper;
        $this->sp_resultRawFactory = $resultRawFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->sp_transportBuilder = $transportBuilder;
        $this->assetRepo = $assetRepo;
        $this->_viewLayoutFactory = $viewLayoutFactory;
    }

    public function execute() {
        //Grid Class: Knowband\Rma\Adminhtml\Tab\ArchivedRequests
        $block = $this->_viewLayoutFactory->create()->createBlock('Knowband\Spinandwin\Block\Adminhtml\Tab\SpinUserlist');
        $this->getResponse()->appendBody($block->toHtml());
    }
    
}
