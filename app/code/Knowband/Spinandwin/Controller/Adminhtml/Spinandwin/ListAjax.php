<?php

namespace Knowband\Spinandwin\Controller\Adminhtml\Spinandwin;
use Magento\Framework\Controller\ResultFactory;

class ListAjax extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        parent::__construct($context);
        $this->sp_request = $request;
        $this->sp_scopeConfig = $scopeConfig;
        $this->sp_helper = $helper;
        $this->sp_resultRawFactory = $resultRawFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->sp_transportBuilder = $transportBuilder;
        $this->assetRepo = $assetRepo;
    }

    public function execute() {
        $response_array = array();
        if ($this->getRequest()->isPost()) {
            $post_data = $this->getRequest()->getPost();
            $api_key = $post_data['api_key'];
            $option = $post_data['method'];
            $response_array = array();
            if ($option == 'mailchimp') {
                $response_array = $this->sp_helper->mailchimpGetLists($api_key);
            } else if ($option == 'klaviyo') {
                $response_array = $this->sp_helper->klaviyoGetLists($api_key);
            }else if ($option == 'constant_contact') {
                $access_token = $post_data['token'];
                $response_array = $this->sp_helper->constantContactGetLists($api_key, $access_token);
            }
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response_array);
        return $resultJson;
    }
}
