<?php

namespace Knowband\Spinandwin\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $sp_resultRawFactory;
    protected $sp_request;
    protected $sp_helper;
    protected $sp_scopeConfig;
    protected $inlineTranslation;
    protected $sp_transportBuilder;
    protected $sp_emailFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Knowband\Spinandwin\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultRawFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Knowband\Spinandwin\Model\EmailFactory $emailFactory
    ) {
        parent::__construct($context);
        $this->sp_request = $request;
        $this->sp_scopeConfig = $scopeConfig;
        $this->sp_helper = $helper;
        $this->sp_resultRawFactory = $resultRawFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->sp_transportBuilder = $transportBuilder;
        $this->sp_emailFactory = $emailFactory;
    }

    public function execute() {
//        $emailModel = $this->sp_emailFactory->create();
//        $collection = $emailModel->getCollection();
//        foreach($collection as $item){
//            p($item->getData());
//        }
        $resultPage = $this->sp_resultRawFactory->create();
        
        return $resultPage;
    }

}
