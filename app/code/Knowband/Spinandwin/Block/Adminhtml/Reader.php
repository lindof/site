<?php
namespace Knowband\Spinandwin\Block\Adminhtml;
class Reader extends \Magento\Backend\Block\Template
{
    private $storeManager;
    private $urlInterface;
    private $request;
    private $scopeConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $context->getStoreManager();
        $this->urlInterface = $context->getUrlBuilder();
        $this->scopeConfig = $context->getScopeConfig();
        $this->request = $request;
    }
}
